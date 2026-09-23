@extends('layouts.print')

@section('title', 'Sijil Pelupusan Stok (KEW.PS-22)')
@section('form_code', 'KEW.PS-22')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto border-2 border-slate-700 p-8 rounded-lg mt-4">
    <div class="text-center pb-4 border-b-2 border-slate-600 space-y-1">
        <h2 class="text-base font-extrabold uppercase tracking-widest text-slate-900">AKADEMI SAINS MALAYSIA</h2>
        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-950">SIJIL PELUPUSAN STOK</h3>
        <p class="text-xs font-mono font-bold text-slate-700">NO. SIJIL: {{ $disposal->witness_cert_number ?? 'KEW.PS-22/ASM/2026/0001' }}</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <p class="text-justify">
            Saya mengesahkan bahawa pelupusan fizikal stok kerajaan di bawah Surat Kelulusan Pelupusan Rujukan: <strong class="font-mono">{{ $disposal->approval_reference ?? '-' }}</strong> bertarikh <strong>{{ $disposal->approved_at ? \Carbon\Carbon::parse($disposal->approved_at)->format('d/m/Y') : '-' }}</strong> telah dilaksanakan seperti ketetapan berikut:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Stor Terlibat: {{ $disposal->store->name ?? '-' }}</div>
            <div>Kaedah Pelupusan Dilaksanakan: <span class="uppercase font-bold text-blue-900">{{ $disposal->disposal_method }}</span></div>
            <div>Bilangan Item Dilupuskan: {{ $disposal->items->count() }} item</div>
            <div>Nilai Perolehan Asal Keseluruhan: RM {{ number_format($disposal->total_original_value ?? 0, 2) }}</div>
            <div>Hasil Jualan Diperoleh (Jika Ada): <span class="font-mono text-emerald-800 font-bold">RM {{ number_format($disposal->total_revenue ?? 0, 2) }}</span></div>
            <div>Tarikh Tindakan Fizikal Sempurna: {{ $disposal->completed_at ? \Carbon\Carbon::parse($disposal->completed_at)->format('d F Y') : date('d F Y') }}</div>
        </div>

        <p class="text-justify">
            Semua rekod stok di dalam Daftar Stok (KEW.PS-3 Bahagian B) dan Kad Petak (KEW.PS-4) telah dikemaskini dan dikeluarkan daripada baki fizikal dan sistem secara kekal.
        </p>

        <div class="pt-8 grid grid-cols-2 gap-8">
            <div>
                <div>Disediakan Oleh Pegawai Stor:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Pengurusan Stor</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>

            <div class="text-right">
                <div>Disaksikan Oleh (Pegawai Saksi Bebas):</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Saksi Pelupusan</div>
                <div>Nama: .................................................</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
