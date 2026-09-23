<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $roles = Role::with('users')->get();
        return view('auth.login', compact('roles'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            AuditLogService::log(
                'LOGIN',
                'Authentication',
                (string) $user->id,
                null,
                ['email' => $user->email, 'role' => $user->role->name],
                'Pengguna berjaya log masuk ke sistem.'
            );

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Maklumat log masuk yang diberikan adalah tidak sah.',
        ])->onlyInput('email');
    }

    /**
     * Instant Role Switcher for Seamless Testing & Demonstration of Multi-Role Workflows
     */
    public function switchRole(Request $request, string $roleName)
    {
        $user = User::whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        })->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            AuditLogService::log(
                'SWITCH_ROLE',
                'Authentication',
                (string) $user->id,
                null,
                ['switched_to' => $roleName],
                'Penukaran peranan ujian pantas: ' . $user->name . ' (' . $user->role->display_name . ')'
            );

            return redirect()->back()->with('success', 'Berjaya menukar peranan kepada: ' . $user->role->display_name . ' (' . $user->name . ')');
        }

        return redirect()->back()->with('error', 'Pengguna untuk peranan tersebut tidak dijumpai.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLogService::log(
                'LOGOUT',
                'Authentication',
                (string) Auth::id(),
                null,
                null,
                'Pengguna telah log keluar.'
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berjaya log keluar.');
    }
}
