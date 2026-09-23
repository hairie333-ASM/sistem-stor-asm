@extends('layouts.print')

@section('title', 'Laporan Tahunan Pindahan Stok (KEW.PS-18)')
@section('form_code', 'KEW.PS-18')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN TAHUNAN PINDAHAN STOK (KEW.PS-18)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.8 - Tahun Kewangan: <span class="font-bold font-mono">{{ $year ?? date('Y') }}</span>)</p>
    </div>

    <div class="text-xs">
        <span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)
    </div>

    <!-- Transfers Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-8">Bil</th>
                <th class="border border-slate-300 p-2 w-32">No. Pindahan (KEW.PS-17)</th>
                <th class="border border-slate-300 p-2">Stor Asal (Pembekal)</th>
                <th class="border border-slate-300 p-2">Stor Penerima</th>
                <th class="border border-slate-300 p-2 text-center w-24">Bil. Item</th>
                <th class="border border-slate-300 p-2 text-right w-32">Jumlah Nilai (RM)</th>
                <th class="border border-slate-300 p-2 w-28">Tarikh Selesai</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @if(isset($transfers) && count($transfers) > 0)
                @foreach($transfers as $idx => $tr)
                @php
                    $trVal = $tr->items->sum(fn($i) => $i->received_quantity * ($i->stockItem->unit_price ?? 0));
                    $grandTotal += $trVal;
                @endphp
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $tr->transfer_number }}</td>
                    <td class="border border-slate-300 p-2">{{ $tr->sourceStore->name ?? '-' }}</td>
                    <td class="border border-slate-300 p-2">{{ $tr->destinationStore->name ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">{{ $tr->items->count() }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">{{ number_format($trVal, 2) }}</td>
                    <td class="border border-slate-300 p-2 font-mono text-[11px]">{{ $tr->received_at ? \Carbon\Carbon::parse($tr->received_at)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="7" class="p-4 text-center text-slate-400">Tiada rekod pindahan selesai bagi tahun ini.</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="bg-slate-50 font-bold border-t-2 border-slate-400">
                <td colspan="5" class="border border-slate-300 p-2 text-right uppercase">Jumlah Nilai Pindahan Tahunan:</td>
                <td class="border border-slate-300 p-2 text-right font-mono text-sm font-extrabold text-slate-900">RM {{ number_format($grandTotal, 2) }}</td>
                <td class="border border-slate-300 p-2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="pt-8 text-xs">
        <div>Disediakan Oleh:</div>
        <div class="h-16"></div>
        <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Pengurusan Stor</div>
        <div>Tarikh: {{ date('d/m/Y') }}</div>
    </div>
</div>
@endsection
