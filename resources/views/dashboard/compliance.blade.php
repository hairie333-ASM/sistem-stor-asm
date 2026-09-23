@extends('layouts.app')

@section('title', 'Papan Pemantauan Pematuhan TPS')
@section('page_title', 'Papan Pemantauan Pematuhan TPS AM 6')
@section('page_description', 'Audit kendiri pematuhan tatacara pengurusan stor kerajaan mengikut Pekeliling Perbendaharaan Malaysia AM 6.1 hingga AM 6.10')

@section('content')
<div class="space-y-6">

    <!-- Overview Banner -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white p-6 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-amber-400 mb-1">
                <span>🛡️ AUDIT PEMATUHAN KERAJAAN</span>
                <span>•</span>
                <span>AKADEMI SAINS MALAYSIA</span>
            </div>
            <h2 class="text-xl font-bold">Status Indikator Pematuhan Tatacara Pengurusan Stor (TPS)</h2>
            <p class="text-xs text-blue-200 mt-1 max-w-2xl">
                Sistem menilai kepatuhan proses pengurusan stor secara masa nyata berpandukan AM 6.1 hingga AM 6.10 menggunakan indikator lampu isyarat (Traffic Light Compliance Indicator).
            </p>
        </div>
        <div class="flex items-center space-x-3 bg-white/10 p-3 rounded-xl border border-white/20">
            <div class="text-center px-3 border-r border-white/20">
                <div class="text-2xl font-black text-emerald-400">
                    {{ count(array_filter($checks, fn($c) => $c['status'] === 'PASS')) }}
                </div>
                <div class="text-[10px] uppercase font-bold text-slate-200">Patuh (Hijau)</div>
            </div>
            <div class="text-center px-3 border-r border-white/20">
                <div class="text-2xl font-black text-amber-400">
                    {{ count(array_filter($checks, fn($c) => $c['status'] === 'WARNING')) }}
                </div>
                <div class="text-[10px] uppercase font-bold text-slate-200">Perhatian (Kuning)</div>
            </div>
            <div class="text-center px-3">
                <div class="text-2xl font-black text-rose-400">
                    {{ count(array_filter($checks, fn($c) => $c['status'] === 'FAIL')) }}
                </div>
                <div class="text-[10px] uppercase font-bold text-slate-200">Tindakan (Merah)</div>
            </div>
        </div>
    </div>

    <!-- Traffic Light Checks Checklist -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($checks as $chk)
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-start space-x-4">
            <!-- Traffic Light Indicator Icon -->
            <div class="pt-0.5">
                @if($chk['status'] === 'PASS')
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-xl text-emerald-800 shadow-sm">
                        🟢
                    </div>
                @elseif($chk['status'] === 'WARNING')
                    <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-xl text-amber-800 shadow-sm">
                        🟡
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-300 flex items-center justify-center text-xl text-rose-800 shadow-sm">
                        🔴
                    </div>
                @endif
            </div>

            <!-- Check Content -->
            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $chk['code'] }}</span>
                    @if($chk['status'] === 'PASS')
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">PATUH</span>
                    @elseif($chk['status'] === 'WARNING')
                        <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">PERLU TINDAKAN</span>
                    @else
                        <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded">TIDAK PATUH</span>
                    @endif
                </div>

                <h3 class="text-sm font-bold text-slate-900 mt-1.5">{{ $chk['title'] }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $chk['description'] }}</p>

                <div class="mt-3 p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-xs text-slate-700 flex items-center space-x-2">
                    <span class="font-bold text-slate-500">Status Semasa:</span>
                    <span>{{ $chk['details'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
