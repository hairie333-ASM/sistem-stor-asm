<?php

namespace App\Http\Controllers;

use App\Models\Disposal;
use App\Models\DisposalCommittee;
use App\Models\DisposalItem;
use App\Models\StockItem;
use App\Models\Store;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\StockTransactionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Disposal::with(['store', 'approver', 'items.stockItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disposals = $query->latest('id')->paginate(15)->withQueryString();

        return view('disposal.index', compact('disposals'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        // Eligible items: obsolete, damaged, expired or manual select
        $stockItems = StockItem::where('status', '!=', 'INACTIVE')->orderBy('description')->get();

        return view('disposal.create', compact('stores', 'stockItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'disposal_method' => 'required|in:SALE,SCRAP,SCHEDULED_WASTE,EXCHANGE,TRADE_IN,GIFT,DESTROY',
            'committee_appointment_ref' => 'required|string|max:100',
            'remarks' => 'nullable|string',
            'committees' => 'required|array|min:2',
            'committees.*.officer_name' => 'required|string|max:100',
            'committees.*.position' => 'required|string|max:100',
            'committees.*.department' => 'required|string|max:100',
            'committees.*.role' => 'required|in:PENGERUSI,AHLI,SAKSI',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.condition' => 'required|in:USANG,ROSAK,EXPIRED,TIDAK_DIPERLUKAN,LEBIHAN,TIDAK_EKONOMI,LAIN_LAIN',
            'items.*.justification' => 'required|string|max:255',
            'items.*.recommended_method' => 'nullable|string|max:100',
        ]);

        $disposalNumber = DocumentNumberService::generate('PS20');

        DB::transaction(function () use ($validated, $disposalNumber) {
            $totalOriginal = 0;

            $disposal = Disposal::create([
                'disposal_number' => $disposalNumber,
                'store_id' => $validated['store_id'],
                'committee_appointment_ref' => $validated['committee_appointment_ref'],
                'disposal_method' => $validated['disposal_method'],
                'status' => 'PROPOSED',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['committees'] as $commData) {
                DisposalCommittee::create(array_merge($commData, ['disposal_id' => $disposal->id]));
            }

            foreach ($validated['items'] as $itemData) {
                $stock = StockItem::lockForUpdate()->find($itemData['stock_item_id']);
                $qty = (float) $itemData['quantity'];
                $totalPrice = $qty * (float) $stock->unit_price;
                $totalOriginal += $totalPrice;

                // Move stock quantity to disposal_quantity (isolate from available!)
                if ($stock->current_quantity >= $qty) {
                    $stock->current_quantity -= $qty;
                }
                $stock->disposal_quantity += $qty;
                $stock->save();

                DisposalItem::create([
                    'disposal_id' => $disposal->id,
                    'stock_item_id' => $stock->id,
                    'quantity' => $qty,
                    'unit_price' => $stock->unit_price,
                    'total_price' => $totalPrice,
                    'condition' => $itemData['condition'],
                    'justification' => $itemData['justification'],
                    'recommended_method' => $itemData['recommended_method'] ?? $validated['disposal_method'],
                    'status' => 'PROPOSED',
                ]);
            }

            $disposal->total_original_value = $totalOriginal;
            $disposal->save();

            AuditLogService::log(
                'PROPOSE_DISPOSAL',
                'Disposal',
                (string) $disposal->id,
                null,
                ['disposal_number' => $disposalNumber],
                'Cadangan pelupusan stok KEW.PS-20 dihantar kepada Lembaga Pemeriksa Pelupusan.'
            );
        });

        return redirect()->route('disposal.index')->with('success', "Cadangan pelupusan {$disposalNumber} berjaya dihantar untuk pemeriksaan.");
    }

    public function show(Disposal $disposal)
    {
        $disposal->load(['store', 'approver', 'committees', 'items.stockItem.uom']);
        return view('disposal.show', compact('disposal'));
    }

    /**
     * Approve Disposal by Kuasa Melulus (KEW.PS-21 Surat Kelulusan Pelupusan)
     */
    public function approve(Request $request, Disposal $disposal)
    {
        $year = date('Y');
        $approvalRef = "KEW.PS-21/ASM/{$year}/" . sprintf("%04d", $disposal->id);

        $disposal->status = 'APPROVED';
        $disposal->approval_reference = $approvalRef;
        $disposal->approver_id = Auth::id();
        $disposal->approved_at = now();
        $disposal->save();

        AuditLogService::log(
            'APPROVE_DISPOSAL',
            'Disposal',
            (string) $disposal->id,
            null,
            ['approval_reference' => $approvalRef],
            'Pelupusan stok diluluskan oleh Kuasa Melulus (KEW.PS-21).'
        );

        return redirect()->route('disposal.show', $disposal)->with('success', "Pelupusan diluluskan dengan Rujukan Kelulusan {$approvalRef}. Tindakan pelupusan fizikal boleh dilaksanakan.");
    }

    /**
     * Complete Physical Disposal & Issue Certificates (KEW.PS-22 & KEW.PS-23)
     */
    public function complete(Request $request, Disposal $disposal)
    {
        $year = date('Y');
        $witnessCert = "KEW.PS-22/ASM/{$year}/" . sprintf("%04d", $disposal->id);
        $completionCert = "KEW.PS-23/ASM/{$year}/" . sprintf("%04d", $disposal->id);

        $validated = $request->validate([
            'total_revenue' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($disposal, $witnessCert, $completionCert, $validated) {
            $disposal->status = 'COMPLETED';
            $disposal->witness_cert_number = $witnessCert;
            $disposal->completion_cert_number = $completionCert;
            $disposal->completed_at = now();
            $disposal->total_revenue = (float) ($validated['total_revenue'] ?? 0);
            $disposal->save();

            // Post deduction to Stock Transaction Engine atomically!
            StockTransactionEngine::completeDisposal($disposal);
        });

        return redirect()->route('disposal.show', $disposal)->with('success', "Pelupusan fizikal selesai. Sijil Pelupusan Stok {$completionCert} dijana dan baki stok dikeluarkan.");
    }

    // Forms Printing
    public function printKewPs19(Disposal $disposal)
    {
        $disposal->load(['store', 'committees']);
        return view('reports.kew_ps.kew_ps_19_print', compact('disposal'));
    }

    public function printKewPs20(Disposal $disposal)
    {
        $disposal->load(['store', 'committees', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_20_print', compact('disposal'));
    }

    public function printKewPs21(Disposal $disposal)
    {
        $disposal->load(['store', 'approver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_21_print', compact('disposal'));
    }

    public function printKewPs22(Disposal $disposal)
    {
        $disposal->load(['store', 'committees', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_22_print', compact('disposal'));
    }

    public function printKewPs23(Disposal $disposal)
    {
        $disposal->load(['store', 'approver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_23_print', compact('disposal'));
    }
}
