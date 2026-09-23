<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Store;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\StockTransactionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = StockRequest::with(['store', 'requester', 'approver', 'items.stockItem']);

        // Staff only see their own requests unless admin/pelulus/pegawai_stor
        if (!$user->hasRole(['admin', 'pegawai_pelulus', 'pegawai_stor', 'ketua_jabatan'])) {
            $query->where('requester_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
        }

        $requests = $query->latest('id')->paginate(15)->withQueryString();

        return view('requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $formType = $request->query('type', 'KEW.PS-8'); // default KEW.PS-8
        $stores = Store::where('is_active', true)->get();
        $stockItems = StockItem::with(['category', 'uom'])
            ->where('status', 'AVAILABLE')
            ->where('current_quantity', '>', 0)
            ->orderBy('description')
            ->get();

        $categories = StockCategory::orderBy('name')->get();

        $stockItemsJson = $stockItems->map(function ($s) {
            return [
                'id' => $s->id,
                'code' => $s->stock_code ?? $s->item_code,
                'description' => $s->description,
                'category' => $s->category->name ?? 'Umum',
                'category_id' => $s->category_id,
                'quantity' => (float)$s->current_quantity,
                'uom' => $s->uom->code ?? 'UNIT',
            ];
        });

        return view('requests.create', compact('formType', 'stores', 'stockItems', 'categories', 'stockItemsJson'));
    }

    public function store(Request $request)
    {
        $formType = $request->input('form_type', 'KEW.PS-8');

        $validated = $request->validate([
            'form_type' => 'required|in:KEW.PS-8,KEW.PS-7',
            'store_id' => 'required|exists:stores,id',
            'requesting_store_id' => 'nullable|required_if:form_type,KEW.PS-7|exists:stores,id',
            'department' => 'required|string|max:100',
            'purpose' => 'required|string|max:255',
            'priority' => 'required|in:NORMAL,URGENT',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.requested_quantity' => 'required|numeric|min:1',
            'items.*.remarks' => 'nullable|string',
        ]);

        $prefix = ($formType === 'KEW.PS-7') ? 'PS7' : 'PS8';
        $requestNumber = DocumentNumberService::generate($prefix);

        DB::transaction(function () use ($validated, $requestNumber, $formType) {
            $stockReq = StockRequest::create([
                'request_number' => $requestNumber,
                'form_type' => $formType,
                'store_id' => $validated['store_id'],
                'requesting_store_id' => $validated['requesting_store_id'] ?? null,
                'requester_id' => Auth::id(),
                'department' => $validated['department'],
                'purpose' => $validated['purpose'],
                'priority' => $validated['priority'],
                'status' => 'SUBMITTED',
            ]);

            foreach ($validated['items'] as $itemData) {
                StockRequestItem::create([
                    'stock_request_id' => $stockReq->id,
                    'stock_item_id' => $itemData['stock_item_id'],
                    'requested_quantity' => $itemData['requested_quantity'],
                    'approved_quantity' => 0,
                    'issued_quantity' => 0,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            AuditLogService::log(
                'SUBMIT_REQUEST',
                'StockRequest',
                (string) $stockReq->id,
                null,
                ['request_number' => $requestNumber, 'form_type' => $formType],
                'Permohonan stok ' . $formType . ' berjaya dihantar untuk kelulusan.'
            );
        });

        return redirect()->route('requests.index')->with('success', "Permohonan stok {$requestNumber} berjaya dihantar.");
    }

    public function show(StockRequest $stockRequest)
    {
        $stockRequest->load(['store', 'requester', 'approver', 'issuer', 'recipient', 'items.stockItem.uom', 'packings']);
        return view('requests.show', compact('stockRequest'));
    }

    /**
     * Approval Action by Pegawai Pelulus (Separation of duties enforced)
     */
    public function approve(Request $request, StockRequest $stockRequest)
    {
        $user = Auth::user();

        // Enforce separation of duties: requester cannot approve own request
        if ($stockRequest->requester_id === $user->id && !$user->hasRole('admin')) {
            return back()->with('error', 'Pemisahan tugas (Separation of Duties): Anda tidak boleh meluluskan permohonan sendiri.');
        }

        $validated = $request->validate([
            'action' => 'required|in:APPROVE,PARTIAL,REJECT',
            'approval_remarks' => 'nullable|string',
            'items' => 'required_unless:action,REJECT|array',
            'items.*.approved_quantity' => 'numeric|min:0',
        ]);

        if ($validated['action'] === 'REJECT') {
            $stockRequest->status = 'REJECTED';
            $stockRequest->approver_id = $user->id;
            $stockRequest->approved_at = now();
            $stockRequest->approval_remarks = $validated['approval_remarks'] ?? 'Permohonan ditolak oleh Pegawai Pelulus.';
            $stockRequest->save();

            AuditLogService::log('REJECT_REQUEST', 'StockRequest', (string) $stockRequest->id, null, ['status' => 'REJECTED'], 'Permohonan stok ditolak.');
            return redirect()->route('requests.show', $stockRequest)->with('success', 'Permohonan telah ditolak.');
        }

        DB::transaction(function () use ($validated, $stockRequest, $user) {
            $isPartial = false;

            foreach ($validated['items'] as $itemId => $data) {
                $item = StockRequestItem::findOrFail($itemId);
                $approvedQty = (float) $data['approved_quantity'];
                $item->approved_quantity = $approvedQty;
                $item->save();

                if ($approvedQty < (float) $item->requested_quantity) {
                    $isPartial = true;
                }
            }

            $stockRequest->status = $isPartial ? 'PARTIALLY_APPROVED' : 'APPROVED';
            $stockRequest->approver_id = $user->id;
            $stockRequest->approved_at = now();
            $stockRequest->approval_remarks = $validated['approval_remarks'] ?? 'Diluluskan.';
            $stockRequest->save();

            // Reserve approved quantity in inventory
            StockTransactionEngine::reserveStockForRequest($stockRequest);
        });

        return redirect()->route('requests.show', $stockRequest)->with('success', 'Permohonan berjaya diluluskan dan kuantiti stok diperuntukkan.');
    }

    /**
     * Issue Stock Action by Pegawai Stor
     */
    public function issue(Request $request, StockRequest $stockRequest)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.issued_quantity' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $stockRequest) {
            foreach ($validated['items'] as $itemId => $data) {
                $item = StockRequestItem::findOrFail($itemId);
                $item->issued_quantity = (float) $data['issued_quantity'];
                $item->save();
            }

            $stockRequest->status = 'ISSUED';
            $stockRequest->issuer_id = Auth::id();
            $stockRequest->issued_at = now();
            $stockRequest->save();

            // Execute atomic issue deduction and record Section B ledger!
            StockTransactionEngine::issueStock($stockRequest);
        });

        return redirect()->route('requests.show', $stockRequest)->with('success', 'Stok berjaya dikeluarkan dan Daftar Stok KEW.PS-3 telah dikemaskini.');
    }

    /**
     * Recipient Confirmation
     */
    public function confirmReceipt(Request $request, StockRequest $stockRequest)
    {
        $validated = $request->validate([
            'recipient_notes' => 'nullable|string',
        ]);

        $stockRequest->status = 'COMPLETED';
        $stockRequest->recipient_id = Auth::id();
        $stockRequest->received_at = now();
        $stockRequest->recipient_notes = $validated['recipient_notes'] ?? 'Diterima dalam keadaan baik dan mencukupi.';
        $stockRequest->save();

        AuditLogService::log(
            'CONFIRM_RECEIPT',
            'StockRequest',
            (string) $stockRequest->id,
            null,
            ['status' => 'COMPLETED'],
            'Penerimaan stok telah disahkan oleh pemohon.'
        );

        return redirect()->route('requests.show', $stockRequest)->with('success', 'Pengesahan penerimaan stok berjaya disimpan.');
    }

    /**
     * Create Packing Slip (KEW.PS-9)
     */
    public function createPacking(StockRequest $stockRequest)
    {
        $stockRequest->load(['store', 'requester', 'items.stockItem.uom']);
        return view('requests.packing_create', compact('stockRequest'));
    }

    public function storePacking(Request $request, StockRequest $stockRequest)
    {
        $validated = $request->validate([
            'package_type' => 'required|string|max:50',
            'weight_kg' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:50',
            'delivery_address' => 'required|string',
            'handling_instructions' => 'nullable|array',
        ]);

        $packingNumber = DocumentNumberService::generate('PS9');

        $packing = Packing::create([
            'packing_number' => $packingNumber,
            'stock_request_id' => $stockRequest->id,
            'package_number' => 'BKG-' . sprintf("%02d", $stockRequest->packings()->count() + 1),
            'package_type' => $validated['package_type'],
            'weight_kg' => $validated['weight_kg'],
            'dimensions' => $validated['dimensions'],
            'sender_name' => $stockRequest->store->name . ' (Pegawai Stor ASM)',
            'receiver_name' => $stockRequest->requester->name,
            'delivery_address' => $validated['delivery_address'],
            'handling_instructions' => $validated['handling_instructions'] ?? [],
            'packing_officer_id' => Auth::id(),
        ]);

        return redirect()->route('requests.show', $stockRequest)->with('success', "Borang Pembungkusan {$packingNumber} berjaya dijana.");
    }

    public function printKewPs8(StockRequest $stockRequest)
    {
        $stockRequest->load(['store', 'requester', 'approver', 'issuer', 'recipient', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_8_print', compact('stockRequest'));
    }

    public function printKewPs7(StockRequest $stockRequest)
    {
        $stockRequest->load(['store', 'requestingStore', 'requester', 'approver', 'issuer', 'recipient', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_7_print', compact('stockRequest'));
    }

    public function printKewPs9(Packing $packing)
    {
        $packing->load(['stockRequest.items.stockItem.uom', 'packingOfficer']);
        return view('reports.kew_ps.kew_ps_9_print', compact('packing'));
    }
}
