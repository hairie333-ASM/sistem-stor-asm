<?php

namespace App\Http\Controllers;

use App\Models\StockBatch;
use App\Models\StockItem;
use App\Services\GroupABService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockRegisterController extends Controller
{
    /**
     * KEW.PS-3 Master Register listing & search
     */
    public function index(Request $request)
    {
        $query = StockItem::with(['category', 'uom', 'defaultLocation.store']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('stock_code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('kad_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('stock_group')) {
            $query->where('stock_group', $request->stock_group);
        }

        $items = $query->orderBy('stock_code')->paginate(20)->withQueryString();

        return view('stock.register_index', compact('items'));
    }

    /**
     * KEW.PS-3 Digital Form View (Bahagian A & Bahagian B)
     */
    public function show(StockItem $stock)
    {
        $stock->load(['category', 'uom', 'defaultLocation.store', 'defaultLocation.section', 'batches']);

        $transactions = $stock->transactionItems()
            ->with(['transaction.user', 'transaction.store', 'batch'])
            ->latest('id')
            ->paginate(25);

        return view('stock.show', compact('stock', 'transactions'));
    }

    /**
     * Print-Ready KEW.PS-3 Form
     */
    public function printKewPs3(StockItem $stock)
    {
        $stock->load(['category', 'uom', 'defaultLocation.store', 'defaultLocation.section']);

        $transactions = $stock->transactionItems()
            ->with(['transaction.user', 'batch'])
            ->latest('id')
            ->take(50)
            ->get();

        return view('reports.kew_ps.kew_ps_3_print', compact('stock', 'transactions'));
    }

    /**
     * KEW.PS-5 Penentuan Kumpulan A & B
     */
    public function groupAb()
    {
        $groupData = GroupABService::calculateGroups();
        return view('stock.group_ab', compact('groupData'));
    }

    /**
     * Apply/Re-classify Group A & B
     */
    public function applyGroupAb()
    {
        GroupABService::applyGroupClassification();
        return back()->with('success', 'Penentuan Kumpulan A & B (KEW.PS-5) berjaya dikemaskini.');
    }

    /**
     * KEW.PS-6 Senarai Stok Bertarikh Luput
     */
    public function expiryMonitoring(Request $request)
    {
        $query = StockBatch::with(['stockItem.category', 'stockItem.uom', 'location.store'])
            ->whereNotNull('expiry_date')
            ->where('remaining_quantity', '>', 0);

        if ($request->filled('filter')) {
            $now = Carbon::now();
            if ($request->filter === 'expired') {
                $query->where('expiry_date', '<', $now);
            } elseif ($request->filter === 'critical') { // < 30 days
                $query->whereBetween('expiry_date', [$now, $now->copy()->addDays(30)]);
            } elseif ($request->filter === 'near_60') { // 30-60 days
                $query->whereBetween('expiry_date', [$now->copy()->addDays(30), $now->copy()->addDays(60)]);
            } elseif ($request->filter === 'near_90') { // 60-90 days
                $query->whereBetween('expiry_date', [$now->copy()->addDays(60), $now->copy()->addDays(90)]);
            }
        }

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(20)->withQueryString();

        return view('stock.expiry_monitoring', compact('batches'));
    }
}
