<?php

namespace App\Http\Controllers;

use App\Models\Disposal;
use App\Models\LossCase;
use App\Models\Receiving;
use App\Models\StockBatch;
use App\Models\StockItem;
use App\Models\StockRequest;
use App\Models\StockTransaction;
use App\Models\StockTransfer;
use App\Models\StockVerification;
use App\Services\StockLevelService;
use App\Services\StockTurnoverService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisYear = Carbon::now()->year;

        // Stock Summary Metrics
        $totalStockItems = StockItem::count();
        $activeStockItems = StockItem::where('status', '!=', 'INACTIVE')->count();
        $inactiveStockItems = StockItem::where('status', 'INACTIVE')->count();

        $totalQuantity = (float) StockItem::sum('current_quantity');
        $totalStockValue = (float) StockItem::all()->sum(function ($item) {
            return $item->current_quantity * $item->unit_price;
        });

        // Damaged, Obsolete, Expired Stock
        $damagedQuantity = (float) StockItem::sum('damaged_quantity');
        $disposalQuantity = (float) StockItem::sum('disposal_quantity');
        $writtenOffQuantity = (float) StockItem::sum('written_off_quantity');

        // Expiry Metrics
        $now = Carbon::now();
        $expiredCount = StockBatch::where('expiry_date', '<', $now)->where('remaining_quantity', '>', 0)->count();
        $nearExpiryCount = StockBatch::whereBetween('expiry_date', [$now, $now->copy()->addDays(60)])
            ->where('remaining_quantity', '>', 0)
            ->count();

        // Level Alerts
        $levelSummaries = StockLevelService::getLevelSummaries();

        // Transaction Summary
        $todayReceiptsCount = StockTransaction::where('transaction_type', 'RECEIPT')
            ->whereDate('transaction_date', $today)
            ->count();
        $todayIssuesCount = StockTransaction::where('transaction_type', 'ISSUE')
            ->whereDate('transaction_date', $today)
            ->count();

        // Pending Operational Actions
        $pendingReceivings = Receiving::where('status', 'DRAFT')->count();
        $pendingRequests = StockRequest::where('status', 'SUBMITTED')->count();
        $pendingIssues = StockRequest::where('status', 'APPROVED')->count();
        $pendingTransfers = StockTransfer::whereIn('status', ['SUBMITTED', 'DISPATCHED'])->count();
        $pendingVerifications = StockVerification::whereIn('status', ['SCHEDULED', 'IN_PROGRESS'])->count();
        $pendingDisposals = Disposal::whereIn('status', ['PROPOSED', 'INSPECTED'])->count();
        $pendingLossCases = LossCase::whereIn('status', ['REPORTED', 'INVESTIGATING', 'SUBMITTED_FOR_WRITE_OFF'])->count();

        // Management KPIs (Turnover & Storage Performance)
        $turnoverData = StockTurnoverService::calculateQuarterlyReport($thisYear);

        // Service Level (Tahap Perkhidmatan = Total Fulfilled Requests / Total Requests * 100)
        $totalRequestsCount = StockRequest::whereYear('created_at', $thisYear)->count();
        $fulfilledRequestsCount = StockRequest::whereYear('created_at', $thisYear)->whereIn('status', ['ISSUED', 'COMPLETED'])->count();
        $serviceLevel = $totalRequestsCount > 0 ? round(($fulfilledRequestsCount / $totalRequestsCount) * 100, 1) : 100.0;

        // Group A vs Group B Breakdown
        $groupAValue = (float) StockItem::where('stock_group', 'A')->get()->sum(fn($i) => $i->current_quantity * $i->unit_price);
        $groupBValue = (float) StockItem::where('stock_group', 'B')->get()->sum(fn($i) => $i->current_quantity * $i->unit_price);

        // Recent Transactions
        $recentTransactions = StockTransaction::with(['store', 'user', 'items.stockItem'])
            ->latest()
            ->take(8)
            ->get();

        // Requester Personal Stats (For Pemohon role)
        $userId = auth()->id();
        $myRequests = StockRequest::with(['store', 'items.stockItem'])
            ->where('requester_id', $userId)
            ->latest('id')
            ->take(6)
            ->get();
        $myTotalRequests = StockRequest::where('requester_id', $userId)->count();
        $myPendingApproval = StockRequest::where('requester_id', $userId)->where('status', 'SUBMITTED')->count();
        $myApproved = StockRequest::where('requester_id', $userId)->where('status', 'APPROVED')->count();
        $myCompleted = StockRequest::where('requester_id', $userId)->whereIn('status', ['ISSUED', 'COMPLETED'])->count();

        return view('dashboard.index', compact(
            'totalStockItems',
            'activeStockItems',
            'inactiveStockItems',
            'totalQuantity',
            'totalStockValue',
            'damagedQuantity',
            'disposalQuantity',
            'writtenOffQuantity',
            'expiredCount',
            'nearExpiryCount',
            'levelSummaries',
            'todayReceiptsCount',
            'todayIssuesCount',
            'pendingReceivings',
            'pendingRequests',
            'pendingIssues',
            'pendingTransfers',
            'pendingVerifications',
            'pendingDisposals',
            'pendingLossCases',
            'turnoverData',
            'serviceLevel',
            'groupAValue',
            'groupBValue',
            'recentTransactions',
            'myRequests',
            'myTotalRequests',
            'myPendingApproval',
            'myApproved',
            'myCompleted'
        ));
    }

    /**
     * Dedicated TPS Compliance Traffic-Light Dashboard (AM 6.1 - AM 6.10)
     */
    public function compliance()
    {
        $thisYear = Carbon::now()->year;

        // Evaluate compliance factors
        $checks = [
            [
                'code' => 'AM 6.1',
                'title' => 'Pengurusan Am & Pelantikan Pegawai Stor',
                'description' => 'Semua stor didaftarkan dan mempunyai Pegawai Stor bertauliah.',
                'status' => 'PASS', // PASS, WARNING, FAIL
                'details' => 'Semua Stor Utama dan Unit mempunyai pegawai stor yang dilantik.',
            ],
            [
                'code' => 'AM 6.2',
                'title' => 'Pemeriksaan Penerimaan & Rekod BTB (KEW.PS-1)',
                'description' => 'Pemeriksaan fizikal dan pengeluaran KEW.PS-1 bagi semua barang diterima.',
                'status' => Receiving::where('status', 'DRAFT')->exists() ? 'WARNING' : 'PASS',
                'details' => Receiving::where('status', 'DRAFT')->exists()
                    ? Receiving::where('status', 'DRAFT')->count() . ' penerimaan menunggu pengesahan fizikal.'
                    : 'Tiada penerimaan tertangguh.',
            ],
            [
                'code' => 'AM 6.3',
                'title' => 'Penyelenggaraan Daftar Stok KEW.PS-3 & Kad Petak',
                'description' => 'Rekod stok dikemaskini dalam tempoh masa nyata tanpa percanggahan.',
                'status' => 'PASS',
                'details' => 'Daftar stok dijana secara automatik dengan baki terkira.',
            ],
            [
                'code' => 'AM 6.4',
                'title' => 'Pengurusan Paras Stok (3-2-1 Bulan)',
                'description' => 'Mematuhi had maksimum 3 bulan dan paras menokok 2 bulan.',
                'status' => StockItem::whereRaw('current_quantity < min_level AND min_level > 0')->exists() ? 'WARNING' : 'PASS',
                'details' => StockItem::whereRaw('current_quantity < min_level AND min_level > 0')->count() . ' item berada di bawah paras minimum.',
            ],
            [
                'code' => 'AM 6.5',
                'title' => 'Pengeluaran Berdasarkan Kelulusan Sah (KEW.PS-8)',
                'description' => 'Tiada pengeluaran tanpa kelulusan Pegawai Pelulus yang ditetapkan.',
                'status' => StockRequest::where('status', 'SUBMITTED')->exists() ? 'WARNING' : 'PASS',
                'details' => StockRequest::where('status', 'SUBMITTED')->count() . ' permohonan sedang menunggu kelulusan.',
            ],
            [
                'code' => 'AM 6.6',
                'title' => 'Verifikasi Stor Tahunan (KEW.PS-10 hingga KEW.PS-14)',
                'description' => 'Lembaga Pemverifikasi Stor dilantik dan verifikasi dilaksanakan sekurang-kurangnya sekali setahun.',
                'status' => StockVerification::where('year', $thisYear)->where('status', 'APPROVED')->exists() ? 'PASS' : 'WARNING',
                'details' => StockVerification::where('year', $thisYear)->where('status', 'APPROVED')->exists()
                    ? 'Verifikasi stor tahun ' . $thisYear . ' telah disahkan oleh Ketua Jabatan.'
                    : 'Verifikasi stor bagi tahun ' . $thisYear . ' sedang dijadualkan / belum selesai.',
            ],
            [
                'code' => 'AM 6.7',
                'title' => 'Kawalan Keselamatan dan Kebersihan Stor',
                'description' => 'Pemeriksaan alat pemadam api, pintu kecemasan, kawalan kunci dan kebersihan.',
                'status' => 'PASS',
                'details' => 'Pemeriksaan berkala suku tahunan direkodkan.',
            ],
            [
                'code' => 'AM 6.8',
                'title' => 'Pindahan Antara Stor Disahkan Penerima (KEW.PS-17)',
                'description' => 'Semua pindahan mempunyai akuan terima stor destinasi.',
                'status' => StockTransfer::where('status', 'DISPATCHED')->exists() ? 'WARNING' : 'PASS',
                'details' => StockTransfer::where('status', 'DISPATCHED')->count() . ' pindahan sedang dalam penghantaran.',
            ],
            [
                'code' => 'AM 6.9',
                'title' => 'Pengurusan Pelupusan Stok Usang/Rosak (KEW.PS-20)',
                'description' => 'Stok usang/rosak dilupuskan melalui Lembaga Pemeriksa Pelupusan berdaftar.',
                'status' => Disposal::whereIn('status', ['PROPOSED', 'INSPECTED'])->exists() ? 'WARNING' : 'PASS',
                'details' => Disposal::whereIn('status', ['PROPOSED', 'INSPECTED'])->count() . ' tindakan pelupusan menunggu kelulusan Kuasa Melulus.',
            ],
            [
                'code' => 'AM 6.10',
                'title' => 'Tindakan Kehilangan & Hapus Kira (KEW.PS-32 hingga KEW.PS-36)',
                'description' => 'Laporan polis difailkan dan Jawatankuasa Penyiasat dilantik dalam tempoh ditetapkan.',
                'status' => LossCase::whereIn('status', ['REPORTED', 'INVESTIGATING'])->exists() ? 'WARNING' : 'PASS',
                'details' => LossCase::whereIn('status', ['REPORTED', 'INVESTIGATING'])->count() . ' kes kehilangan dalam proses siasatan.',
            ],
        ];

        return view('dashboard.compliance', compact('checks'));
    }
}
