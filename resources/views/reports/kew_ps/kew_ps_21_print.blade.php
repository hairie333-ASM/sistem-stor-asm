@extends('layouts.print')

@section('title', 'Surat Kelulusan Pelupusan Stok (KEW.PS-21)')
@section('form_code', 'KEW.PS-21')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SURAT KELULUSAN PELUPUSAN STOK (KEW.PS-21)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - No. Rujukan Kelulusan: <span class="font-mono font-bold">{{ $disposal->approval_reference ?? 'KEW.PS-21/ASM/2026/0001' }}</span>)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="flex justify-between">
            <div>
                <div>Kepada:</div>
                <div class="font-bold">Urus Setia Pelupusan / Pegawai Stor</div>
                <div>Akademi Sains Malaysia</div>
            </div>
            <div class="text-right">
                <div>Tarikh Surat: {{ $disposal->approved_at ? \Carbon\Carbon::parse($disposal->approved_at)->format('d F Y') : date('d F Y') }}</div>
            </div>
        </div>

        <div class="pt-2">
            <div class="font-bold uppercase text-[11px]">TUAN,</div>
            <div class="font-bold uppercase text-[12px] text-blue-950 mt-1">
                KELULUSAN PELUPUSAN STOK KERAJAAN DI BAWAH TATACARA PENGURUSAN STOR (TPS AM 6.9)
            </div>
        </div>

        <p>
            Dengan hormatnya saya merujuk kepada Laporan Lembaga Pemeriksa Pelupusan Stok (KEW.PS-20) No. Rujukan <strong>{{ $disposal->disposal_number }}</strong>.
        </p>

        <p>
            2. Sukacita dimaklumkan bahawa permohonan pelupusan stok berikut telah <strong>DILULUSKAN</strong> oleh Kuasa Melulus:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Nama Stor: {{ $disposal->store->name ?? 'Stor Utama' }}</div>
            <div>Bilangan Item: {{ $disposal->items->count() }} item</div>
            <div>Nilai Perolehan Asal: RM {{ number_format($disposal->total_original_value ?? 0, 2) }}</div>
            <div>Kaedah Pelupusan Diluluskan: <span class="text-blue-900 font-bold uppercase">{{ $disposal->disposal_method }}</span></div>
        </div>

        <p>
            3. Tindakan pelupusan fizikal hendaklah disempurnakan dalam tempoh tiga (3) bulan dari tarikh surat ini. Sijil Pelupusan Stok (KEW.PS-22) hendaklah disediakan dan dikemukakan setelah tindakan pelupusan selesai disaksikan.
        </p>

        <p>
            Sekian, terima kasih.
        </p>

        <div class="pt-8">
            <div class="font-bold">"BERKHIDMAT UNTUK NEGARA"</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Kuasa Melulus Pelupusan</div>
            <div class="font-bold text-slate-800">{{ $disposal->approver->name ?? 'Ketua Pegawai Eksekutif' }}</div>
            <div>Akademi Sains Malaysia</div>
        </div>
    </div>
</div>
@endsection
