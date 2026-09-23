@extends('layouts.app')

@section('title', 'Proses Akhir Tahun & Penutupan Baki')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.6 & AM 6.3</span>
                <span class="text-xs text-slate-500">Penutupan Tahun Kewangan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Proses Akhir Tahun & Pelarasan Baki Stor</h1>
            <p class="text-sm text-slate-600">Pelaksanaan penutupan tahun kewangan, membawa ke hadapan baki stok, penyelarasan paras stok 3-2-1 bulan, dan semakan tahunan Kumpulan A & B.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-semibold flex items-center">
        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
            Kitaran Automasi Akhir Tahun TPS Kerajaan
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
            Mengikut Pekeliling Perbendaharaan Malaysia AM 6.3 dan AM 6.6, pada setiap akhir tahun kewangan (31 Disember), pengurusan stor hendaklah menyempurnakan:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <div class="p-4 bg-blue-50/60 border border-blue-200 rounded-lg space-y-1">
                <div class="text-xs font-bold text-blue-900 uppercase">1. Baki Bawa Ke Hadapan</div>
                <div class="text-xs text-slate-600">Baki akhir kuantiti dan nilai pada 31 Disember dibawa ke hadapan sebagai baki awal 1 Januari tahun berikutnya secara automatik.</div>
            </div>

            <div class="p-4 bg-amber-50/60 border border-amber-200 rounded-lg space-y-1">
                <div class="text-xs font-bold text-amber-900 uppercase">2. Semakan Paras Stok 3-2-1</div>
                <div class="text-xs text-slate-600">Paras Maksimum (3 bulan), Menokok (2 bulan), dan Minimum (1 bulan) dikira semula berasaskan purata pengeluaran sebenar 12 bulan lalu.</div>
            </div>

            <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-lg space-y-1">
                <div class="text-xs font-bold text-emerald-900 uppercase">3. Semakan Kumpulan A/B</div>
                <div class="text-xs text-slate-600">Analisis 30% nilai tertinggi perolehan tahunan dikelaskan ke Kumpulan A dan 70% selebihnya ke Kumpulan B (KEW.PS-5).</div>
            </div>
        </div>
    </div>

    <!-- Execution Form -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-800">Laksanakan Penutupan Tahun Kewangan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Tindakan ini akan memproses kesemua {{ $totalItems }} item stok aktif di seluruh stor ASM.</p>
        </div>

        <form action="{{ route('admin.year-end.process') }}" method="POST" class="space-y-4">
            @csrf
            <div class="max-w-xs">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun Kewangan Ditutup <span class="text-rose-500">*</span></label>
                <select name="closing_year" class="w-full text-sm font-bold border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="{{ $prevYear }}">{{ $prevYear }} (Tahun Lepas)</option>
                    <option value="{{ $currentYear }}" selected>{{ $currentYear }} (Tahun Semasa)</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" onclick="return confirm('Adakah anda pasti mahu memproses penutupan akhir tahun bagi tahun kewangan yang dipilih? Tindakan ini akan mengemaskini parameter paras stok dan kumpulan item secara automatik.')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                    Jalankan Proses Akhir Tahun Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
