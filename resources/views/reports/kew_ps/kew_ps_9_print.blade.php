@extends('layouts.print')

@section('title', 'Borang Pembungkusan Stok (KEW.PS-9)')
@section('form_code', 'KEW.PS-9')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG PEMBUNGKUSAN STOK (KEW.PS-9)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.5 - No. Pembungkusan: <span class="font-mono font-bold">{{ $packing->packing_number ?? 'PS9/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="space-y-1">
            <div><span class="font-bold">Pengirim:</span> {{ $packing->sender_name ?? 'Akademi Sains Malaysia (Stor Utama)' }}</div>
            <div><span class="font-bold">Penerima:</span> {{ $packing->receiver_name ?? '-' }}</div>
            <div><span class="font-bold">Alamat Penghantaran:</span> {{ $packing->delivery_address ?? '-' }}</div>
        </div>
        <div class="space-y-1 text-right">
            <div><span class="font-bold">No. Bungkusan:</span> {{ $packing->package_number ?? 'BKG-01' }}</div>
            <div><span class="font-bold">Jenis Bungkusan:</span> {{ $packing->package_type ?? 'Kotak Kadbod' }}</div>
            <div><span class="font-bold">Berat:</span> {{ $packing->weight_kg ? $packing->weight_kg . ' KG' : '-' }}</div>
            <div><span class="font-bold">Dimensi:</span> {{ $packing->dimensions ?? '-' }}</div>
        </div>
    </div>

    <!-- Handling Instructions -->
    @if(is_array($packing->handling_instructions) && count($packing->handling_instructions) > 0)
    <div class="p-2.5 bg-amber-50 border border-amber-200 rounded text-xs text-amber-900">
        <span class="font-bold uppercase tracking-wider text-[10px]">Arahan Khas Pengendalian:</span>
        <div class="flex flex-wrap gap-2 mt-1">
            @foreach($packing->handling_instructions as $instr)
                <span class="px-2 py-0.5 bg-amber-200 text-amber-900 rounded font-semibold text-[11px]">{{ $instr }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Items Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                <th class="border border-slate-300 p-2 w-28">No. Kod Stok</th>
                <th class="border border-slate-300 p-2">Perihal Barang / Stok</th>
                <th class="border border-slate-300 p-2 text-center w-16">Unit</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Dibungkus</th>
                <th class="border border-slate-300 p-2">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($packing->stockRequest->items) && count($packing->stockRequest->items) > 0)
                @foreach($packing->stockRequest->items as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">{{ number_format($item->approved_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-slate-600">{{ $item->remarks ?: '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="6" class="p-4 text-center text-slate-400">Tiada item dipaparkan.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-8 pt-6 text-xs">
        <div>
            <div>Dibungkus & Disediakan Oleh:</div>
            <div class="h-14"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Pembungkus Stor</div>
            <div class="text-slate-500">Nama: {{ $packing->packingOfficer->name ?? 'Pegawai Stor ASM' }}</div>
            <div class="text-slate-500">Tarikh: {{ $packing->created_at ? $packing->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
        </div>
        <div class="text-right">
            <div>Diterima & Disemak Oleh:</div>
            <div class="h-14"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Penerima / Syarikat Penghantar</div>
            <div class="text-slate-500">Nama: .................................................</div>
            <div class="text-slate-500">Tarikh: .................................................</div>
        </div>
    </div>
</div>
@endsection
