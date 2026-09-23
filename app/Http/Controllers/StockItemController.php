<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockUnit;
use App\Services\AuditLogService;
use App\Services\StockLevelService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockItemController extends Controller
{
    public function index(Request $request)
    {
        $query = StockItem::with(['category', 'uom', 'defaultLocation.store']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('stock_code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('supplier_name', 'like', "%{$s}%")
                  ->orWhere('kad_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_group')) {
            $query->where('stock_group', $request->stock_group);
        }

        if ($request->filled('movement')) {
            $query->where('movement', $request->movement);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('level_alert')) {
            if ($request->level_alert === 'below_min') {
                $query->whereRaw('current_quantity < min_level AND min_level > 0');
            } elseif ($request->level_alert === 'reorder') {
                $query->whereRaw('current_quantity <= reorder_level AND current_quantity >= min_level AND reorder_level > 0');
            } elseif ($request->level_alert === 'above_max') {
                $query->whereRaw('current_quantity > max_level AND max_level > 0');
            } elseif ($request->level_alert === 'no_stock') {
                $query->where('current_quantity', '<=', 0);
            }
        }

        $items = $query->orderBy('stock_code')->paginate(15)->withQueryString();

        $categories = StockCategory::all();
        $units = StockUnit::all();
        $locations = Location::where('is_active', true)->get();

        return view('stock.index', compact('items', 'categories', 'units', 'locations'));
    }

    public function create()
    {
        $categories = StockCategory::all();
        $units = StockUnit::all();
        $locations = Location::with('store')->where('is_active', true)->get();

        return view('stock.create_edit', compact('categories', 'units', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kad_no' => 'nullable|string|max:50',
            'stock_code' => 'required|string|unique:stock_items,stock_code|max:50',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:stock_categories,id',
            'stock_group' => 'required|in:A,B',
            'movement' => 'required|in:CEPAT,PERLAHAN',
            'uom_id' => 'required|exists:stock_units,id',
            'default_location_id' => 'nullable|exists:locations,id',
            'min_level' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'max_level' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'is_expiry_controlled' => 'boolean',
            'is_batch_controlled' => 'boolean',
            'is_serial_controlled' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        $validated['is_expiry_controlled'] = $request->boolean('is_expiry_controlled');
        $validated['is_batch_controlled'] = $request->boolean('is_batch_controlled');
        $validated['is_serial_controlled'] = $request->boolean('is_serial_controlled');
        $validated['status'] = 'AVAILABLE';
        $validated['current_quantity'] = 0; // Balances are only incremented via transactions!

        $stock = StockItem::create($validated);

        AuditLogService::log(
            'CREATE',
            'StockItem',
            (string) $stock->id,
            null,
            $stock->toArray(),
            'Item stok baru didaftarkan ke dalam katalog: ' . $stock->stock_code
        );

        return redirect()->route('stock.show', $stock)->with('success', 'Item stok berjaya didaftarkan ke dalam katalog.');
    }

    public function show(StockItem $stock)
    {
        $stock->load(['category', 'uom', 'defaultLocation.store', 'batches']);

        // Load KEW.PS-3 Bahagian B Transaction Ledger
        $transactions = $stock->transactionItems()
            ->with(['transaction.user', 'transaction.store'])
            ->latest('id')
            ->paginate(20);

        return view('stock.show', compact('stock', 'transactions'));
    }

    public function edit(StockItem $stock)
    {
        $categories = StockCategory::all();
        $units = StockUnit::all();
        $locations = Location::with('store')->where('is_active', true)->get();

        return view('stock.create_edit', compact('stock', 'categories', 'units', 'locations'));
    }

    public function update(Request $request, StockItem $stock)
    {
        $validated = $request->validate([
            'kad_no' => 'nullable|string|max:50',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:stock_categories,id',
            'stock_group' => 'required|in:A,B',
            'movement' => 'required|in:CEPAT,PERLAHAN',
            'uom_id' => 'required|exists:stock_units,id',
            'default_location_id' => 'nullable|exists:locations,id',
            'min_level' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'max_level' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'is_expiry_controlled' => 'boolean',
            'is_batch_controlled' => 'boolean',
            'is_serial_controlled' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        $oldValues = $stock->toArray();

        $validated['is_expiry_controlled'] = $request->boolean('is_expiry_controlled');
        $validated['is_batch_controlled'] = $request->boolean('is_batch_controlled');
        $validated['is_serial_controlled'] = $request->boolean('is_serial_controlled');

        $stock->update($validated);

        AuditLogService::log(
            'UPDATE',
            'StockItem',
            (string) $stock->id,
            $oldValues,
            $stock->toArray(),
            'Maklumat item stok ' . $stock->stock_code . ' dikemaskini.'
        );

        return redirect()->route('stock.show', $stock)->with('success', 'Maklumat item stok berjaya dikemaskini.');
    }

    public function toggleStatus(StockItem $stock)
    {
        $oldStatus = $stock->status;
        $newStatus = ($oldStatus === 'INACTIVE') ? 'AVAILABLE' : 'INACTIVE';
        $stock->status = $newStatus;
        $stock->save();

        AuditLogService::log(
            'TOGGLE_STATUS',
            'StockItem',
            (string) $stock->id,
            ['status' => $oldStatus],
            ['status' => $newStatus],
            'Status stok ditukar kepada ' . $newStatus
        );

        return back()->with('success', 'Status stok berjaya dikemaskini kepada ' . $newStatus . '.');
    }

    /**
     * KEW.PS-4 Kad Petak view
     */
    public function kadPetak(StockItem $stock)
    {
        $stock->load(['category', 'uom', 'defaultLocation.store']);
        $transactions = $stock->transactionItems()
            ->with(['transaction.user'])
            ->latest('id')
            ->take(30)
            ->get();

        return view('stock.kad_petak', compact('stock', 'transactions'));
    }

    /**
     * Export Stock Catalogue to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = StockItem::with(['category', 'uom', 'defaultLocation.store']);

        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('stock_group')) $query->where('stock_group', $request->stock_group);
        if ($request->filled('movement')) $query->where('movement', $request->movement);

        $items = $query->orderBy('stock_code')->get();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Katalog_Stok_ASM_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['No. Kod', 'No. Kad', 'Perihal Stok', 'Kategori', 'Kumpulan', 'Pergerakan', 'Unit', 'Lokasi', 'Harga Seunit (RM)', 'Baki Kuantiti', 'Jumlah Nilai (RM)', 'Paras Min', 'Paras Menokok', 'Paras Maks', 'Status'];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->stock_code,
                    $item->kad_no ?? '-',
                    $item->description,
                    $item->category->name ?? '-',
                    $item->stock_group,
                    $item->movement,
                    $item->uom->code ?? '-',
                    $item->defaultLocation->full_code ?? '-',
                    number_format($item->unit_price, 2),
                    $item->current_quantity,
                    number_format($item->total_value, 2),
                    $item->min_level,
                    $item->reorder_level,
                    $item->max_level,
                    $item->status,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Print View for Stock Catalogue
     */
    public function printCatalogue(Request $request)
    {
        $items = StockItem::with(['category', 'uom', 'defaultLocation'])->orderBy('stock_code')->get();
        return view('stock.print_catalogue', compact('items'));
    }

    /**
     * JSON Search API for Fast Live Autocomplete
     */
    public function searchApi(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $stocks = StockItem::with(['category', 'uom'])
            ->where('status', 'AVAILABLE')
            ->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('description', 'like', "%{$query}%")
                        ->orWhere('stock_code', 'like', "%{$query}%")
                        ->orWhere('item_code', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('description')
            ->limit(40)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'code' => $s->stock_code ?? $s->item_code,
                'description' => $s->description,
                'category' => $s->category->name ?? 'Umum',
                'category_id' => $s->category_id,
                'quantity' => (float)$s->current_quantity,
                'uom' => $s->uom->code ?? 'UNIT',
            ]);

        return response()->json($stocks);
    }
}

