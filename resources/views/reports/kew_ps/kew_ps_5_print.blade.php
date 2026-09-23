@extends('layouts.print')

@section('title', 'Senarai Stok Kumpulan A dan B (KEW.PS-5)')
@section('form_code', 'KEW.PS-5')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SENARAI STOK KUMPULAN A DAN B</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.3 - Penentuan Mengikut Nilai Pembelian / Baki Semasa Tahun {{ date('Y') }})</p>
    </div>

    <div class="flex justify-between items-center text-xs">
        <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
        <div><span class="font-bold">Tarikh Cetakan:</span> {{ date('d/m/Y') }}</div>
    </div>

    <!-- Group A Table -->
    <div class="space-y-2">
        <h3 class="text-xs font-bold text-slate-900 uppercase">Kumpulan A (30% Nilai Pembelian / Baki Tertinggi)</h3>
        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2 w-32">No. Kod Stok</th>
                    <th class="border border-slate-300 p-2">Perihal Stok</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Kuantiti</th>
                    <th class="border border-slate-300 p-2 text-right w-24">Harga Seunit (RM)</th>
                    <th class="border border-slate-300 p-2 text-right w-28">Jumlah Nilai (RM)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $groupAItems = \App\Models\StockItem::where('stock_group', 'A')->orderBy('current_quantity', 'desc')->take(50)->get();
                    $totalA = 0;
                @endphp
                @forelse($groupAItems as $idx => $item)
                @php 
                    $val = $item->current_quantity * $item->unit_price;
                    $totalA += $val;
                @endphp
                <tr>
                    <td class="border border-slate-300 p-1.5 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-1.5 font-mono font-bold">{{ $item->stock_code }}</td>
                    <td class="border border-slate-300 p-1.5">{{ $item->description }}</td>
                    <td class="border border-slate-300 p-1.5 text-center">{{ number_format($item->current_quantity) }} {{ $item->uom->symbol ?? '' }}</td>
                    <td class="border border-slate-300 p-1.5 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="border border-slate-300 p-1.5 text-right font-mono font-bold">{{ number_format($val, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border border-slate-300 p-3 text-center text-slate-400">Tiada rekod Kumpulan A</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 font-bold border-t border-slate-400">
                    <td colspan="5" class="border border-slate-300 p-2 text-right">Jumlah Nilai Kumpulan A (RM):</td>
                    <td class="border border-slate-300 p-2 text-right font-mono text-emerald-800">{{ number_format($totalA, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signature Block -->
    <div class="grid grid-cols-2 gap-8 text-xs pt-8">
        <div class="border-t border-slate-400 pt-2 space-y-1">
            <div>Disediakan Oleh (Pegawai Stor):</div>
            <div class="font-bold">Ahmad Zulkifli (Pegawai Stor Kanan)</div>
            <div class="text-[10px] text-slate-500">Tarikh: {{ date('d/m/Y') }}</div>
        </div>
        <div class="border-t border-slate-400 pt-2 space-y-1">
            <div>Disahkan Oleh (Ketua Jabatan):</div>
            <div class="font-bold">Dr. Hazami Habib (CEO)</div>
            <div class="text-[10px] text-slate-500">Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
