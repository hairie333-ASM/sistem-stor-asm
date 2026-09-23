<?php

namespace App\Http\Controllers;

use App\Models\Disposal;
use App\Models\LossCase;
use App\Models\Packing;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Rejection;
use App\Models\RejectionItem;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\StockTransactionItem;
use App\Models\StockTransfer;
use App\Models\StockVerification;
use App\Models\Store;
use App\Models\User;
use App\Services\StockTurnoverService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Master Government Reports Directory (KEW.PS-1 to KEW.PS-36 Hub)
     */
    public function index()
    {
        $currentYear = Carbon::now()->year;

        // Statistics for each module report
        $counts = [
            'kew_ps_1' => Receiving::where('status', 'ACCEPTED')->count(),
            'kew_ps_2' => Rejection::count(),
            'kew_ps_3' => StockItem::count(),
            'kew_ps_4' => StockItem::count(),
            'kew_ps_5' => StockItem::where('stock_group', 'A')->count(),
            'kew_ps_6' => StockItem::where('is_expiry_controlled', true)->count(),
            'kew_ps_7' => StockRequest::where('form_type', 'KEW.PS-7')->count(),
            'kew_ps_8' => StockRequest::where('form_type', 'KEW.PS-8')->count(),
            'kew_ps_11' => StockVerification::count(),
            'kew_ps_12' => StockVerification::whereNotNull('report_number')->count(),
            'kew_ps_14' => 4, // 4 Quarters
            'kew_ps_15' => StockAdjustment::count(),
            'kew_ps_17' => StockTransfer::count(),
            'kew_ps_20' => Disposal::count(),
            'kew_ps_32' => LossCase::count(),
            'kew_ps_35' => LossCase::whereNotNull('write_off_cert_number')->count(),
        ];

        return view('reports.index', compact('counts', 'currentYear'));
    }

    /**
     * KEW.PS-14 Quarterly & Annual Stock Position Report
     */
    public function kewPs14(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $turnoverData = StockTurnoverService::calculateQuarterlyReport($year);
        $stores = Store::where('is_active', true)->get();

        return view('reports.kew_ps_14', compact('turnoverData', 'year', 'stores'));
    }

    /**
     * Print View for KEW.PS-14
     */
    public function printKewPs14(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $turnoverData = StockTurnoverService::calculateQuarterlyReport($year);
        return view('reports.kew_ps.kew_ps_14_print', compact('turnoverData', 'year'));
    }

    /**
     * Stock Valuation & Balance Summary Report
     */
    public function stockValuation()
    {
        $items = StockItem::with(['category', 'uom', 'defaultLocation'])
            ->orderBy('category_id')
            ->get();

        $totalValuation = (float) $items->sum(fn($i) => $i->current_quantity * $i->unit_price);
        $groupAValuation = (float) $items->where('stock_group', 'A')->sum(fn($i) => $i->current_quantity * $i->unit_price);
        $groupBValuation = (float) $items->where('stock_group', 'B')->sum(fn($i) => $i->current_quantity * $i->unit_price);

        return view('reports.stock_valuation', compact('items', 'totalValuation', 'groupAValuation', 'groupBValuation'));
    }

    /**
     * Reorder & Level Alerts Report (Below Min / Reorder)
     */
    public function reorderReport()
    {
        $criticalItems = StockItem::with(['category', 'uom', 'defaultLocation'])
            ->where(function ($q) {
                $q->whereRaw('current_quantity <= reorder_level AND reorder_level > 0')
                  ->orWhere('current_quantity', '<=', 0);
            })
            ->orderBy('current_quantity', 'asc')
            ->get();

        return view('reports.reorder_report', compact('criticalItems'));
    }

    /**
     * Universal Form Router / Preview for any KEW.PS Form (PS-1 to PS-36)
     */
    public function showKewPsForm(string $form)
    {
        // Normalize: replace both '.' and '-' with '_' (e.g. 'kew-ps-1' -> 'kew_ps_1')
        $formKey = strtolower(str_replace(['.', '-'], '_', $form));

        if (!view()->exists("reports.kew_ps.{$formKey}_print")) {
            return redirect()->route('reports.index')->with('error', "Templat cetakan bagi borang {$form} belum didaftarkan.");
        }

        $data = [
            'formTitle' => strtoupper(str_replace(['_', '-'], '.', $form)),
            'orgName' => 'Akademi Sains Malaysia (ASM)',
            'currentDate' => now()->translatedFormat('d F Y'),
        ];

        if ($formKey === 'kew_ps_1') {
            $receiving = Receiving::with(['store', 'items.stockItem.uom'])->latest()->first();
            if (!$receiving) {
                $store = Store::first();
                $sampleStock = StockItem::with('uom')->first();
                $receiving = new Receiving([
                    'btb_number' => 'BTB/ASM/' . date('Y') . '/0001',
                    'store_id' => $store->id ?? 1,
                    'received_date' => now()->toDateString(),
                    'purchase_order_number' => 'PO/ASM/' . date('Y') . '/0089',
                    'delivery_order_number' => 'DO/SPL/' . date('Y') . '/1204',
                    'invoice_number' => 'INV-2026-981',
                    'supplier_name' => 'Pembekal Rasmi ASM',
                    'supplier_address' => 'Kuala Lumpur',
                    'status' => 'ACCEPTED',
                    'total_amount' => $sampleStock ? ($sampleStock->unit_price * 10) : 100.0,
                ]);
                $receiving->setRelation('store', $store);
                if ($sampleStock) {
                    $item = new ReceivingItem([
                        'stock_item_id' => $sampleStock->id,
                        'ordered_quantity' => 10,
                        'delivered_quantity' => 10,
                        'accepted_quantity' => 10,
                        'rejected_quantity' => 0,
                        'unit_price' => $sampleStock->unit_price,
                        'total_price' => $sampleStock->unit_price * 10,
                        'status' => 'ACCEPTED',
                    ]);
                    $item->setRelation('stockItem', $sampleStock);
                    $receiving->setRelation('items', collect([$item]));
                } else {
                    $receiving->setRelation('items', collect());
                }
            }
            $data['receiving'] = $receiving;
        } elseif ($formKey === 'kew_ps_2') {
            $rejection = Rejection::with(['receiving.store', 'items.stockItem.uom'])->latest()->first();
            if (!$rejection) {
                $store = Store::first();
                $sampleStock = StockItem::with('uom')->first();
                $rec = new Receiving(['btb_number' => 'BTB/ASM/' . date('Y') . '/0001']);
                $rec->setRelation('store', $store);
                $rejection = new Rejection([
                    'bpb_number' => 'BPB/ASM/' . date('Y') . '/0001',
                    'rejection_date' => now()->toDateString(),
                    'supplier_name' => 'Pembekal Rasmi ASM',
                    'delivery_order_number' => 'DO/SPL/' . date('Y') . '/1204',
                ]);
                $rejection->setRelation('receiving', $rec);
                if ($sampleStock) {
                    $rItem = new RejectionItem([
                        'stock_item_id' => $sampleStock->id,
                        'delivered_quantity' => 5,
                        'rejected_quantity' => 5,
                        'rejection_reason' => 'Kerosakan fizikal / Tidak mematuhi spesifikasi',
                    ]);
                    $rItem->setRelation('stockItem', $sampleStock);
                    $rejection->setRelation('items', collect([$rItem]));
                } else {
                    $rejection->setRelation('items', collect());
                }
            }
            $data['rejection'] = $rejection;
        } elseif (in_array($formKey, ['kew_ps_3', 'kew_ps_4'])) {
            $stock = StockItem::with(['category', 'uom', 'defaultLocation.store'])->first() ?? new StockItem();
            $transactions = $stock->exists ? $stock->transactionItems()->with(['transaction.user', 'batch'])->latest()->take(30)->get() : collect();
            $data['stock'] = $stock;
            $data['transactions'] = $transactions;
        } elseif (in_array($formKey, ['kew_ps_7', 'kew_ps_8'])) {
            $stockRequest = StockRequest::with(['store', 'requester', 'approver', 'issuer', 'items.stockItem.uom'])->latest()->first();
            if (!$stockRequest) {
                $store = Store::first();
                $sampleStock = StockItem::with('uom')->first();
                $stockRequest = new StockRequest([
                    'request_number' => ($formKey === 'kew_ps_7' ? 'PS7' : 'PS8') . '/ASM/' . date('Y') . '/0001',
                    'form_type' => ($formKey === 'kew_ps_7' ? 'KEW.PS-7' : 'KEW.PS-8'),
                    'request_date' => now()->toDateString(),
                    'status' => 'APPROVED',
                    'purpose' => 'Kegunaan Rasmi Akademi Sains Malaysia',
                ]);
                $stockRequest->setRelation('store', $store);
                $stockRequest->setRelation('requester', User::first());
                $stockRequest->setRelation('approver', User::whereHas('role', fn($q) => $q->where('name', 'pegawai_pelulus'))->first());
                if ($sampleStock) {
                    $sItem = new StockRequestItem([
                        'stock_item_id' => $sampleStock->id,
                        'requested_quantity' => 5,
                        'approved_quantity' => 5,
                        'issued_quantity' => 5,
                    ]);
                    $sItem->setRelation('stockItem', $sampleStock);
                    $stockRequest->setRelation('items', collect([$sItem]));
                } else {
                    $stockRequest->setRelation('items', collect());
                }
            }
            $data['stockRequest'] = $stockRequest;
        } elseif ($formKey === 'kew_ps_9') {
            $packing = Packing::with(['stockRequest.store', 'packingOfficer'])->latest()->first();
            if (!$packing) {
                $packing = new Packing([
                    'packing_number' => 'PS9/ASM/' . date('Y') . '/0001',
                    'package_number' => 'PKG-001',
                    'package_type' => 'KOTAK',
                    'weight_kg' => 2.5,
                    'dimensions' => '30x20x15 cm',
                    'sender_name' => 'Unit Pengurusan Stor ASM',
                    'receiver_name' => 'Seksyen Outreach & Program',
                    'delivery_address' => 'Tingkat 20, Menara MATRADE',
                    'handling_instructions' => ['Mudah Pecah / Fragile', 'Jauhkan dari Air'],
                ]);
                $req = new StockRequest(['request_number' => 'PS8/ASM/' . date('Y') . '/0001']);
                $req->setRelation('store', Store::first());
                $packing->setRelation('stockRequest', $req);
                $packing->setRelation('packingOfficer', User::first());
            }
            $data['packing'] = $packing;
        } elseif (in_array($formKey, ['kew_ps_10', 'kew_ps_11', 'kew_ps_12', 'kew_ps_13'])) {
            $verification = StockVerification::with(['store', 'verifier1', 'verifier2', 'approvalOfficer', 'items.stockItem.uom'])->latest()->first();
            if (!$verification) {
                $verification = new StockVerification([
                    'verification_number' => 'PS11/ASM/' . date('Y') . '/001',
                    'report_number' => 'PS12/ASM/' . date('Y') . '/001',
                    'cert_number' => 'PS13/ASM/' . date('Y') . '/001',
                    'year' => date('Y'),
                    'appointment_letter_ref' => 'ASM/AST/LANTIK/' . date('Y') . '/01',
                    'scheduled_date' => now()->toDateString(),
                    'status' => 'APPROVED',
                    'approved_at' => now(),
                    'findings_summary' => 'Pemeriksaan fizikal menunjukkan baki stok adalah teratur dan mematuhi rekod.',
                    'corrective_actions' => 'Kekalkan kawalan penyimpanan mengikut MDKD.',
                ]);
                $verification->setRelation('store', Store::first());
                $verification->setRelation('verifier1', User::whereHas('role', fn($q) => $q->where('name', 'pemverifikasi'))->first() ?? User::first());
                $verification->setRelation('verifier2', User::whereHas('role', fn($q) => $q->where('name', 'pemverifikasi'))->skip(1)->first() ?? User::first());
                $verification->setRelation('approvalOfficer', User::whereHas('role', fn($q) => $q->where('name', 'ketua_jabatan'))->first());
                $verification->setRelation('items', collect());
            }
            $data['verification'] = $verification;
        } elseif ($formKey === 'kew_ps_14') {
            $data['turnoverData'] = StockTurnoverService::calculateTurnover(date('Y'));
            $data['year'] = date('Y');
            $data['q'] = 3;
        } elseif (in_array($formKey, ['kew_ps_15', 'kew_ps_16'])) {
            $adjustment = StockAdjustment::with(['store', 'requester', 'approver', 'items.stockItem.uom'])->latest()->first();
            if (!$adjustment) {
                $adjustment = new StockAdjustment([
                    'adjustment_number' => 'PS15/ASM/' . date('Y') . '/0001',
                    'perakuan_number' => 'PS16/ASM/' . date('Y') . '/0001',
                    'reason' => 'APPROVED_VERIFICATION',
                    'status' => 'APPROVED',
                    'approved_at' => now(),
                    'approval_remarks' => 'Pelarasan diluluskan berasaskan dapatan verifikasi stor tahunan.',
                ]);
                $adjustment->setRelation('store', Store::first());
                $adjustment->setRelation('requester', User::first());
                $adjustment->setRelation('approver', User::whereHas('role', fn($q) => $q->where('name', 'ketua_jabatan'))->first());
                $adjustment->setRelation('items', collect());
            }
            $data['adjustment'] = $adjustment;
        } elseif ($formKey === 'kew_ps_17') {
            $transfer = StockTransfer::with(['sourceStore', 'destinationStore', 'requester', 'approver', 'sender', 'receiver', 'items.stockItem.uom'])->latest()->first();
            if (!$transfer) {
                $stores = Store::take(2)->get();
                $transfer = new StockTransfer([
                    'transfer_number' => 'PS17/ASM/' . date('Y') . '/0001',
                    'purpose' => 'Pindahan stok operasi antara stor ASM',
                    'status' => 'RECEIVED',
                    'approved_at' => now(),
                    'dispatched_at' => now(),
                    'received_at' => now(),
                ]);
                $transfer->setRelation('sourceStore', $stores[0] ?? Store::first());
                $transfer->setRelation('destinationStore', $stores[1] ?? Store::first());
                $transfer->setRelation('requester', User::first());
                $transfer->setRelation('approver', User::whereHas('role', fn($q) => $q->where('name', 'ketua_jabatan'))->first());
                $transfer->setRelation('sender', User::whereHas('role', fn($q) => $q->where('name', 'pegawai_stor'))->first());
                $transfer->setRelation('receiver', User::first());
                $transfer->setRelation('items', collect());
            }
            $data['transfer'] = $transfer;
        } elseif ($formKey === 'kew_ps_18') {
            $data['transfers'] = StockTransfer::with(['sourceStore', 'destinationStore'])->whereYear('created_at', date('Y'))->get();
            $data['year'] = date('Y');
        } elseif (in_array($formKey, ['kew_ps_19', 'kew_ps_20', 'kew_ps_21', 'kew_ps_22', 'kew_ps_23'])) {
            $disposal = Disposal::with(['store', 'items.stockItem.uom', 'committees'])->latest()->first();
            if (!$disposal) {
                $disposal = new Disposal([
                    'disposal_number' => 'PS20/ASM/' . date('Y') . '/0001',
                    'approval_letter_ref' => 'ASM/AST/PELUPUS/' . date('Y') . '/001',
                    'completion_cert_number' => 'PS22/ASM/' . date('Y') . '/0001',
                    'disposal_method' => 'E-WASTE',
                    'status' => 'COMPLETED',
                    'approved_at' => now(),
                    'completed_at' => now(),
                ]);
                $disposal->setRelation('store', Store::first());
                $disposal->setRelation('committees', collect());
                $disposal->setRelation('items', collect());
            }
            $data['disposal'] = $disposal;
        } elseif (in_array($formKey, ['kew_ps_32', 'kew_ps_33', 'kew_ps_34', 'kew_ps_35', 'kew_ps_36'])) {
            $lossCase = LossCase::with(['store', 'items.stockItem.uom', 'committees', 'investigationCommittee'])->latest()->first();
            if (!$lossCase) {
                $lossCase = new LossCase([
                    'case_number' => 'PS32/ASM/' . date('Y') . '/0001',
                    'final_report_number' => 'PS34/ASM/' . date('Y') . '/0001',
                    'write_off_cert_number' => 'PS35/ASM/' . date('Y') . '/0001',
                    'incident_date' => now()->subDays(5)->toDateString(),
                    'incident_location' => 'Stor Utama ASM, Menara MATRADE',
                    'description' => 'Kehilangan stok dalam simpanan stor semasa pemeriksaan berkala.',
                    'police_report_number' => 'RPT/MATRADE/2026/0491',
                    'status' => 'APPROVED_WRITEOFF',
                    'approved_at' => now(),
                ]);
                $lossCase->setRelation('store', Store::first());
                $lossCase->setRelation('committees', collect());
                $lossCase->setRelation('investigationCommittee', collect());
                $lossCase->setRelation('items', collect());
            }
            $data['lossCase'] = $lossCase;
        }

        return view("reports.kew_ps.{$formKey}_print", $data);
    }
}
