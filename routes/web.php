<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisposalController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LossWriteOffController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SafetyCleanlinessController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockRegisterController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\StockReturnController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Quick Demo Role Switcher for paired testing of multi-agent workflows
Route::get('/auth/switch-role/{role}', [AuthController::class, 'switchRole'])->name('auth.switch-role');

/*
|--------------------------------------------------------------------------
| Authenticated Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // 1. Dashboards
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/compliance', [DashboardController::class, 'compliance'])->name('dashboard.compliance');

    // 2. Stock Master / Katalog
    Route::get('/api/stocks/search', [StockItemController::class, 'searchApi'])->name('stock.search-api');
    Route::resource('stock', StockItemController::class);
    Route::post('stock/{stock}/toggle-status', [StockItemController::class, 'toggleStatus'])->name('stock.toggle-status');
    Route::get('stock-export/csv', [StockItemController::class, 'exportCsv'])->name('stock.export-csv');
    Route::get('stock-print/catalogue', [StockItemController::class, 'printCatalogue'])->name('stock.print-catalogue');
    Route::get('stock/{stock}/kad-petak', [StockItemController::class, 'kadPetak'])->name('stock.kad-petak');

    // 3. Stock Register — KEW.PS-3, 4, 5, 6
    Route::get('/stock-register', [StockRegisterController::class, 'index'])->name('stock-register.index');
    Route::get('/stock-register/{stock}', [StockRegisterController::class, 'show'])->name('stock-register.show');
    Route::get('/stock-register/{stock}/print', [StockRegisterController::class, 'printKewPs3'])->name('stock-register.print');
    Route::get('/stock-group-ab', [StockRegisterController::class, 'groupAb'])->name('stock.group-ab');
    Route::post('/stock-group-ab/apply', [StockRegisterController::class, 'applyGroupAb'])->name('stock.group-ab.apply');
    Route::get('/stock-expiry-monitoring', [StockRegisterController::class, 'expiryMonitoring'])->name('stock.expiry');

    // 4. Locations & Storage
    Route::resource('locations', LocationController::class);
    Route::get('locations/{location}/print-label', [LocationController::class, 'printLabel'])->name('locations.print-label');

    // 5. Receiving — AM 6.2 (KEW.PS-1 BTB & KEW.PS-2 BPB)
    Route::resource('receiving', ReceivingController::class);
    Route::post('receiving/{receiving}/inspect', [ReceivingController::class, 'inspect'])->name('receiving.inspect');
    Route::get('receiving/{receiving}/print-btb', [ReceivingController::class, 'printKewPs1'])->name('receiving.print-btb');
    Route::get('rejections/{rejection}/print-bpb', [ReceivingController::class, 'printKewPs2'])->name('rejections.print-bpb');

    Route::resource('requests', StockRequestController::class)->parameters(['requests' => 'stockRequest']);
    Route::post('requests/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('requests.approve');
    Route::post('requests/{stockRequest}/issue', [StockRequestController::class, 'issue'])->name('requests.issue');
    Route::post('requests/{stockRequest}/confirm', [StockRequestController::class, 'confirmReceipt'])->name('requests.confirm');
    Route::get('requests/{stockRequest}/packing', [StockRequestController::class, 'createPacking'])->name('requests.packing.create');
    Route::post('requests/{stockRequest}/packing', [StockRequestController::class, 'storePacking'])->name('requests.packing.store');
    Route::get('requests/{stockRequest}/print-ps8', [StockRequestController::class, 'printKewPs8'])->name('requests.print-ps8');
    Route::get('requests/{stockRequest}/print-ps7', [StockRequestController::class, 'printKewPs7'])->name('requests.print-ps7');
    Route::get('packing/{packing}/print-ps9', [StockRequestController::class, 'printKewPs9'])->name('packing.print-ps9');

    // 7. Stock Returns
    Route::resource('returns', StockReturnController::class);
    Route::post('returns/{return}/inspect', [StockReturnController::class, 'inspect'])->name('returns.inspect');

    // 8. Stock Transfers — AM 6.8 (KEW.PS-17 & KEW.PS-18)
    Route::resource('transfers', StockTransferController::class);
    Route::post('transfers/{transfer}/approve', [StockTransferController::class, 'approve'])->name('transfers.approve');
    Route::post('transfers/{transfer}/dispatch', [StockTransferController::class, 'dispatchTransfer'])->name('transfers.dispatch');
    Route::post('transfers/{transfer}/receive', [StockTransferController::class, 'receiveTransfer'])->name('transfers.receive');
    Route::get('transfers/{transfer}/print-ps17', [StockTransferController::class, 'printKewPs17'])->name('transfers.print-ps17');
    Route::get('transfers-report/ps18', [StockTransferController::class, 'reportKewPs18'])->name('transfers.report-ps18');

    // 9. Stock Verification & Adjustments — AM 6.6 (KEW.PS-10 to KEW.PS-16)
    Route::resource('verification', StockVerificationController::class);
    Route::post('verification/{verification}/start', [StockVerificationController::class, 'startVerification'])->name('verification.start');
    Route::post('verification/{verification}/record-counts', [StockVerificationController::class, 'recordCounts'])->name('verification.record-counts');
    Route::post('verification/{verification}/approve', [StockVerificationController::class, 'approve'])->name('verification.approve');
    Route::get('verification/{verification}/adjustments/create', [StockVerificationController::class, 'createAdjustment'])->name('verification.adjustments.create');
    Route::post('verification/{verification}/adjustments', [StockVerificationController::class, 'storeAdjustment'])->name('verification.adjustments.store');
    Route::post('adjustments/{adjustment}/approve', [StockVerificationController::class, 'approveAdjustment'])->name('adjustments.approve');

    Route::get('verification/{verification}/print-ps10', [StockVerificationController::class, 'printKewPs10'])->name('verification.print-ps10');
    Route::get('verification/{verification}/print-ps11', [StockVerificationController::class, 'printKewPs11'])->name('verification.print-ps11');
    Route::get('verification/{verification}/print-ps12', [StockVerificationController::class, 'printKewPs12'])->name('verification.print-ps12');
    Route::get('verification/{verification}/print-ps13', [StockVerificationController::class, 'printKewPs13'])->name('verification.print-ps13');
    Route::get('adjustments/{adjustment}/print-ps15', [StockVerificationController::class, 'printKewPs15'])->name('adjustments.print-ps15');
    Route::get('adjustments/{adjustment}/print-ps16', [StockVerificationController::class, 'printKewPs16'])->name('adjustments.print-ps16');

    // 10. Disposal — AM 6.9 (KEW.PS-19 to KEW.PS-31)
    Route::resource('disposal', DisposalController::class);
    Route::post('disposal/{disposal}/approve', [DisposalController::class, 'approve'])->name('disposal.approve');
    Route::post('disposal/{disposal}/complete', [DisposalController::class, 'complete'])->name('disposal.complete');
    Route::get('disposal/{disposal}/print-ps19', [DisposalController::class, 'printKewPs19'])->name('disposal.print-ps19');
    Route::get('disposal/{disposal}/print-ps20', [DisposalController::class, 'printKewPs20'])->name('disposal.print-ps20');
    Route::get('disposal/{disposal}/print-ps21', [DisposalController::class, 'printKewPs21'])->name('disposal.print-ps21');
    Route::get('disposal/{disposal}/print-ps22', [DisposalController::class, 'printKewPs22'])->name('disposal.print-ps22');
    Route::get('disposal/{disposal}/print-ps23', [DisposalController::class, 'printKewPs23'])->name('disposal.print-ps23');

    // 11. Loss & Write-Off — AM 6.10 (KEW.PS-32 to KEW.PS-36)
    Route::resource('loss', LossWriteOffController::class);
    Route::post('loss/{lossCase}/appoint-committee', [LossWriteOffController::class, 'appointCommittee'])->name('loss.appoint-committee');
    Route::post('loss/{lossCase}/submit-final-report', [LossWriteOffController::class, 'submitFinalReport'])->name('loss.submit-final-report');
    Route::post('loss/{lossCase}/approve-write-off', [LossWriteOffController::class, 'approveWriteOff'])->name('loss.approve-write-off');
    Route::get('loss/{lossCase}/print-ps32', [LossWriteOffController::class, 'printKewPs32'])->name('loss.print-ps32');
    Route::get('loss/{lossCase}/print-ps33', [LossWriteOffController::class, 'printKewPs33'])->name('loss.print-ps33');
    Route::get('loss/{lossCase}/print-ps34', [LossWriteOffController::class, 'printKewPs34'])->name('loss.print-ps34');
    Route::get('loss/{lossCase}/print-ps35', [LossWriteOffController::class, 'printKewPs35'])->name('loss.print-ps35');
    Route::get('loss/{lossCase}/print-ps36', [LossWriteOffController::class, 'printKewPs36'])->name('loss.print-ps36');

    // 12. Safety & Cleanliness — AM 6.7
    Route::resource('safety', SafetyCleanlinessController::class);

    // 13. Reports Hub & Statutory KEW.PS Forms
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/kew-ps-14', [ReportController::class, 'kewPs14'])->name('reports.kew-ps-14');
    Route::get('/reports/kew-ps-14/print', [ReportController::class, 'printKewPs14'])->name('reports.kew-ps-14.print');
    Route::get('/reports/stock-valuation', [ReportController::class, 'stockValuation'])->name('reports.stock-valuation');
    Route::get('/reports/reorder-report', [ReportController::class, 'reorderReport'])->name('reports.reorder');
    Route::get('/reports/form/{form}', [ReportController::class, 'showKewPsForm'])->name('reports.form');

    // 14. Administration
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/audit', [AdminController::class, 'auditLogs'])->name('audit');
        Route::get('/year-end', [AdminController::class, 'yearEnd'])->name('year-end');
        Route::post('/year-end', [AdminController::class, 'processYearEnd'])->name('year-end.process');
    });
});
