@extends('layouts.print')

@section('title', 'Borang Pesanan Stok Antara Stor (KEW.PS-7)')
@section('form_code', 'KEW.PS-7')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG PESANAN PENGELUARAN STOK (ANTARA STOR)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.5 - No. Pesanan: <span class="font-mono font-bold">{{ $stockRequest->request_number ?? 'PS7/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="space-y-1">
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Pembekal:</span> {{ $stockRequest->store->name ?? 'Stor Pusat/Utama' }}</div>
            <div><span class="font-bold">Stor Pemesan:</span> {{ $stockRequest->requestingStore->name ?? 'Stor Cawangan/Unit' }}</div>
            <div><span class="font-bold">Tujuan Pesanan:</span> {{ $stockRequest->purpose ?? '-' }}</div>
        </div>
        <div class="space-y-1 text-right">
            <div><span class="font-bold">Tarikh Dipesan:</span> {{ $stockRequest->created_at ? $stockRequest->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><span class="font-bold">Pegawai Pemesan:</span> {{ $stockRequest->requester->name ?? '-' }}</div>
            <div><span class="font-bold">Pegawai Pelulus:</span> {{ $stockRequest->approver->name ?? '-' }}</div>
            <div><span class="font-bold">Status:</span> {{ $stockRequest->status ?? 'COMPLETED' }}</div>
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
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Dipesan</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Diluluskan</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Dikeluarkan</th>
                <th class="border border-slate-300 p-2">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($stockRequest->items) && count($stockRequest->items) > 0)
                @foreach($stockRequest->items as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ number_format($item->requested_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-blue-800">{{ number_format($item->approved_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-emerald-800">{{ number_format($item->issued_quantity) }}</td>
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
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">1. Pemesan</div>
            <div class="h-12"></div>
            <div>Nama: {{ $stockRequest->requester->name ?? 'Pemesan' }}</div>
            <div>Tarikh: {{ $stockRequest->created_at ? $stockRequest->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
        </div>

        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">2. Pelulus</div>
            <div class="h-12"></div>
            <div>Nama: {{ $stockRequest->approver->name ?? 'Pelulus' }}</div>
            <div>Tarikh: {{ $stockRequest->approved_at ? \Carbon\Carbon::parse($stockRequest->approved_at)->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">3. Pengeluar</div>
            <div class="h-12"></div>
            <div>Nama: {{ $stockRequest->issuer->name ?? 'Pegawai Stor' }}</div>
            <div>Tarikh: {{ $stockRequest->issued_at ? \Carbon\Carbon::parse($stockRequest->issued_at)->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="border border-slate-300 p-2.5 rounded">
            <div class="font-bold border-b border-slate-200 pb-1 mb-2">4. Penerima</div>
            <div class="h-12"></div>
            <div>Nama: {{ $stockRequest->recipient->name ?? 'Penerima' }}</div>
            <div>Tarikh: {{ $stockRequest->received_at ? \Carbon\Carbon::parse($stockRequest->received_at)->format('d/m/Y') : '-' }}</div>
        </div>
    </div>
</div>
@endsection
