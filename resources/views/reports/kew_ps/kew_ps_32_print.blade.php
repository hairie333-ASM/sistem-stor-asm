@extends('layouts.print')

@section('title', 'Laporan Awal Kehilangan Stok (KEW.PS-32)')
@section('form_code', 'KEW.PS-32')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN AWAL KEHILANGAN STOK KERAJAAN (KEW.PS-32)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.10 - No. Rujukan Kes: <span class="font-mono font-bold">{{ $lossCase->case_number ?? 'PS32/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div>
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Terlibat:</span> {{ $lossCase->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Tarikh Kejadian (Dianggarkan):</span> {{ $lossCase->incident_date }}</div>
            <div><span class="font-bold">Tarikh Dikesan / Diketahui:</span> {{ $lossCase->discovery_date }}</div>
        </div>
        <div class="text-right">
            <div><span class="font-bold">No. Laporan Polis:</span> <span class="font-mono font-bold">{{ $lossCase->police_report_no ?: 'Tiada (Siasatan Dalaman)' }}</span></div>
            <div><span class="font-bold">Tarikh Laporan Polis:</span> {{ $lossCase->police_report_date ?: '-' }}</div>
            <div><span class="font-bold">Status Tindakan Polis:</span> {{ $lossCase->police_action_status ?: '-' }}</div>
            <div><span class="font-bold">Jumlah Nilai Anggaran:</span> <span class="font-mono font-bold text-rose-800">RM {{ number_format($lossCase->total_loss_value ?? 0, 2) }}</span></div>
        </div>
    </div>

    <!-- Description -->
    <div class="border border-slate-300 p-3 rounded text-xs space-y-1">
        <div class="font-bold uppercase text-[10px] text-slate-800">Perihal Ringkas Kejadian Kehilangan:</div>
        <p class="text-slate-700 italic leading-relaxed">
            "{{ $lossCase->description }}"
        </p>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-8">Bil</th>
                <th class="border border-slate-300 p-2 w-28">No. Kod Stok</th>
                <th class="border border-slate-300 p-2">Perihal Barang / Stok</th>
                <th class="border border-slate-300 p-2 text-center w-14">Unit</th>
                <th class="border border-slate-300 p-2 text-center w-20">Kuantiti Hilang</th>
                <th class="border border-slate-300 p-2 text-right w-28">Harga Seunit (RM)</th>
                <th class="border border-slate-300 p-2 text-right w-28">Nilai Kehilangan (RM)</th>
                <th class="border border-slate-300 p-2">Catatan Keadaan</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @if(isset($lossCase->items) && count($lossCase->items) > 0)
                @foreach($lossCase->items as $idx => $item)
                @php $total += $item->total_value; @endphp
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-rose-800 font-mono">{{ number_format($item->quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono font-bold">{{ number_format($item->total_value, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-[11px]">{{ $item->circumstances ?: '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="8" class="p-4 text-center text-slate-400">Tiada item dipaparkan.</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="bg-slate-50 font-bold border-t border-slate-400">
                <td colspan="6" class="border border-slate-300 p-2 text-right uppercase">Jumlah Nilai Keseluruhan Kehilangan:</td>
                <td class="border border-slate-300 p-2 text-right font-mono text-sm font-bold text-rose-800">RM {{ number_format($total, 2) }}</td>
                <td class="border border-slate-300 p-2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="grid grid-cols-2 gap-8 pt-6 text-xs">
        <div>
            <div>Disediakan Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Yang Menemui Kehilangan</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
        <div class="text-right">
            <div>Dikemukakan Kepada Pegawai Pengawal:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
