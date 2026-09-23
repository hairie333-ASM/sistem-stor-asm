@extends('layouts.print')

@section('title', 'Laporan Verifikasi Stor (KEW.PS-12)')
@section('form_code', 'KEW.PS-12')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN VERIFIKASI STOR (KEW.PS-12)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.6 - No. Laporan: <span class="font-mono font-bold">{{ $verification->report_number ?? 'KEW.PS-12/ASM/2026/001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div>
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Diperiksa:</span> {{ $verification->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Tahun Verifikasi:</span> {{ $verification->year ?? date('Y') }}</div>
        </div>
        <div class="text-right">
            <div><span class="font-bold">Tarikh Mula:</span> {{ $verification->start_date ?? date('d/m/Y') }}</div>
            <div><span class="font-bold">Tarikh Tamat:</span> {{ $verification->end_date ?? date('d/m/Y') }}</div>
            <div><span class="font-bold">Status:</span> {{ $verification->status }}</div>
        </div>
    </div>

    <!-- Verification Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-[10px]">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[9px]">
                <th class="border border-slate-300 p-1 text-center w-8">Bil</th>
                <th class="border border-slate-300 p-1 w-24">No. Kod</th>
                <th class="border border-slate-300 p-1">Perihal Stok</th>
                <th class="border border-slate-300 p-1 text-center w-12">Unit</th>
                <th class="border border-slate-300 p-1 text-center w-14">Baki Buku</th>
                <th class="border border-slate-300 p-1 text-center w-14">Kiraan Fizikal</th>
                <th class="border border-slate-300 p-1 text-center w-12">Lebihan</th>
                <th class="border border-slate-300 p-1 text-center w-12">Kurangan</th>
                <th class="border border-slate-300 p-1 text-right w-16">Harga (RM)</th>
                <th class="border border-slate-300 p-1 text-right w-16">Nilai Selisih</th>
                <th class="border border-slate-300 p-1 text-center w-16">Keadaan</th>
                <th class="border border-slate-300 p-1">Catatan / Sebab</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($verification->items) && count($verification->items) > 0)
                @foreach($verification->items as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-1 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-1 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-1 font-medium">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-1 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-1 text-center font-mono">{{ number_format($item->system_quantity) }}</td>
                    <td class="border border-slate-300 p-1 text-center font-mono font-bold">{{ number_format($item->physical_quantity) }}</td>
                    <td class="border border-slate-300 p-1 text-center font-mono font-bold text-emerald-800">{{ $item->surplus_quantity > 0 ? '+' . number_format($item->surplus_quantity) : '-' }}</td>
                    <td class="border border-slate-300 p-1 text-center font-mono font-bold text-rose-800">{{ $item->shortage_quantity > 0 ? '-' . number_format($item->shortage_quantity) : '-' }}</td>
                    <td class="border border-slate-300 p-1 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="border border-slate-300 p-1 text-right font-mono font-bold">{{ number_format($item->variance_value, 2) }}</td>
                    <td class="border border-slate-300 p-1 text-center font-semibold">{{ $item->condition_status }}</td>
                    <td class="border border-slate-300 p-1 text-[9px]">{{ $item->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="12" class="p-3 text-center text-slate-400">Tiada rekod item verifikasi.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- Ringkasan Dapatan -->
    <div class="border border-slate-300 p-3 rounded text-xs space-y-2">
        <div><span class="font-bold uppercase text-[10px]">Ringkasan Dapatan:</span> {{ $verification->findings_summary ?: 'Semua pemeriksaan dijalankan mengikut Tatacara Pengurusan Stor AM 6.6.' }}</div>
        <div><span class="font-bold uppercase text-[10px]">Syor / Cadangan Pembetulan:</span> {{ $verification->corrective_actions ?: 'Tiada tindakan pembetulan mendesak.' }}</div>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 gap-8 pt-4 text-xs">
        <div class="space-y-4">
            <div>Pegawai Pemverifikasi 1:</div>
            <div class="h-10"></div>
            <div>Tandatangan: .................................................</div>
            <div class="font-bold">Nama: {{ $verification->verifier1->name ?? 'Pemverifikasi 1' }}</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>

        <div class="space-y-4">
            <div>Pegawai Pemverifikasi 2:</div>
            <div class="h-10"></div>
            <div>Tandatangan: .................................................</div>
            <div class="font-bold">Nama: {{ $verification->verifier2->name ?? 'Pemverifikasi 2' }}</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
