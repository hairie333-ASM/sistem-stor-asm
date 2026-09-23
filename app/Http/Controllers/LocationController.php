<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\StockItem;
use App\Models\Store;
use App\Models\StoreSection;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::with('sections')->get();
        $query = Location::with(['store', 'section', 'stockItems']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_code', 'like', "%{$s}%")
                  ->orWhere('rack', 'like', "%{$s}%")
                  ->orWhere('bin', 'like', "%{$s}%");
            });
        }

        $locations = $query->orderBy('full_code')->paginate(20)->withQueryString();

        return view('locations.index', compact('stores', 'locations'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $sections = StoreSection::all();
        return view('locations.create_edit', compact('stores', 'sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'section_id' => 'nullable|exists:store_sections,id',
            'row' => 'required|string|max:10',
            'rack' => 'required|string|max:20',
            'level' => 'required|string|max:10',
            'bin' => 'required|string|max:10',
            'capacity' => 'nullable|integer',
        ]);

        $store = Store::findOrFail($request->store_id);
        $sectionCode = '01';
        if ($request->filled('section_id')) {
            $sec = StoreSection::find($request->section_id);
            $sectionCode = $sec ? $sec->code : '01';
        }

        // Generate full location code: e.g. STR1-SEC-ROW-RACK-LVL-BIN
        $fullCode = sprintf(
            "%s-%s-%s-%s-%s-%s",
            $store->code,
            $sectionCode,
            str_pad($request->row, 2, '0', STR_PAD_LEFT),
            $request->rack,
            str_pad($request->level, 2, '0', STR_PAD_LEFT),
            str_pad($request->bin, 2, '0', STR_PAD_LEFT)
        );

        $validated['full_code'] = $fullCode;
        $validated['barcode'] = $fullCode;
        $validated['is_active'] = true;

        $location = Location::create($validated);

        AuditLogService::log(
            'CREATE',
            'Location',
            (string) $location->id,
            null,
            $location->toArray(),
            'Lokasi penyimpanan baru didaftarkan: ' . $fullCode
        );

        return redirect()->route('locations.index')->with('success', "Lokasi {$fullCode} berjaya didaftarkan.");
    }

    public function show(Location $location)
    {
        $location->load(['store', 'section', 'stockItems.category', 'batches.stockItem']);
        return view('locations.show', compact('location'));
    }

    /**
     * Print Location Label & QR Barcode
     */
    public function printLabel(Location $location)
    {
        $location->load(['store', 'section']);
        return view('locations.print_label', compact('location'));
    }
}
