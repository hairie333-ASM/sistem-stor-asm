@extends('layouts.app')

@section('title', 'Tetapan Sistem & Penomboran Borang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-slate-100 text-slate-800 border border-slate-300">Konfigurasi Pentadbir</span>
                <span class="text-xs text-slate-500">TPS AM 6.1 – AM 6.10</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Tetapan Sistem & Parameter Penomboran</h1>
            <p class="text-sm text-slate-600">Konfigurasi awalan nombor borang rasmi KEW.PS, sasaran KPI pusingan stok, dan profil agensi.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-semibold flex items-center">
        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Profil Organisasi & KPI -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                Profil Agensi & Sasaran TPS
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Agensi / Jabatan</label>
                    <input type="text" name="organization_name" value="{{ $settings['organization_name'] ?? 'Akademi Sains Malaysia (ASM)' }}" class="w-full text-sm border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sasaran Kadar Pusingan Stok Tahunan (KPS)</label>
                    <input type="number" step="0.1" name="store_turnover_target" value="{{ $settings['store_turnover_target'] ?? '4.0' }}" class="w-full text-sm border-slate-300 rounded-lg font-mono font-bold">
                    <p class="text-xs text-slate-500 mt-1">Piawaian minimum Perbendaharaan Malaysia adalah 4.0 kali setahun.</p>
                </div>
            </div>
        </div>

        <!-- Format Penomboran Borang Rasmi KEW.PS -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>
                Format Awalan Siri Penomboran Borang (Prefixes)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">BTB (KEW.PS-1)</label>
                    <input type="text" name="prefix_btb" value="{{ $settings['prefix_btb'] ?? 'BTB/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">BPB (KEW.PS-2)</label>
                    <input type="text" name="prefix_bpb" value="{{ $settings['prefix_bpb'] ?? 'BPB/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pesanan Antara Stor (KEW.PS-7)</label>
                    <input type="text" name="prefix_ps7" value="{{ $settings['prefix_ps7'] ?? 'PS7/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pesanan Individu (KEW.PS-8)</label>
                    <input type="text" name="prefix_ps8" value="{{ $settings['prefix_ps8'] ?? 'PS8/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pembungkusan (KEW.PS-9)</label>
                    <input type="text" name="prefix_ps9" value="{{ $settings['prefix_ps9'] ?? 'PS9/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Verifikasi Stor (KEW.PS-11)</label>
                    <input type="text" name="prefix_ps11" value="{{ $settings['prefix_ps11'] ?? 'PS11/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pelarasan Stok (KEW.PS-15)</label>
                    <input type="text" name="prefix_ps15" value="{{ $settings['prefix_ps15'] ?? 'PS15/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pindahan Stok (KEW.PS-17)</label>
                    <input type="text" name="prefix_ps17" value="{{ $settings['prefix_ps17'] ?? 'PS17/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pelupusan Stok (KEW.PS-20)</label>
                    <input type="text" name="prefix_ps20" value="{{ $settings['prefix_ps20'] ?? 'PS20/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kehilangan Stok (KEW.PS-32)</label>
                    <input type="text" name="prefix_ps32" value="{{ $settings['prefix_ps32'] ?? 'PS32/ASM' }}" class="w-full text-xs font-mono border-slate-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Simpan Tetapan Sistem
            </button>
        </div>
    </form>
</div>
@endsection
