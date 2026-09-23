<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\GroupABService;
use App\Services\StockLevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * User Management Listing
     */
    public function users(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('staff_id', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'staff_id' => 'required|string|unique:users,staff_id|max:50',
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);

        AuditLogService::log(
            'CREATE_USER',
            'UserManagement',
            (string) $user->id,
            null,
            ['name' => $user->name, 'email' => $user->email],
            'Pengguna baru didaftarkan oleh pentadbir.'
        );

        return back()->with('success', 'Pengguna baru berjaya didaftarkan.');
    }

    public function toggleUserStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        AuditLogService::log(
            'TOGGLE_USER_STATUS',
            'UserManagement',
            (string) $user->id,
            null,
            ['is_active' => $user->is_active],
            'Status aktif pengguna dikemaskini.'
        );

        return back()->with('success', 'Status pengguna berjaya dikemaskini.');
    }

    /**
     * System Settings
     */
    public function settings()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            SystemSetting::set($key, (string) $value);
        }

        AuditLogService::log(
            'UPDATE_SETTINGS',
            'SystemSettings',
            null,
            null,
            $inputs,
            'Tetapan sistem dan format penomboran borang dikemaskini.'
        );

        return back()->with('success', 'Tetapan sistem berjaya disimpan.');
    }

    /**
     * Immutable Audit Log Viewer
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('remarks', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhere('role_name', 'like', "%{$s}%");
            });
        }

        $logs = $query->latest('created_at')->paginate(25)->withQueryString();
        $modules = AuditLog::distinct()->pluck('module')->filter();

        return view('admin.audit', compact('logs', 'modules'));
    }

    /**
     * Year-End Closing & Balance Rollover (AM 6.6 / Module 37)
     */
    public function yearEnd()
    {
        $currentYear = date('Y');
        $prevYear = $currentYear - 1;
        $totalItems = StockItem::count();

        return view('admin.year_end', compact('currentYear', 'prevYear', 'totalItems'));
    }

    public function processYearEnd(Request $request)
    {
        $year = (int) $request->input('closing_year', date('Y'));

        DB::transaction(function () use ($year) {
            // 1. Recalculate 3-2-1 month parameters for all items
            $stocks = StockItem::all();
            foreach ($stocks as $stock) {
                StockLevelService::recalculateItemLevels($stock);
            }

            // 2. Perform Group A & B Annual Review
            GroupABService::applyGroupClassification();

            // 3. Set Year-End Lock Flag in Settings
            SystemSetting::set("year_end_closed_{$year}", 'true', 'FINANCIAL', "Tutup Tahun Kewangan {$year}");
            SystemSetting::set("year_end_closed_date_{$year}", now()->toDateTimeString());

            AuditLogService::log(
                'YEAR_END_PROCESSING',
                'YearEnd',
                (string) $year,
                null,
                ['closed_year' => $year],
                "Proses Akhir Tahun {$year} selesai: Baki stok dibawa ke hadapan, paras stok diselaraskan dan Kumpulan A/B disemak."
            );
        });

        return back()->with('success', "Proses Akhir Tahun bagi {$year} berjaya dilaksanakan. Baki telah dibawa ke hadapan dan paras stok telah diselaraskan.");
    }
}
