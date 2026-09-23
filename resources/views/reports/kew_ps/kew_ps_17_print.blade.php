@extends('layouts.print')

@section('title', 'Borang Pindahan Stok (KEW.PS-17)')
@section('form_code', 'KEW.PS-17')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG PINDAHAN STOK ANTARA STOR (KEW.PS-17)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.8 - No. Pindahan: <span class="font-mono font-bold">{{ $transfer->transfer_number ?? 'PS17/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="space-y-1">
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Asal (Pembekal):</span> {{ $transfer->sourceStore->name ?? '-' }}</div>
            <div><span class="font-bold">Stor Destinasi (Pemohon):</span> {{ $transfer->destinationStore->name ?? '-' }}</div>
            <div><span class="font-bold">Tujuan / Justifikasi Pindahan:</span> {{ $transfer->purpose ?? '-' }}</div>
        </div>
        <div class="space-y-1 text-right">
            <div><span class="font-bold">Tarikh Permohonan:</span> {{ $transfer->created_at ? $transfer->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><span class="font-bold">Tarikh Dikeluarkan:</span> {{ $transfer->dispatched_at ? \Carbon\Carbon::parse($transfer->dispatched_at)->format('d/m/Y') : '-' }}</div>
            <div><span class="font-bold">Tarikh Diterima:</span> {{ $transfer->received_at ? \Carbon\Carbon::parse($transfer->received_at)->format('d/m/Y') : '-' }}</div>
            <div><span class="font-bold">Status Pindahan:</span> {{ $transfer->status }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                <th class="border border-slate-300 p-2 w-28">No. Kod Stok</th>
                <th class="border border-slate-300 p-2">Perihal Barang / Stok</th>
                <th class="border border-slate-300 p-2 text-center w-16">Unit</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Dimohon</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Diluluskan</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Diterima</th>
                <th class="border border-slate-300 p-2">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($transfer->items) && count($transfer->items) > 0)
                @foreach($transfer->items as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ number_format($item->requested_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-blue-800">{{ number_format($item->approved_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-emerald-800">{{ number_format($item->received_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-slate-600">{{ $item->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="8" class="p-4 text-center text-slate-400">Tiada item dipaparkan.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- 4 Signatures Grid -->
    <div class="grid grid-cols-4 gap-4 pt-6 text-[11px]">
        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">1. Pemohon</div>
            <div class="h-12"></div>
            <div>Nama: {{ $transfer->requester->name ?? 'Pemohon' }}</div>
            <div>Tarikh: {{ $transfer->created_at ? $transfer->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
        </div>

        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">2. Pegawai Pelulus</div>
            <div class="h-12"></div>
            <div>Nama: {{ $transfer->approver->name ?? 'Pelulus' }}</div>
            <div>Tarikh: {{ $transfer->approved_at ? \Carbon\Carbon::parse($transfer->approved_at)->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">3. Pegawai Penghantar</div>
            <div class="h-12"></div>
            <div>Nama: {{ $transfer->sender->name ?? 'Stor Asal' }}</div>
            <div>Tarikh: {{ $transfer->dispatched_at ? \Carbon\Carbon::parse($transfer->dispatched_at)->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">4. Pegawai Penerima</div>
            <div class="h-12"></div>
            <div>Nama: {{ $transfer->receiver->name ?? 'Stor Destinasi' }}</div>
            <div>Tarikh: {{ $transfer->received_at ? \Carbon\Carbon::parse($transfer->received_at)->format('d/m/Y') : '-' }}</div>
        </div>
    </div>
</div>
@endsection
