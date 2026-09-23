<?php

namespace App\Http\Controllers;

use App\Models\InvestigationCommittee;
use App\Models\LossCase;
use App\Models\LossItem;
use App\Models\StockItem;
use App\Models\Store;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\StockTransactionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LossWriteOffController extends Controller
{
    public function index(Request $request)
    {
        $query = LossCase::with(['store', 'approver', 'items.stockItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lossCases = $query->latest('id')->paginate(15)->withQueryString();

        return view('loss.index', compact('lossCases'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $stockItems = StockItem::where('status', '!=', 'INACTIVE')->orderBy('description')->get();

        return view('loss.create', compact('stores', 'stockItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'incident_date' => 'required|date',
            'discovery_date' => 'required|date',
            'description' => 'required|string',
            'police_report_no' => 'nullable|string|max:100',
            'police_report_date' => 'nullable|date',
            'police_action_status' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.circumstances' => 'nullable|string',
        ]);

        $caseNumber = DocumentNumberService::generate('PS32');

        DB::transaction(function () use ($validated, $caseNumber) {
            $totalLoss = 0;

            $lossCase = LossCase::create([
                'case_number' => $caseNumber,
                'store_id' => $validated['store_id'],
                'incident_date' => $validated['incident_date'],
                'discovery_date' => $validated['discovery_date'],
                'description' => $validated['description'],
                'police_report_no' => $validated['police_report_no'],
                'police_report_date' => $validated['police_report_date'],
                'police_action_status' => $validated['police_action_status'],
                'status' => 'REPORTED',
            ]);

            foreach ($validated['items'] as $itemData) {
                $stock = StockItem::findOrFail($itemData['stock_item_id']);
                $qty = (float) $itemData['quantity'];
                $totalVal = $qty * (float) $stock->unit_price;
                $totalLoss += $totalVal;

                LossItem::create([
                    'loss_case_id' => $lossCase->id,
                    'stock_item_id' => $stock->id,
                    'quantity' => $qty,
                    'unit_price' => $stock->unit_price,
                    'total_value' => $totalVal,
                    'circumstances' => $itemData['circumstances'] ?? null,
                ]);
            }

            $lossCase->total_loss_value = $totalLoss;
            $lossCase->save();

            AuditLogService::log(
                'REPORT_LOSS',
                'LossCase',
                (string) $lossCase->id,
                null,
                ['case_number' => $caseNumber],
                'Laporan Awal Kehilangan Stok KEW.PS-32 didaftarkan.'
            );
        });

        return redirect()->route('loss.index')->with('success', "Laporan Awal Kehilangan Stok {$caseNumber} berjaya didaftarkan.");
    }

    public function show(LossCase $lossCase)
    {
        $lossCase->load(['store', 'approver', 'committees', 'items.stockItem.uom']);
        return view('loss.show', compact('lossCase'));
    }

    /**
     * Appoint Investigation Committee (KEW.PS-33)
     */
    public function appointCommittee(Request $request, LossCase $lossCase)
    {
        $validated = $request->validate([
            'investigation_committee_ref' => 'required|string|max:100',
            'committees' => 'required|array|min:2',
            'committees.*.officer_name' => 'required|string|max:100',
            'committees.*.position' => 'required|string|max:100',
            'committees.*.department' => 'required|string|max:100',
            'committees.*.role' => 'required|in:PENGERUSI,AHLI',
        ]);

        DB::transaction(function () use ($validated, $lossCase) {
            $lossCase->status = 'INVESTIGATING';
            $lossCase->investigation_committee_ref = $validated['investigation_committee_ref'];
            $lossCase->save();

            foreach ($validated['committees'] as $commData) {
                InvestigationCommittee::create(array_merge($commData, ['loss_case_id' => $lossCase->id]));
            }

            AuditLogService::log(
                'APPOINT_COMMITTEE',
                'LossCase',
                (string) $lossCase->id,
                null,
                ['ref' => $lossCase->investigation_committee_ref],
                'Pelantikan Jawatankuasa Penyiasat KEW.PS-33 dikeluarkan.'
            );
        });

        return redirect()->route('loss.show', $lossCase)->with('success', 'Jawatankuasa Penyiasat (KEW.PS-33) berjaya dilantik.');
    }

    /**
     * Submit Final Investigation Report (KEW.PS-34)
     */
    public function submitFinalReport(Request $request, LossCase $lossCase)
    {
        $year = date('Y');
        $finalReportRef = "KEW.PS-34/ASM/{$year}/" . sprintf("%04d", $lossCase->id);

        $validated = $request->validate([
            'findings' => 'required|string',
            'recommendations' => 'required|string',
            'surcharge_recommended' => 'boolean',
            'surcharge_details' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $lossCase, $finalReportRef) {
            $lossCase->status = 'SUBMITTED_FOR_WRITE_OFF';
            $lossCase->final_report_ref = $finalReportRef;
            $lossCase->surcharge_recommended = $validated['surcharge_recommended'] ?? false;
            $lossCase->surcharge_details = $validated['surcharge_details'] ?? null;
            $lossCase->save();

            // Update first committee row findings
            if ($comm = $lossCase->committees()->first()) {
                $comm->findings = $validated['findings'];
                $comm->recommendations = $validated['recommendations'];
                $comm->report_date = now()->toDateString();
                $comm->save();
            }

            AuditLogService::log(
                'FINAL_REPORT',
                'LossCase',
                (string) $lossCase->id,
                null,
                ['final_report_ref' => $finalReportRef],
                'Laporan Akhir Kehilangan Stok KEW.PS-34 dikemukakan untuk kelulusan Hapus Kira.'
            );
        });

        return redirect()->route('loss.show', $lossCase)->with('success', "Laporan Akhir Kehilangan Stok {$finalReportRef} dihantar untuk kelulusan Hapus Kira.");
    }

    /**
     * Approve Write-Off & Issue Certificate (KEW.PS-35)
     */
    public function approveWriteOff(Request $request, LossCase $lossCase)
    {
        $year = date('Y');
        $certNumber = "KEW.PS-35/ASM/{$year}/" . sprintf("%04d", $lossCase->id);

        DB::transaction(function () use ($lossCase, $certNumber) {
            $lossCase->status = $lossCase->surcharge_recommended ? 'SURCHARGE_RECOMMENDED' : 'APPROVED';
            $lossCase->write_off_cert_number = $certNumber;
            $lossCase->authority_approval_date = now()->toDateString();
            $lossCase->approver_id = Auth::id();
            $lossCase->save();

            // Atomically update stock registers and deduct written-off quantities!
            StockTransactionEngine::completeWriteOff($lossCase);
        });

        return redirect()->route('loss.show', $lossCase)->with('success', "Hapus kira diluluskan dengan Sijil {$certNumber}. Baki stok telah dikeluarkan daripada inventori aktif.");
    }

    // Forms Printing
    public function printKewPs32(LossCase $lossCase)
    {
        $lossCase->load(['store', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_32_print', compact('lossCase'));
    }

    public function printKewPs33(LossCase $lossCase)
    {
        $lossCase->load(['store', 'committees']);
        return view('reports.kew_ps.kew_ps_33_print', compact('lossCase'));
    }

    public function printKewPs34(LossCase $lossCase)
    {
        $lossCase->load(['store', 'committees', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_34_print', compact('lossCase'));
    }

    public function printKewPs35(LossCase $lossCase)
    {
        $lossCase->load(['store', 'approver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_35_print', compact('lossCase'));
    }

    public function printKewPs36(LossCase $lossCase)
    {
        $lossCase->load(['store', 'approver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_36_print', compact('lossCase'));
    }
}
