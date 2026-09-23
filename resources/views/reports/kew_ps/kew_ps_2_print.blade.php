@extends('layouts.print')

@section('title', 'Borang Penolakan Barang-Barang (BPB)')
@section('form_code', 'KEW.PS-2')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG PENOLAKAN BARANG-BARANG (BPB)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.2 - No. Rujukan: <span class="font-mono font-bold">{{ $rejection->bpb_number ?? 'BPB/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="space-y-1">
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Terlibat:</span> {{ $rejection->receiving->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Nama Pembekal:</span> {{ $rejection->supplier_name ?? '-' }}</div>
        </div>
        <div class="space-y-1 text-right">
            <div><span class="font-bold">Tarikh Penolakan:</span> {{ $rejection->rejection_date ? \Carbon\Carbon::parse($rejection->rejection_date)->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><span class="font-bold">No. Nota Hantaran (DO):</span> {{ $rejection->delivery_order_number ?? '-' }}</div>
            <div><span class="font-bold">No. BTB Asal:</span> {{ $rejection->receiving->btb_number ?? '-' }}</div>
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
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Dihantar</th>
                <th class="border border-slate-300 p-2 text-center w-24">Kuantiti Ditolak</th>
                <th class="border border-slate-300 p-2">Sebab-Sebab Penolakan</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($rejection->items) && count($rejection->items) > 0)
                @foreach($rejection->items as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ number_format($item->delivered_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-rose-800">{{ number_format($item->rejected_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-slate-700 font-medium">{{ $item->rejection_reason }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="7" class="p-4 text-center text-slate-400">Tiada item dipaparkan.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- Perakuan Pegawai Penerima & Akuan Pembekal -->
    <div class="grid grid-cols-2 gap-8 pt-6 text-xs">
        <div class="border border-slate-300 p-4 rounded space-y-3">
            <div class="font-bold uppercase text-[11px] border-b border-slate-200 pb-1">Dikeluarkan Oleh Pegawai Penerima</div>
            <p class="text-[11px] text-slate-600 leading-relaxed">
                Barang-barang di atas telah ditolak kerana tidak menepati spesifikasi pesanan, rosak atau cacat semasa penghantaran.
            </p>
            <div class="pt-6">
                <div>Tandatangan: .................................................</div>
                <div class="mt-1 font-bold">Nama: {{ $rejection->officer->name ?? 'Pegawai Penerima' }}</div>
                <div>Jawatan: {{ $rejection->officer->position ?? 'Pegawai Stor' }}</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>

        <div class="border border-slate-300 p-4 rounded space-y-3">
            <div class="font-bold uppercase text-[11px] border-b border-slate-200 pb-1">Akuan Penerimaan Pembekal / Penghantar</div>
            <p class="text-[11px] text-slate-600 leading-relaxed">
                Saya mengakui telah menerima kembali barang-barang yang ditolak seperti yang dinyatakan di atas untuk tindakan penggantian.
            </p>
            <div class="pt-6">
                <div>Tandatangan: .................................................</div>
                <div class="mt-1 font-bold">Nama Wakil: ...........................................</div>
                <div>No. K/P: .....................................................</div>
                <div>Tarikh: ......................................................</div>
            </div>
        </div>
    </div>
</div>
@endsection
