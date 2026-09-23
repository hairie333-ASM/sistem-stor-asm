@extends('layouts.app')

@section('title', 'Pemeriksaan Keselamatan & Kebersihan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.7</span>
                <span class="text-xs text-slate-500">Rekod Pemeriksaan Keselamatan & Kebersihan</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Pemeriksaan {{ $safety->inspection_date }}</h1>
                @if($safety->status === 'PASS')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Memuaskan (Lulus)</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Tindakan Diperlukan</span>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('safety.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Kembali ke Senarai
            </a>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Stor & Kategori</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Stor:</span> <span class="font-bold text-slate-800">{{ $safety->store->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Kategori:</span> <span class="font-semibold text-slate-800">{{ $safety->category }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh:</span> <span class="text-slate-700">{{ $safety->inspection_date }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Pegawai Pemeriksa</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Nama:</span> <span class="font-bold text-slate-800">{{ $safety->inspector->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Jawatan:</span> <span class="text-slate-700">{{ $safety->inspector->position ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Bahagian:</span> <span class="text-slate-700">{{ $safety->inspector->department ?? '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-center items-center text-center">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Skor Pematuhan Keseluruhan</div>
            <div class="text-4xl font-extrabold font-mono {{ $safety->score >= 80 ? 'text-emerald-600' : ($safety->score >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                {{ $safety->score }}%
            </div>
            <div class="text-xs text-slate-500 mt-1">Penilaian Audit Dalaman AM 6.7</div>
        </div>
    </div>

    <!-- Checklist Results -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-800 pb-2 border-b border-slate-100">Status Semakan Senarai Pematuhan</h3>
        @if(is_array($safety->checklist_items) && count($safety->checklist_items) > 0)
        <div class="space-y-2">
            @foreach($safety->checklist_items as $key => $val)
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                <span class="text-sm font-medium text-slate-800 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                <span class="inline-flex items-center text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Patuh
                </span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-slate-400 italic">Tiada item senarai semak spesifik direkodkan.</p>
        @endif
    </div>

    <!-- Catatan Pemeriksaan -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-2">
        <h3 class="text-base font-bold text-slate-800 pb-2 border-b border-slate-100">Catatan Pegawai Pemeriksa & Tindakan</h3>
        <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100">
            {{ $safety->remarks ?: 'Tiada catatan tambahan.' }}
        </p>
    </div>
</div>
@endsection
