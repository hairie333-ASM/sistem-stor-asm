<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Rejection;
use App\Models\RejectionItem;
use App\Models\StockItem;
use App\Models\Store;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\StockTransactionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    public function index(Request $request)
    {
        $query = Receiving::with(['store', 'receivingOfficer', 'items.stockItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('btb_number', 'like', "%{$s}%")
                  ->orWhere('supplier_name', 'like', "%{$s}%")
                  ->orWhere('delivery_order_number', 'like', "%{$s}%")
                  ->orWhere('po_contract_number', 'like', "%{$s}%");
            });
        }

        $receivings = $query->latest('id')->paginate(15)->withQueryString();

        return view('receiving.index', compact('receivings'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $stockItems = StockItem::with(['category', 'uom'])->where('status', '!=', 'INACTIVE')->orderBy('description')->get();
        $technicalOfficers = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'pegawai_stor', 'ketua_jabatan', 'pegawai_penerima']);
        })->get();
        $locations = Location::where('is_active', true)->get();

        $stockItemsJson = $stockItems->map(fn($s) => [
            'id' => $s->id,
            'code' => $s->stock_code ?? $s->item_code,
            'description' => $s->description,
            'category' => $s->category->name ?? 'Umum',
            'category_id' => $s->category_id,
            'quantity' => (float)$s->current_quantity,
            'uom' => $s->uom->code ?? 'UNIT',
        ]);

        return view('receiving.create', compact('stores', 'stockItems', 'technicalOfficers', 'locations', 'stockItemsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string',
            'receipt_type' => 'required|in:PURCHASE,TRANSFER,GIFT,SEIZURE,OTHER',
            'po_contract_number' => 'nullable|string|max:100',
            'po_contract_date' => 'nullable|date',
            'delivery_order_number' => 'required|string|max:100',
            'delivery_order_date' => 'required|date',
            'carrier_info' => 'nullable|string|max:255',
            'technical_officer_id' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.ordered_quantity' => 'required|numeric|min:0',
            'items.*.do_quantity' => 'required|numeric|min:0',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.location_id' => 'nullable|exists:locations,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $btbNumber = DocumentNumberService::generate('BTB');

            $receiving = Receiving::create([
                'btb_number' => $btbNumber,
                'store_id' => $validated['store_id'],
                'supplier_name' => $validated['supplier_name'],
                'supplier_address' => $validated['supplier_address'],
                'receipt_type' => $validated['receipt_type'],
                'po_contract_number' => $validated['po_contract_number'],
                'po_contract_date' => $validated['po_contract_date'],
                'delivery_order_number' => $validated['delivery_order_number'],
                'delivery_order_date' => $validated['delivery_order_date'],
                'carrier_info' => $validated['carrier_info'],
                'status' => 'DRAFT', // Quarantine / Pending physical inspection
                'receiving_officer_id' => Auth::id(),
                'technical_officer_id' => $validated['technical_officer_id'],
                'remarks' => $validated['remarks'],
            ]);

            foreach ($validated['items'] as $itemData) {
                $ordered = (float) $itemData['ordered_quantity'];
                $received = (float) $itemData['received_quantity'];
                $unitPrice = (float) $itemData['unit_price'];

                ReceivingItem::create([
                    'receiving_id' => $receiving->id,
                    'stock_item_id' => $itemData['stock_item_id'],
                    'ordered_quantity' => $ordered,
                    'do_quantity' => (float) $itemData['do_quantity'],
                    'received_quantity' => $received,
                    'accepted_quantity' => $received, // Default accepted = received until inspection
                    'rejected_quantity' => 0,
                    'unit_price' => $unitPrice,
                    'total_price' => $received * $unitPrice,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'location_id' => $itemData['location_id'] ?? null,
                    'status' => 'PENDING',
                ]);
            }

            AuditLogService::log(
                'CREATE',
                'Receiving',
                (string) $receiving->id,
                null,
                ['btb_number' => $btbNumber, 'supplier' => $receiving->supplier_name],
                'Penerimaan stok didaftarkan. Menunggu pemeriksaan fizikal dan pengesahan BTB.'
            );
        });

        return redirect()->route('receiving.index')->with('success', 'Borang Terimaan Barang-Barang (BTB) berjaya dicipta.');
    }

    public function show(Receiving $receiving)
    {
        $receiving->load(['store', 'receivingOfficer', 'technicalOfficer', 'items.stockItem.uom', 'items.location', 'rejections.items.stockItem']);
        return view('receiving.show', compact('receiving'));
    }

    /**
     * Inspect and Confirm Acceptance / Generate Rejection (KEW.PS-1 & KEW.PS-2)
     */
    public function inspect(Request $request, Receiving $receiving)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'items' => 'required|array',
            'items.*.accepted_quantity' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'required|numeric|min:0',
            'items.*.rejection_reason' => 'nullable|in:DAMAGED,QUANTITY_LESS,QUANTITY_MORE,WRONG_ITEM,SPEC_MISMATCH,QUALITY_ISSUE,OTHER',
            'items.*.action_to_take' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $receiving) {
            $hasRejections = false;
            $rejectionItemsToCreate = [];

            foreach ($validated['items'] as $itemId => $itemData) {
                $receivingItem = ReceivingItem::findOrFail($itemId);
                $accepted = (float) $itemData['accepted_quantity'];
                $rejected = (float) $itemData['rejected_quantity'];

                $receivingItem->accepted_quantity = $accepted;
                $receivingItem->rejected_quantity = $rejected;
                $receivingItem->total_price = $accepted * (float) $receivingItem->unit_price;
                $receivingItem->status = ($rejected > 0) ? ($accepted > 0 ? 'PARTIAL' : 'REJECTED') : 'ACCEPTED';
                $receivingItem->save();

                if ($rejected > 0) {
                    $hasRejections = true;
                    $rejectionItemsToCreate[] = [
                        'receiving_item_id' => $receivingItem->id,
                        'stock_item_id' => $receivingItem->stock_item_id,
                        'rejected_quantity' => $rejected,
                        'rejection_reason' => $itemData['rejection_reason'] ?? 'QUALITY_ISSUE',
                        'action_to_take' => $itemData['action_to_take'] ?? 'Tukar ganti pembekal',
                    ];
                }
            }

            // Create KEW.PS-2 if any rejections occurred
            if ($hasRejections) {
                $bpbNumber = DocumentNumberService::generate('BPB');
                $rejection = Rejection::create([
                    'bpb_number' => $bpbNumber,
                    'receiving_id' => $receiving->id,
                    'supplier_name' => $receiving->supplier_name,
                    'delivery_order_number' => $receiving->delivery_order_number,
                    'rejection_date' => $validated['inspection_date'],
                    'officer_id' => Auth::id(),
                    'status' => 'PENDING_ACK',
                ]);

                foreach ($rejectionItemsToCreate as $rejData) {
                    RejectionItem::create(array_merge($rejData, ['rejection_id' => $rejection->id]));
                }
            }

            $receiving->inspection_date = $validated['inspection_date'];
            $receiving->status = $hasRejections ? 'PARTIALLY_REJECTED' : 'ACCEPTED';
            $receiving->remarks = $validated['remarks'] ?? $receiving->remarks;
            $receiving->save();

            // Post accepted quantities to Stock Transaction Engine atomically!
            StockTransactionEngine::processReceivingAcceptance($receiving);
        });

        return redirect()->route('receiving.show', $receiving)->with('success', 'Pemeriksaan penerimaan selesai. Kuantiti diterima telah dikemaskini dalam Daftar Stok KEW.PS-3.');
    }

    /**
     * Print KEW.PS-1 BTB
     */
    public function printKewPs1(Receiving $receiving)
    {
        $receiving->load(['store', 'receivingOfficer', 'technicalOfficer', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_1_print', compact('receiving'));
    }

    /**
     * Print KEW.PS-2 BPB
     */
    public function printKewPs2(Rejection $rejection)
    {
        $rejection->load(['receiving.store', 'officer', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_2_print', compact('rejection'));
    }
}
