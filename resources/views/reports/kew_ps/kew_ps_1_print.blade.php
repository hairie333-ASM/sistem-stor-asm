@extends('layouts.print')

@section('title', 'Borang Terimaan Barang-Barang (BTB)')
@section('form_code', 'KEW.PS-1')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG TERIMAAN BARANG-BARANG (BTB)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.2 - No. Rujukan: <span class="font-mono font-bold">{{ $receiving->btb_number ?? 'BTB/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="space-y-1">
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Penerima:</span> {{ $receiving->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Nama Pembekal:</span> {{ $receiving->supplier_name ?? 'Pembekal Sah' }}</div>
            <div><span class="font-bold">Alamat Pembekal:</span> {{ $receiving->supplier_address ?? 'Kuala Lumpur' }}</div>
        </div>
        <div class="space-y-1 text-right">
            <div><span class="font-bold">Tarikh Terimaan:</span> {{ $receiving->received_date ? \Carbon\Carbon::parse($receiving->received_date)->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><span class="font-bold">No. Pesanan Kerajaan (PO):</span> {{ $receiving->purchase_order_number ?? '-' }}</div>
            <div><span class="font-bold">No. Nota Hantaran (DO):</span> {{ $receiving->delivery_order_number ?? '-' }}</div>
            <div><span class="font-bold">No. Invois:</span> {{ $receiving->invoice_number ?? '-' }}</div>
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
                <th class="border border-slate-300 p-2 text-center w-20">Kuantiti Dipesan</th>
                <th class="border border-slate-300 p-2 text-center w-20">Kuantiti Dihantar</th>
                <th class="border border-slate-300 p-2 text-center w-20">Kuantiti Diterima</th>
                <th class="border border-slate-300 p-2 text-right w-24">Harga Seunit (RM)</th>
                <th class="border border-slate-300 p-2 text-right w-24">Jumlah (RM)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @if(isset($receiving->items) && count($receiving->items) > 0)
                @foreach($receiving->items as $idx => $item)
                @php 
                    $sub = $item->accepted_quantity * $item->unit_price;
                    $total += $sub;
                @endphp
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ number_format($item->ordered_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ number_format($item->delivered_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold text-emerald-800">{{ number_format($item->accepted_quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono font-bold">{{ number_format($sub, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="9" class="p-4 text-center text-slate-400">Tiada item dipaparkan.</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="bg-slate-50 font-bold border-t border-slate-400">
                <td colspan="8" class="border border-slate-300 p-2 text-right uppercase">Jumlah Nilai Terimaan (RM):</td>
                <td class="border border-slate-300 p-2 text-right font-mono text-sm font-bold text-slate-900">RM {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Perakuan Pegawai Penerima & Teknikal -->
    <div class="grid grid-cols-2 gap-8 pt-6 text-xs">
        <div class="border border-slate-300 p-4 rounded space-y-3">
            <div class="font-bold uppercase text-[11px] border-b border-slate-200 pb-1">Perakuan Pegawai Penerima</div>
            <p class="text-[11px] text-slate-600 leading-relaxed">
                Diperakui bahawa barang-barang yang disenaraikan di atas telah diperiksa kuantiti dan keadaannya secara fizikal serta diterima dengan sempurna.
            </p>
            <div class="pt-6">
                <div>Tandatangan: .................................................</div>
                <div class="mt-1 font-bold">Nama: {{ $receiving->receivingOfficer->name ?? 'Pegawai Penerima' }}</div>
                <div>Jawatan: {{ $receiving->receivingOfficer->position ?? 'Pegawai Stor' }}</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>

        <div class="border border-slate-300 p-4 rounded space-y-3">
            <div class="font-bold uppercase text-[11px] border-b border-slate-200 pb-1">Perakuan Pegawai Teknikal (Jika Berkaitan)</div>
            <p class="text-[11px] text-slate-600 leading-relaxed">
                Diperakui bahawa barang-barang teknikal di atas telah diuji fungsinya, mematuhi spesifikasi kontrak dan berkeadaan baik sepenuhnya.
            </p>
            <div class="pt-6">
                <div>Tandatangan: .................................................</div>
                <div class="mt-1 font-bold">Nama: {{ $receiving->technicalOfficer->name ?? 'Pegawai Teknikal ASM' }}</div>
                <div>Jawatan: {{ $receiving->technicalOfficer->position ?? 'Pegawai Penyelidik' }}</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
