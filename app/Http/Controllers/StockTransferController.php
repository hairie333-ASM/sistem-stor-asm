<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\StockTransactionEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransfer::with(['sourceStore', 'destinationStore', 'requester', 'items.stockItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->latest('id')->paginate(15)->withQueryString();

        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $stockItems = StockItem::with(['category', 'uom'])
            ->where('status', 'AVAILABLE')
            ->where('current_quantity', '>', 0)
            ->orderBy('description')
            ->get();

        $stockItemsJson = $stockItems->map(fn($s) => [
            'id' => $s->id,
            'code' => $s->stock_code ?? $s->item_code,
            'description' => $s->description,
            'category' => $s->category->name ?? 'Umum',
            'category_id' => $s->category_id,
            'quantity' => (float)$s->current_quantity,
            'uom' => $s->uom->code ?? 'UNIT',
        ]);

        return view('transfers.create', compact('stores', 'stockItems', 'stockItemsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_store_id' => 'required|exists:stores,id',
            'destination_store_id' => 'required|exists:stores,id|different:source_store_id',
            'purpose' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.requested_quantity' => 'required|numeric|min:1',
            'items.*.remarks' => 'nullable|string',
        ]);

        $transferNumber = DocumentNumberService::generate('PS17');

        DB::transaction(function () use ($validated, $transferNumber) {
            $transfer = StockTransfer::create([
                'transfer_number' => $transferNumber,
                'source_store_id' => $validated['source_store_id'],
                'destination_store_id' => $validated['destination_store_id'],
                'requester_id' => Auth::id(),
                'purpose' => $validated['purpose'],
                'status' => 'SUBMITTED',
            ]);

            foreach ($validated['items'] as $itemData) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'stock_item_id' => $itemData['stock_item_id'],
                    'requested_quantity' => $itemData['requested_quantity'],
                    'approved_quantity' => 0,
                    'sent_quantity' => 0,
                    'received_quantity' => 0,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            AuditLogService::log(
                'SUBMIT_TRANSFER',
                'StockTransfer',
                (string) $transfer->id,
                null,
                ['transfer_number' => $transferNumber],
                'Permohonan pindahan stok antara stor KEW.PS-17 dihantar.'
            );
        });

        return redirect()->route('transfers.index')->with('success', "Permohonan pindahan stok {$transferNumber} berjaya dihantar.");
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['sourceStore', 'destinationStore', 'requester', 'approver', 'sender', 'receiver', 'items.stockItem.uom']);
        return view('transfers.show', compact('transfer'));
    }

    /**
     * Approve Transfer (Ketua Jabatan / Pegawai Pelulus)
     */
    public function approve(Request $request, StockTransfer $transfer)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.approved_quantity' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $transfer) {
            foreach ($validated['items'] as $itemId => $data) {
                $item = StockTransferItem::findOrFail($itemId);
                $item->approved_quantity = (float) $data['approved_quantity'];
                $item->save();
            }

            $transfer->status = 'APPROVED';
            $transfer->approver_id = Auth::id();
            $transfer->approved_at = now();
            $transfer->save();

            AuditLogService::log(
                'APPROVE_TRANSFER',
                'StockTransfer',
                (string) $transfer->id,
                null,
                ['status' => 'APPROVED'],
                'Pindahan stok antara stor diluluskan.'
            );
        });

        return redirect()->route('transfers.show', $transfer)->with('success', 'Pindahan stok telah diluluskan.');
    }

    /**
     * Dispatch Transfer (Pegawai Stor Asal - Deduct from Source Store)
     */
    public function dispatchTransfer(StockTransfer $transfer)
    {
        $transfer->status = 'DISPATCHED';
        $transfer->sender_id = Auth::id();
        $transfer->dispatched_at = now();
        $transfer->save();

        // Atomically deduct from source store register
        StockTransactionEngine::dispatchTransfer($transfer);

        return redirect()->route('transfers.show', $transfer)->with('success', 'Stok telah dihantar keluar dari stor asal. Status kini dalam penghantaran.');
    }

    /**
     * Receive Transfer (Pegawai Stor Penerima - Add to Destination Store)
     */
    public function receiveTransfer(Request $request, StockTransfer $transfer)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $transfer) {
            foreach ($validated['items'] as $itemId => $data) {
                $item = StockTransferItem::findOrFail($itemId);
                $item->received_quantity = (float) $data['received_quantity'];
                $item->save();
            }

            $transfer->status = 'RECEIVED';
            $transfer->receiver_id = Auth::id();
            $transfer->received_at = now();
            $transfer->remarks = $validated['remarks'] ?? $transfer->remarks;
            $transfer->save();

            // Atomically update destination store register
            StockTransactionEngine::receiveTransfer($transfer);
        });

        return redirect()->route('transfers.show', $transfer)->with('success', 'Penerimaan pindahan stok disahkan. Kedua-dua daftar stok kini disegerakkan.');
    }

    public function printKewPs17(StockTransfer $transfer)
    {
        $transfer->load(['sourceStore', 'destinationStore', 'requester', 'approver', 'sender', 'receiver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_17_print', compact('transfer'));
    }

    public function reportKewPs18()
    {
        $year = Carbon::now()->year;
        $transfers = StockTransfer::with(['sourceStore', 'destinationStore', 'items.stockItem'])
            ->whereYear('created_at', $year)
            ->where('status', 'RECEIVED')
            ->get();

        return view('reports.kew_ps.kew_ps_18_print', compact('transfers', 'year'));
    }
}
