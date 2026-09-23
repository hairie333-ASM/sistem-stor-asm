@extends('layouts.app')

@section('title', 'Kad Petak KEW.PS-4: ' . $stock->stock_code)
@section('page_title', 'Kad Petak Kerajaan — KEW.PS-4')
@section('page_description', 'Kad petak simpanan fizikal yang diletakkan bersama stok di rak/petak penyimpanan (AM 6.3)')

@section('page_actions')
    <button onclick="window.print()" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>🖨️</span>
        <span>Cetak Kad Petak</span>
    </button>
    <a href="{{ route('stock-register.show', $stock) }}" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow transition">
        Lihat KEW.PS-3
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-300 shadow-sm print:shadow-none print:border-none">
    
    <!-- KEW.PS-4 Header -->
    <div class="border-b-2 border-slate-900 pb-3 mb-6 flex justify-between items-start">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/asm-logo-emblem.png') }}" alt="Logo ASM" class="w-12 h-12 object-contain shrink-0">
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-500">Pekeliling Perbendaharaan Malaysia AM 6.3</div>
                <h2 class="text-lg font-black text-slate-900">KAD PETAK</h2>
                <div class="text-xs text-slate-600 font-medium">Akademi Sains Malaysia (ASM)</div>
            </div>
        </div>
        <div class="text-right">
            <span class="inline-block border-2 border-slate-900 px-2.5 py-0.5 text-xs font-black uppercase bg-slate-100">
                KEW.PS-4
            </span>
            <div class="text-[11px] font-mono font-bold text-slate-700 mt-1">No. Kod: {{ $stock->stock_code }}</div>
        </div>
    </div>

    <!-- Top Card Details -->
    <div class="flex flex-col sm:flex-row gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs mb-6 items-center">
        @if($stock->display_image)
            <div class="w-20 h-20 bg-white rounded-lg border border-slate-300 p-1 flex-shrink-0 flex items-center justify-center">
                <img src="{{ $stock->display_image }}" alt="{{ $stock->stock_code }}" class="max-h-full max-w-full object-contain">
            </div>
        @endif
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1 w-full">
            <div>
                <span class="text-slate-400 text-[10px] block font-bold uppercase">Perihal Stok:</span>
                <span class="font-bold text-slate-900">{{ $stock->description }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block font-bold uppercase">Unit Pengukuran:</span>
                <span class="font-bold text-slate-900">{{ $stock->uom->name }} ({{ $stock->uom->code }})</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block font-bold uppercase">Lokasi / Petak:</span>
                <span class="font-mono font-bold text-blue-800">{{ $stock->defaultLocation->full_code ?? 'Lokasi Am' }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block font-bold uppercase">Paras (Min/Nokok/Maks):</span>
                <span class="font-bold text-slate-900">{{ $stock->min_level }} / {{ $stock->reorder_level }} / {{ $stock->max_level }}</span>
            </div>
        </div>
    </div>

    <!-- Transactions Ledger Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border border-slate-900">
            <thead class="bg-slate-100 text-slate-900 font-bold border-b border-slate-900 uppercase text-[10px]">
                <tr class="divide-x divide-slate-900 text-center">
                    <th class="py-2 px-3">Tarikh</th>
                    <th class="py-2 px-3">No. Rujukan</th>
                    <th class="py-2 px-3">Daripada / Kepada</th>
                    <th class="py-2 px-3 bg-emerald-50">Terima</th>
                    <th class="py-2 px-3 bg-amber-50">Keluar</th>
                    <th class="py-2 px-3 bg-blue-50 font-black">Baki</th>
                    <th class="py-2 px-3">T/Tangan Pegawai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-900 font-mono">
                @forelse($transactions as $tItem)
                @php $txn = $tItem->transaction; @endphp
                <tr class="divide-x divide-slate-900 text-slate-800">
                    <td class="py-2 px-3 text-center whitespace-nowrap">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                    <td class="py-2 px-3 whitespace-nowrap font-sans font-medium">{{ $txn->reference_number }}</td>
                    <td class="py-2 px-3 font-sans">{{ $txn->party_name ?? '-' }}</td>
                    <td class="py-2 px-3 text-center font-bold text-emerald-800">
                        {{ $tItem->movement_type === 'IN' ? number_format($tItem->quantity, 0) : '-' }}
                    </td>
                    <td class="py-2 px-3 text-center font-bold text-amber-800">
                        {{ $tItem->movement_type === 'OUT' ? number_format($tItem->quantity, 0) : '-' }}
                    </td>
                    <td class="py-2 px-3 text-center font-black text-slate-950 bg-blue-50/50">
                        {{ number_format($tItem->balance_quantity_after, 0) }}
                    </td>
                    <td class="py-2 px-3 text-center font-sans text-[10px] text-slate-600">
                        {{ $txn->user->name ?? 'Pegawai Stor' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">
                        Tiada transaksi direkodkan untuk kad petak ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
