@extends('layouts.app')

@section('title', 'Jejak Audit Sistem (Audit Trail)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-slate-800 text-white">Integriti Data</span>
                <span class="text-xs text-slate-500">Log Transaksi Tidak Boleh Dipinda (Immutable)</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Jejak Audit Sistem (Audit Trail)</h1>
            <p class="text-sm text-slate-600">Rakaman menyeluruh setiap transaksi, penerimaan, kelulusan, pelarasan dan aktiviti pengguna mengikut ketetapan TPS.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.audit') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ulasan, IP, tindakan..." class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-48">
                <select name="module" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Modul --</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                Tapis Log
            </button>
            @if(request()->filled('search') || request()->filled('module'))
            <a href="{{ route('admin.audit') }}" class="text-xs text-rose-600 hover:underline">
                Kosongkan Tapisan
            </a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 w-36">Masa & Tarikh</th>
                        <th class="py-3 px-4 w-40">Pengguna & Peranan</th>
                        <th class="py-3 px-4 w-32">Modul</th>
                        <th class="py-3 px-4 w-36">Tindakan</th>
                        <th class="py-3 px-4">Keterangan / Butiran Aktiviti</th>
                        <th class="py-3 px-4 w-28 text-right font-mono">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono text-slate-600">
                            <div>{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-800">{{ $log->user->name ?? 'Sistem / Anonim' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $log->role_name ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 font-medium text-blue-900">
                            {{ $log->module }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-800">
                            {{ $log->remarks }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-400 text-[11px]">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400 text-sm">
                            Tiada rekod jejak audit dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
