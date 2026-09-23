@extends('layouts.print')

@section('title', 'Kad Petak (KEW.PS-4)')
@section('form_code', 'KEW.PS-4')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">KAD PETAK (KEW.PS-4)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.3 - Kad Petak Lokasi Rak)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="border border-slate-400 p-3 rounded text-xs grid grid-cols-3 gap-2">
        <div><span class="font-bold">No. Kod:</span> {{ $stock->item_code ?? 'STK-001' }}</div>
        <div><span class="font-bold">No. Kad:</span> {{ $stock->card_number ?? '-' }}</div>
        <div><span class="font-bold">Lokasi:</span> {{ $stock->defaultLocation->code ?? 'A-01-01' }}</div>
        <div class="col-span-2"><span class="font-bold">Perihal Stok:</span> {{ $stock->description ?? 'Item Stok Pejabat' }}</div>
        <div><span class="font-bold">Unit:</span> {{ $stock->uom->code ?? 'UNIT' }}</div>
        <div><span class="font-bold">Kumpulan:</span> {{ $stock->stock_group ?? 'B' }}</div>
        <div><span class="font-bold">Paras Min:</span> {{ number_format($stock->minimum_level ?? 0) }}</div>
        <div><span class="font-bold">Paras Menokok:</span> {{ number_format($stock->reorder_level ?? 0) }}</div>
    </div>

    <!-- Bin Card Transactions -->
    <table class="w-full text-left border-collapse border border-slate-400 text-[11px]">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-1.5 text-center w-20">Tarikh</th>
                <th class="border border-slate-300 p-1.5 w-36">No. Rujukan</th>
                <th class="border border-slate-300 p-1.5 text-center w-20">Terimaan</th>
                <th class="border border-slate-300 p-1.5 text-center w-20">Keluaran</th>
                <th class="border border-slate-300 p-1.5 text-center w-20 font-bold bg-slate-200">Baki</th>
                <th class="border border-slate-300 p-1.5 text-center w-28">T/tangan Pegawai</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($transactions) && count($transactions) > 0)
                @foreach($transactions as $txn)
                <tr>
                    <td class="border border-slate-300 p-1.5 text-center font-mono">{{ $txn->transaction->transaction_date ?? date('d/m/Y') }}</td>
                    <td class="border border-slate-300 p-1.5 font-mono text-[10px]">{{ $txn->transaction->reference_number ?? '-' }}</td>
                    <td class="border border-slate-300 p-1.5 text-center font-mono">{{ $txn->movement_type === 'IN' ? number_format($txn->quantity) : '-' }}</td>
                    <td class="border border-slate-300 p-1.5 text-center font-mono">{{ $txn->movement_type === 'OUT' ? number_format($txn->quantity) : '-' }}</td>
                    <td class="border border-slate-300 p-1.5 text-center font-mono font-bold bg-slate-50">{{ number_format($txn->running_balance_quantity) }}</td>
                    <td class="border border-slate-300 p-1.5 text-center text-[10px]">{{ $txn->transaction->user->name ?? 'Pegawai' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="6" class="p-4 text-center text-slate-400">Tiada rekod pada kad petak.</td></tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
