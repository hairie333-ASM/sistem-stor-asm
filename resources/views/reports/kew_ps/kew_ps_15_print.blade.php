@extends('layouts.print')

@section('title', 'Laporan Pelarasan Stok (KEW.PS-15)')
@section('form_code', 'KEW.PS-15')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN PELARASAN STOK (KEW.PS-15)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.6 - No. Rujukan: <span class="font-mono font-bold">{{ $adjustment->adjustment_number ?? 'PS15/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div>
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Terlibat:</span> {{ $adjustment->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Punca / Sebab:</span> {{ $adjustment->reason ?? 'Verifikasi Tahunan' }}</div>
        </div>
        <div class="text-right">
            <div><span class="font-bold">Tarikh Permohonan:</span> {{ $adjustment->created_at ? $adjustment->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><span class="font-bold">Pegawai Pemohon:</span> {{ $adjustment->requester->name ?? '-' }}</div>
            <div><span class="font-bold">Status:</span> {{ $adjustment->status }}</div>
        </div>
    </div>

    <!-- Discrepancy Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                <th class="border border-slate-300 p-2 w-28">No. Kod Stok</th>
                <th class="border border-slate-300 p-2">Perihal Barang / Stok</th>
                <th class="border border-slate-300 p-2 text-center w-16">Unit</th>
                <th class="border border-slate-300 p-2 text-center w-20">Kuantiti Buku</th>
                <th class="border border-slate-300 p-2 text-center w-24">Pelarasan (+/-)</th>
                <th class="border border-slate-300 p-2 text-center w-20">Baki Baru</th>
                <th class="border border-slate-300 p-2 text-right w-24">Harga Seunit (RM)</th>
                <th class="border border-slate-300 p-2 text-right w-24">Nilai Selisih (RM)</th>
                <th class="border border-slate-300 p-2">Sebab Selisih</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($adjustment->items) && count($adjustment->items) > 0)
                @foreach($adjustment->items as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono">{{ number_format($item->current_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono font-bold {{ $item->adjustment_quantity >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                        {{ $item->adjustment_quantity > 0 ? '+' : '' }}{{ number_format($item->adjustment_quantity) }}
                    </td>
                    <td class="border border-slate-300 p-2 text-center font-mono font-bold">{{ number_format($item->new_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono font-bold">{{ number_format($item->total_variance_value, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-slate-600">{{ $item->reason_detail ?: '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="10" class="p-4 text-center text-slate-400">Tiada item pelarasan dipaparkan.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-8 pt-8 text-xs">
        <div>
            <div>Disediakan Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Stor</div>
            <div>Nama: {{ $adjustment->requester->name ?? '-' }}</div>
            <div>Tarikh: {{ $adjustment->created_at ? $adjustment->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
        </div>
        <div class="text-right">
            <div>Disemak Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Unit / Bahagian</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
