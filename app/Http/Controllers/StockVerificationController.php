<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockItem;
use App\Models\StockVerification;
use App\Models\Store;
use App\Models\User;
use App\Models\VerificationItem;
use App\Services\AuditLogService;
use App\Services\DocumentNumberService;
use App\Services\StockTransactionEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockVerificationController extends Controller
{
    public function index(Request $request)
    {
        $verifications = StockVerification::with(['store', 'verifier1', 'verifier2', 'approvalOfficer'])
            ->latest('id')
            ->paginate(15);

        $adjustments = StockAdjustment::with(['store', 'requester', 'approver'])
            ->latest('id')
            ->paginate(15);

        return view('verification.index', compact('verifications', 'adjustments'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        // Independent officers not assigned to the store
        $users = User::where('is_active', true)->get();

        return view('verification.create', compact('stores', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'year' => 'required|integer|min:2020|max:2050',
            'appointment_letter_ref' => 'required|string|max:100',
            'scheduled_date' => 'required|date',
            'verifier_1_id' => 'required|exists:users,id',
            'verifier_2_id' => 'required|exists:users,id|different:verifier_1_id',
        ]);

        $verificationNumber = DocumentNumberService::generate('PS11');

        $verification = StockVerification::create([
            'verification_number' => $verificationNumber,
            'store_id' => $validated['store_id'],
            'year' => $validated['year'],
            'appointment_letter_ref' => $validated['appointment_letter_ref'],
            'scheduled_date' => $validated['scheduled_date'],
            'verifier_1_id' => $validated['verifier_1_id'],
            'verifier_2_id' => $validated['verifier_2_id'],
            'status' => 'SCHEDULED',
            'is_frozen' => false,
        ]);

        AuditLogService::log(
            'SCHEDULE_VERIFICATION',
            'StockVerification',
            (string) $verification->id,
            null,
            ['verification_number' => $verificationNumber],
            'Jadual Verifikasi Stor KEW.PS-11 dijana dan Pemverifikasi dilantik (KEW.PS-10).'
        );

        return redirect()->route('verification.index')->with('success', "Jadual Verifikasi {$verificationNumber} berjaya dicipta.");
    }

    public function show(StockVerification $verification)
    {
        $verification->load(['store', 'verifier1', 'verifier2', 'approvalOfficer', 'items.stockItem.uom', 'adjustments']);
        return view('verification.show', compact('verification'));
    }

    /**
     * Start Physical Count & Snapshot System Balances (Freeze Transactions)
     */
    public function startVerification(StockVerification $verification)
    {
        DB::transaction(function () use ($verification) {
            $verification->status = 'IN_PROGRESS';
            $verification->is_frozen = true;
            $verification->start_date = now()->toDateString();
            $verification->save();

            // Populate verification items snapshot from current store stocks
            $stocks = StockItem::where('status', '!=', 'INACTIVE')->get();
            foreach ($stocks as $stock) {
                VerificationItem::firstOrCreate(
                    [
                        'verification_id' => $verification->id,
                        'stock_item_id' => $stock->id,
                    ],
                    [
                        'system_quantity' => $stock->current_quantity,
                        'physical_quantity' => $stock->current_quantity, // default to match
                        'variance_quantity' => 0,
                        'surplus_quantity' => 0,
                        'shortage_quantity' => 0,
                        'damaged_quantity' => 0,
                        'obsolete_quantity' => 0,
                        'unit_price' => $stock->unit_price,
                        'variance_value' => 0,
                        'condition_status' => 'BAIK',
                    ]
                );
            }

            AuditLogService::log(
                'START_VERIFICATION',
                'StockVerification',
                (string) $verification->id,
                null,
                ['status' => 'IN_PROGRESS'],
                'Pemeriksaan fizikal stor dimulakan. Transaksi stor dibekukan.'
            );
        });

        return redirect()->route('verification.show', $verification)->with('success', 'Verifikasi fizikal dimulakan. Senarai item telah diselaraskan.');
    }

    /**
     * Save Physical Count Entries
     */
    public function recordCounts(Request $request, StockVerification $verification)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.physical_quantity' => 'required|numeric|min:0',
            'items.*.damaged_quantity' => 'nullable|numeric|min:0',
            'items.*.obsolete_quantity' => 'nullable|numeric|min:0',
            'items.*.condition_status' => 'required|in:BAIK,ROSAK,USANG,TIDAK_BERGERAK',
            'items.*.remarks' => 'nullable|string',
            'findings_summary' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $verification) {
            foreach ($validated['items'] as $itemId => $data) {
                $item = VerificationItem::findOrFail($itemId);
                $systemQty = (float) $item->system_quantity;
                $physicalQty = (float) $data['physical_quantity'];
                $variance = $physicalQty - $systemQty;

                $item->physical_quantity = $physicalQty;
                $item->variance_quantity = $variance;
                $item->surplus_quantity = $variance > 0 ? $variance : 0;
                $item->shortage_quantity = $variance < 0 ? abs($variance) : 0;
                $item->damaged_quantity = (float) ($data['damaged_quantity'] ?? 0);
                $item->obsolete_quantity = (float) ($data['obsolete_quantity'] ?? 0);
                $item->variance_value = $variance * (float) $item->unit_price;
                $item->condition_status = $data['condition_status'];
                $item->remarks = $data['remarks'] ?? null;
                $item->save();
            }

            $year = $verification->year;
            $verification->status = 'COMPLETED';
            $verification->is_frozen = false;
            $verification->end_date = now()->toDateString();
            $verification->report_number = "KEW.PS-12/ASM/{$year}/" . sprintf("%03d", $verification->id);
            $verification->findings_summary = $validated['findings_summary'] ?? $verification->findings_summary;
            $verification->corrective_actions = $validated['corrective_actions'] ?? $verification->corrective_actions;
            $verification->save();

            AuditLogService::log(
                'RECORD_VERIFICATION',
                'StockVerification',
                (string) $verification->id,
                null,
                ['status' => 'COMPLETED'],
                'Kiraan fizikal selesai direkodkan. Laporan Pemeriksaan/Verifikasi KEW.PS-12 dijana.'
            );
        });

        return redirect()->route('verification.show', $verification)->with('success', 'Kiraan fizikal selesai direkodkan. Laporan KEW.PS-12 sedia untuk perakuan Ketua Jabatan.');
    }

    /**
     * Approve Verification & Issue Certificate (KEW.PS-13) by Ketua Jabatan
     */
    public function approve(StockVerification $verification)
    {
        $year = $verification->year;
        $verification->status = 'APPROVED';
        $verification->approval_officer_id = Auth::id();
        $verification->approved_at = now();
        $verification->cert_number = "KEW.PS-13/ASM/{$year}/" . sprintf("%03d", $verification->id);
        $verification->save();

        AuditLogService::log(
            'APPROVE_VERIFICATION',
            'StockVerification',
            (string) $verification->id,
            null,
            ['cert_number' => $verification->cert_number],
            'Sijil Verifikasi Stor KEW.PS-13 dikeluarkan oleh Ketua Jabatan.'
        );

        return redirect()->route('verification.show', $verification)->with('success', "Sijil Verifikasi Stor {$verification->cert_number} berjaya diperakui.");
    }

    /**
     * Create Adjustment Request (KEW.PS-15) based on Verification Discrepancies
     */
    public function createAdjustment(StockVerification $verification)
    {
        $verification->load(['items.stockItem']);
        $discrepancies = $verification->items->filter(fn($i) => $i->variance_quantity != 0);

        return view('verification.adjustment_create', compact('verification', 'discrepancies'));
    }

    public function storeAdjustment(Request $request, StockVerification $verification)
    {
        $validated = $request->validate([
            'reason' => 'required|in:RECORDING_ERROR,PHYSICAL_DISCREPANCY,DAMAGE,APPROVED_VERIFICATION,OTHER',
            'items' => 'required|array',
            'items.*.adjustment_quantity' => 'required|numeric',
            'items.*.reason_detail' => 'nullable|string',
        ]);

        $adjNumber = DocumentNumberService::generate('PS15');

        DB::transaction(function () use ($validated, $verification, $adjNumber) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjNumber,
                'verification_id' => $verification->id,
                'store_id' => $verification->store_id,
                'reason' => $validated['reason'],
                'status' => 'SUBMITTED',
                'requester_id' => Auth::id(),
            ]);

            foreach ($validated['items'] as $stockItemId => $data) {
                $stock = StockItem::findOrFail($stockItemId);
                $adjQty = (float) $data['adjustment_quantity'];
                $newQty = (float) $stock->current_quantity + $adjQty;

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'stock_item_id' => $stock->id,
                    'current_quantity' => $stock->current_quantity,
                    'adjustment_quantity' => $adjQty,
                    'new_quantity' => $newQty,
                    'unit_price' => $stock->unit_price,
                    'total_variance_value' => $adjQty * (float) $stock->unit_price,
                    'reason_detail' => $data['reason_detail'] ?? null,
                ]);
            }

            AuditLogService::log(
                'SUBMIT_ADJUSTMENT',
                'StockAdjustment',
                (string) $adjustment->id,
                null,
                ['adjustment_number' => $adjNumber],
                'Permohonan Pelarasan Stok KEW.PS-15 dihantar untuk kelulusan Ketua Jabatan.'
            );
        });

        return redirect()->route('verification.index')->with('success', "Laporan Pelarasan Stok {$adjNumber} berjaya dihantar untuk kelulusan.");
    }

    /**
     * Approve Adjustment (Ketua Jabatan - KEW.PS-16 Perakuan Pelarasan Stok)
     */
    public function approveAdjustment(Request $request, StockAdjustment $adjustment)
    {
        $year = date('Y');
        $perakuanNumber = "KEW.PS-16/ASM/{$year}/" . sprintf("%04d", $adjustment->id);

        $adjustment->status = 'APPROVED';
        $adjustment->approver_id = Auth::id();
        $adjustment->approved_at = now();
        $adjustment->perakuan_number = $perakuanNumber;
        $adjustment->approval_remarks = $request->input('approval_remarks', 'Diluluskan mengikut peraturan kewangan.');
        $adjustment->save();

        // Atomically execute balance adjustment and Section B ledger update!
        StockTransactionEngine::applyAdjustment($adjustment);

        return redirect()->route('verification.index')->with('success', "Pelarasan stok diluluskan dengan Perakuan {$perakuanNumber}. Baki stok telah dikemaskini.");
    }

    // Forms Printing
    public function printKewPs10(StockVerification $verification)
    {
        $verification->load(['store', 'verifier1', 'verifier2']);
        return view('reports.kew_ps.kew_ps_10_print', compact('verification'));
    }

    public function printKewPs11(StockVerification $verification)
    {
        $verification->load(['store', 'verifier1', 'verifier2']);
        return view('reports.kew_ps.kew_ps_11_print', compact('verification'));
    }

    public function printKewPs12(StockVerification $verification)
    {
        $verification->load(['store', 'verifier1', 'verifier2', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_12_print', compact('verification'));
    }

    public function printKewPs13(StockVerification $verification)
    {
        $verification->load(['store', 'verifier1', 'verifier2', 'approvalOfficer']);
        return view('reports.kew_ps.kew_ps_13_print', compact('verification'));
    }

    public function printKewPs15(StockAdjustment $adjustment)
    {
        $adjustment->load(['store', 'requester', 'approver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_15_print', compact('adjustment'));
    }

    public function printKewPs16(StockAdjustment $adjustment)
    {
        $adjustment->load(['store', 'requester', 'approver', 'items.stockItem.uom']);
        return view('reports.kew_ps.kew_ps_16_print', compact('adjustment'));
    }
}
