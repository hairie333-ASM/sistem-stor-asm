<?php

namespace App\Http\Controllers;

use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockReturn;
use App\Models\StockReturnItem;
use App\Models\Store;
use App\Services\AuditLogService;
use App\Services\StockTransactionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockReturnController extends Controller
{
    public function index()
    {
        $returns = StockReturn::with(['store', 'user', 'inspector', 'items.stockItem'])
            ->latest('id')
            ->paginate(15);

        return view('requests.returns_index', compact('returns'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $stockItems = StockItem::with(['category', 'uom'])->orderBy('description')->get();
        $categories = StockCategory::orderBy('name')->get();
        $recentRequests = StockRequest::whereIn('status', ['ISSUED', 'COMPLETED'])->latest('id')->take(20)->get();

        $stockItemsJson = $stockItems->map(fn($s) => [
            'id' => $s->id,
            'code' => $s->stock_code ?? $s->item_code,
            'description' => $s->description,
            'category' => $s->category->name ?? 'Umum',
            'category_id' => $s->category_id,
            'quantity' => (float)$s->current_quantity,
            'uom' => $s->uom->code ?? 'UNIT',
        ]);

        return view('requests.returns_create', compact('stores', 'stockItems', 'categories', 'stockItemsJson', 'recentRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'stock_request_id' => 'nullable|exists:stock_requests,id',
            'return_type' => 'required|in:FULL,PARTIAL,UNUSED,DAMAGED',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.returned_quantity' => 'required|numeric|min:1',
            'items.*.condition' => 'required|in:GOOD,DAMAGED',
            'items.*.remarks' => 'nullable|string',
        ]);

        $year = date('Y');
        $count = StockReturn::whereYear('created_at', $year)->count() + 1;
        $returnNumber = sprintf("RET/ASM/%s/%04d", $year, $count);

        DB::transaction(function () use ($validated, $returnNumber) {
            $stockReturn = StockReturn::create([
                'return_number' => $returnNumber,
                'stock_request_id' => $validated['stock_request_id'] ?? null,
                'store_id' => $validated['store_id'],
                'user_id' => Auth::id(),
                'return_type' => $validated['return_type'],
                'status' => 'PENDING_INSPECTION',
            ]);

            foreach ($validated['items'] as $itemData) {
                StockReturnItem::create([
                    'stock_return_id' => $stockReturn->id,
                    'stock_item_id' => $itemData['stock_item_id'],
                    'returned_quantity' => $itemData['returned_quantity'],
                    'accepted_quantity' => 0,
                    'condition' => $itemData['condition'],
                    'is_restocked' => false,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            AuditLogService::log(
                'SUBMIT_RETURN',
                'StockReturn',
                (string) $stockReturn->id,
                null,
                ['return_number' => $returnNumber],
                'Pemulangan stok dikemukakan. Menunggu pemeriksaan keadaan fizikal.'
            );
        });

        return redirect()->route('returns.index')->with('success', "Permohonan pemulangan stok {$returnNumber} berjaya dihantar.");
    }

    public function show(StockReturn $return)
    {
        $return->load(['store', 'user', 'inspector', 'stockRequest', 'items.stockItem.uom']);
        return view('requests.returns_show', compact('return'));
    }

    /**
     * Inspect returned stock (Pegawai Stor)
     */
    public function inspect(Request $request, StockReturn $return)
    {
        $validated = $request->validate([
            'inspection_notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.accepted_quantity' => 'required|numeric|min:0',
            'items.*.condition' => 'required|in:GOOD,DAMAGED',
        ]);

        DB::transaction(function () use ($validated, $return) {
            foreach ($validated['items'] as $itemId => $data) {
                $item = StockReturnItem::findOrFail($itemId);
                $item->accepted_quantity = (float) $data['accepted_quantity'];
                $item->condition = $data['condition'];
                $item->save();
            }

            $return->status = 'ACCEPTED';
            $return->inspector_id = Auth::id();
            $return->inspected_at = now();
            $return->inspection_notes = $validated['inspection_notes'];
            $return->save();

            // Process return to update inventory or damaged bucket
            StockTransactionEngine::processReturn($return);
        });

        return redirect()->route('returns.show', $return)->with('success', 'Pemeriksaan pemulangan selesai dan baki stok dikemaskini mengikut keadaan.');
    }
}
