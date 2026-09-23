@extends('layouts.print')

@section('title', 'Daftar Stok (KEW.PS-3)')
@section('form_code', 'KEW.PS-3')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">DAFTAR STOK (KEW.PS-3)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.3 - Rekod Stok Kerajaan)</p>
    </div>

    <!-- BAHAGIAN A -->
    <div class="border border-slate-400 p-3 rounded text-xs space-y-2">
        <div class="font-bold uppercase text-[11px] text-blue-900 border-b border-slate-300 pb-1">BAHAGIAN A: Butiran Stok</div>
        <div class="grid grid-cols-3 gap-3">
            <div><span class="font-bold">No. Kod:</span> {{ $stock->item_code ?? 'STK-001' }}</div>
            <div><span class="font-bold">No. Kad:</span> {{ $stock->card_number ?? '-' }}</div>
            <div><span class="font-bold">Kategori:</span> {{ $stock->category->name ?? 'Am' }}</div>

            <div class="col-span-2"><span class="font-bold">Perihal Stok:</span> {{ $stock->description ?? 'Stok Pejabat' }}</div>
            <div><span class="font-bold">Kumpulan:</span> {{ $stock->stock_group ?? 'B' }} ({{ $stock->movement ?? 'CEPAT' }})</div>

            <div><span class="font-bold">Unit Pengukuran:</span> {{ $stock->uom->name ?? 'Unit' }}</div>
            <div><span class="font-bold">Lokasi Simpanan:</span> {{ $stock->defaultLocation->code ?? '-' }}</div>
            <div><span class="font-bold">Harga Seunit:</span> RM {{ number_format($stock->unit_price ?? 0, 2) }}</div>

            <div><span class="font-bold">Paras Minimum:</span> {{ number_format($stock->minimum_level ?? 0) }}</div>
            <div><span class="font-bold">Paras Menokok:</span> {{ number_format($stock->reorder_level ?? 0) }}</div>
            <div><span class="font-bold">Paras Maksimum:</span> {{ number_format($stock->maximum_level ?? 0) }}</div>
        </div>
    </div>

    <!-- BAHAGIAN B -->
    <div class="space-y-1">
        <div class="font-bold uppercase text-[11px] text-blue-900">BAHAGIAN B: Transaksi Stok (Penerimaan, Pengeluaran & Baki)</div>
        <table class="w-full text-left border-collapse border border-slate-400 text-[11px]">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[9px]">
                    <th rowspan="2" class="border border-slate-300 p-1 text-center w-16">Tarikh</th>
                    <th rowspan="2" class="border border-slate-300 p-1 w-28">No. PK / BTB / BPB / Pesanan</th>
                    <th colspan="2" class="border border-slate-300 p-1 text-center bg-blue-50">Terimaan</th>
                    <th colspan="2" class="border border-slate-300 p-1 text-center bg-purple-50">Keluaran</th>
                    <th colspan="2" class="border border-slate-300 p-1 text-center bg-slate-200">Baki Semasa</th>
                    <th rowspan="2" class="border border-slate-300 p-1 text-center w-24">T/tangan Pegawai</th>
                </tr>
                <tr class="bg-slate-50 font-bold border-b border-slate-400 uppercase text-[9px]">
                    <th class="border border-slate-300 p-1 text-right w-14">Kuantiti</th>
                    <th class="border border-slate-300 p-1 text-right w-16">Nilai (RM)</th>
                    <th class="border border-slate-300 p-1 text-right w-14">Kuantiti</th>
                    <th class="border border-slate-300 p-1 text-right w-16">Nilai (RM)</th>
                    <th class="border border-slate-300 p-1 text-right w-14">Kuantiti</th>
                    <th class="border border-slate-300 p-1 text-right w-16">Nilai (RM)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($transactions) && count($transactions) > 0)
                    @foreach($transactions as $txn)
                    <tr>
                        <td class="border border-slate-300 p-1 text-center font-mono">{{ $txn->transaction->transaction_date ?? date('d/m/Y') }}</td>
                        <td class="border border-slate-300 p-1 font-mono text-[10px]">{{ $txn->transaction->reference_number ?? '-' }}</td>
                        <td class="border border-slate-300 p-1 text-right font-mono">{{ $txn->movement_type === 'IN' ? number_format($txn->quantity) : '-' }}</td>
                        <td class="border border-slate-300 p-1 text-right font-mono">{{ $txn->movement_type === 'IN' ? number_format($txn->total_price, 2) : '-' }}</td>
                        <td class="border border-slate-300 p-1 text-right font-mono">{{ $txn->movement_type === 'OUT' ? number_format($txn->quantity) : '-' }}</td>
                        <td class="border border-slate-300 p-1 text-right font-mono">{{ $txn->movement_type === 'OUT' ? number_format($txn->total_price, 2) : '-' }}</td>
                        <td class="border border-slate-300 p-1 text-right font-mono font-bold bg-slate-50">{{ number_format($txn->running_balance_quantity) }}</td>
                        <td class="border border-slate-300 p-1 text-right font-mono font-bold bg-slate-50">{{ number_format($txn->running_balance_value, 2) }}</td>
                        <td class="border border-slate-300 p-1 text-center text-[10px]">{{ $txn->transaction->user->name ?? 'Pegawai' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" class="p-4 text-center text-slate-400">Tiada rekod transaksi stok.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
