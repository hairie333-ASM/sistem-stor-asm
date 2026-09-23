@extends('layouts.print')

@section('title', 'Katalog Stok Induk Kerajaan')
@section('form_code', 'KATALOG-STOK')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-base font-bold uppercase tracking-wider text-slate-900">Katalog Lengkap Stok Kerajaan (TPS AM 6.1)</h2>
        <p class="text-xs text-slate-600">Akademi Sains Malaysia (ASM) &bull; Tarikh Janaan: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-1.5 text-center w-8">Bil</th>
                <th class="border border-slate-300 p-1.5 w-24">No. Kod</th>
                <th class="border border-slate-300 p-1.5 w-20">No. Kad</th>
                <th class="border border-slate-300 p-1.5">Perihal Stok</th>
                <th class="border border-slate-300 p-1.5 w-24">Kategori</th>
                <th class="border border-slate-300 p-1.5 text-center w-12">Kump.</th>
                <th class="border border-slate-300 p-1.5 text-center w-14">Unit</th>
                <th class="border border-slate-300 p-1.5 w-20">Lokasi</th>
                <th class="border border-slate-300 p-1.5 text-right w-20">Harga (RM)</th>
                <th class="border border-slate-300 p-1.5 text-right w-16">Baki</th>
                <th class="border border-slate-300 p-1.5 text-right w-20">Jumlah (RM)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalVal = 0; @endphp
            @foreach($items as $idx => $item)
            @php 
                $val = $item->current_quantity * $item->unit_price;
                $totalVal += $val;
            @endphp
            <tr class="hover:bg-slate-50">
                <td class="border border-slate-300 p-1.5 text-center">{{ $idx + 1 }}</td>
                <td class="border border-slate-300 p-1.5 font-mono font-bold">{{ $item->item_code }}</td>
                <td class="border border-slate-300 p-1.5 font-mono">{{ $item->card_number ?? '-' }}</td>
                <td class="border border-slate-300 p-1.5 font-semibold text-slate-800">{{ $item->description }}</td>
                <td class="border border-slate-300 p-1.5 text-[11px]">{{ $item->category->name ?? '-' }}</td>
                <td class="border border-slate-300 p-1.5 text-center font-bold">{{ $item->stock_group }}</td>
                <td class="border border-slate-300 p-1.5 text-center">{{ $item->uom->code ?? '' }}</td>
                <td class="border border-slate-300 p-1.5 font-mono text-[11px]">{{ $item->defaultLocation->code ?? '-' }}</td>
                <td class="border border-slate-300 p-1.5 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                <td class="border border-slate-300 p-1.5 text-right font-bold">{{ number_format($item->current_quantity) }}</td>
                <td class="border border-slate-300 p-1.5 text-right font-mono font-bold">{{ number_format($val, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-slate-100 font-bold border-t-2 border-slate-400">
                <td colspan="10" class="border border-slate-300 p-2 text-right uppercase text-[11px]">Jumlah Keseluruhan Nilai Stok Semasa:</td>
                <td class="border border-slate-300 p-2 text-right font-mono text-sm">RM {{ number_format($totalVal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="pt-6 grid grid-cols-2 text-xs">
        <div>
            <div>Disediakan Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Stor</div>
            <div class="text-slate-500">Tarikh: {{ date('d/m/Y') }}</div>
        </div>
        <div class="text-right">
            <div>Disahkan Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan</div>
            <div class="text-slate-500">Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
