@extends('layouts.app')

@section('title', 'Daftar Stok KEW.PS-3: ' . $stock->stock_code)
@section('page_title', 'Daftar Stok Kerajaan — KEW.PS-3')
@section('page_description', 'Kad Kawalan Stok digital yang mengandungi Bahagian A (Butiran Stok) dan Bahagian B (Lejar Transaksi)')

@section('page_actions')
    <a href="{{ route('stock-register.print', $stock) }}" target="_blank" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>🖨️</span>
        <span>Cetak Borang KEW.PS-3</span>
    </a>
    <a href="{{ route('stock.kad-petak', $stock) }}" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>🏷️</span>
        <span>Kad Petak (KEW.PS-4)</span>
    </a>
    <a href="{{ route('stock.edit', $stock) }}" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow transition">
        Kemaskini Butiran
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KEW.PS-3 BAHAGIAN A: BUTIRAN STOK -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-900 text-white px-4 sm:px-6 py-3 sm:py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4">
            <div class="flex items-center space-x-2.5 sm:space-x-3">
                <span class="px-2.5 py-1 rounded bg-blue-800 text-blue-200 font-bold text-xs shrink-0 tracking-wider">BAHAGIAN A</span>
                <h3 class="text-xs sm:text-sm font-bold tracking-wide text-white">MAKLUMAT KAWALAN & PENENTUAN PARAS STOK</h3>
            </div>
            <div class="text-xs text-amber-400 font-mono font-bold shrink-0">
                NO. KAD: {{ $stock->kad_no ?? 'KAD-' . $stock->id }}
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 text-xs">
            <!-- Product Photo -->
            <div class="lg:col-span-3 flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                @if($stock->display_image)
                    <div class="w-32 h-32 bg-white rounded-xl border border-slate-200 p-2 shadow-inner flex items-center justify-center mb-2">
                        <img src="{{ $stock->display_image }}" alt="{{ $stock->stock_code }}" class="max-h-full max-w-full object-contain">
                    </div>
                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        📸 Gambar Katalog
                    </span>
                @else
                    <div class="w-32 h-32 bg-slate-100 rounded-xl border border-slate-200 flex flex-col items-center justify-center text-slate-400 mb-2">
                        <span class="text-3xl mb-1">📦</span>
                        <span class="text-[10px] text-slate-400">Tiada Gambar</span>
                    </div>
                    <span class="text-[10px] text-slate-400">Rujuk stor fizikal</span>
                @endif
            </div>

            <!-- Col 1: Details -->
            <div class="lg:col-span-3 space-y-3">
                <div>
                    <span class="text-slate-400 block uppercase text-[10px] font-bold">Perihal Stok:</span>
                    <span class="text-sm font-bold text-slate-900">{{ $stock->description }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase text-[10px] font-bold">No. Kod Stok:</span>
                    <span class="font-mono text-sm font-bold text-blue-700">{{ $stock->stock_code }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase text-[10px] font-bold">Kategori & Kumpulan:</span>
                    <span class="text-slate-800 font-medium">{{ $stock->category->name ?? '-' }} (Kumpulan {{ $stock->stock_group }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase text-[10px] font-bold">Unit Pengukuran:</span>
                    <span class="text-slate-800 font-medium">{{ $stock->uom->name ?? 'Unit' }} ({{ $stock->uom->code ?? '-' }})</span>
                </div>
            </div>

            <!-- Col 2: Storage Location Details -->
            <div class="lg:col-span-3 space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-1">
                    Lokasi Penyimpanan (AM 6.4)
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 text-[10px] block">Gudang / Seksyen:</span>
                        <span class="font-semibold text-slate-800">{{ $stock->defaultLocation->section->name ?? 'Gudang Utama' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Baris / Rak:</span>
                        <span class="font-semibold text-slate-800">Baris {{ $stock->defaultLocation->row ?? '01' }} / Rak {{ $stock->defaultLocation->rack ?? 'RA01' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Tingkat / Petak:</span>
                        <span class="font-semibold text-slate-800">Tingkat {{ $stock->defaultLocation->level ?? '01' }} / Petak {{ $stock->defaultLocation->bin ?? '01' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Kod Lokasi Penuh:</span>
                        <span class="font-mono font-bold text-blue-700">{{ $stock->defaultLocation->full_code ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Col 3: Stock Levels & Values -->
            <div class="lg:col-span-3 space-y-3 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                <div class="text-[11px] font-bold text-blue-900 uppercase tracking-wider border-b border-blue-200 pb-1">
                    Paras Stok & Nilai (AM 6.4)
                </div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 bg-white rounded border border-blue-200">
                        <div class="text-[10px] font-bold text-slate-500 uppercase">Maksimum (3 Bulan)</div>
                        <div class="text-base font-black text-slate-900">{{ number_format($stock->max_level, 0) }}</div>
                    </div>
                    <div class="p-2 bg-white rounded border border-amber-200">
                        <div class="text-[10px] font-bold text-amber-700 uppercase">Menokok (2 Bulan)</div>
                        <div class="text-base font-black text-amber-700">{{ number_format($stock->reorder_level, 0) }}</div>
                    </div>
                    <div class="p-2 bg-white rounded border border-rose-200">
                        <div class="text-[10px] font-bold text-rose-700 uppercase">Minimum (1 Bulan)</div>
                        <div class="text-base font-black text-rose-700">{{ number_format($stock->min_level, 0) }}</div>
                    </div>
                </div>
                <div class="pt-2 flex justify-between items-center text-xs">
                    <span class="text-slate-600 font-semibold">Baki Semasa: <strong class="text-slate-900 text-sm">{{ number_format($stock->current_quantity, 0) }} {{ $stock->uom->code }}</strong></span>
                    <span class="text-slate-600 font-semibold">Jumlah Nilai: <strong class="text-blue-800 text-sm">RM {{ number_format($stock->total_value, 2) }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- KEW.PS-3 BAHAGIAN B: TRANSAKSI LEJAR -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header Bahagian B (Spacious & Responsive) -->
        <div class="bg-slate-900 text-white px-4 sm:px-6 py-3.5 sm:py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2.5 sm:gap-4">
            <div class="flex items-center space-x-2.5 sm:space-x-3">
                <span class="px-2.5 py-1 rounded bg-emerald-800 text-emerald-200 font-bold text-xs shrink-0 tracking-wider">BAHAGIAN B</span>
                <h3 class="text-xs sm:text-sm font-bold tracking-wide text-white">LEJAR TRANSAKSI STOK & BAKI (KEW.PS-3)</h3>
            </div>
            <div class="flex items-center space-x-1.5 text-[11px] sm:text-xs text-slate-300">
                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Baki dikira automatik tanpa boleh diubah suai secara manual</span>
            </div>
        </div>

        <!-- Horizontal Scroll Notice for Mobile Screens -->
        <div class="lg:hidden px-4 py-2 bg-emerald-50/70 border-b border-emerald-100 flex items-center justify-between text-[11px] text-emerald-900">
            <div class="flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Tatal mendatar untuk melihat kesemua 11 kolum transaksi</span>
            </div>
            <span class="font-mono text-[10px] bg-emerald-200/80 text-emerald-900 px-2 py-0.5 rounded font-bold">KEW.PS-3</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse min-w-[980px]">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                    <tr class="divide-x divide-slate-200">
                        <th rowspan="2" class="py-3 px-4 text-center whitespace-nowrap min-w-[105px]">Tarikh</th>
                        <th rowspan="2" class="py-3 px-4 whitespace-nowrap min-w-[155px]">No. Rujukan</th>
                        <th rowspan="2" class="py-3 px-4 min-w-[220px]">Terima Daripada / Keluar Kepada</th>
                        <th colspan="3" class="py-2 px-3 text-center bg-emerald-50 text-emerald-900 border-b border-slate-200 font-extrabold tracking-wider">Terimaan</th>
                        <th colspan="2" class="py-2 px-3 text-center bg-amber-50 text-amber-900 border-b border-slate-200 font-extrabold tracking-wider">Keluaran</th>
                        <th colspan="2" class="py-2 px-3 text-center bg-blue-50 text-blue-900 border-b border-slate-200 font-extrabold tracking-wider">Baki Semasa</th>
                        <th rowspan="2" class="py-3 px-4 text-center whitespace-nowrap min-w-[120px]">T/Tangan Pegawai</th>
                    </tr>
                    <tr class="divide-x divide-slate-200 text-[9px]">
                        <th class="py-2 px-3 text-center bg-emerald-50/80 whitespace-nowrap min-w-[80px]">Kuantiti</th>
                        <th class="py-2 px-3 text-right bg-emerald-50/80 whitespace-nowrap min-w-[85px]">Harga (RM)</th>
                        <th class="py-2 px-3 text-right bg-emerald-50/80 whitespace-nowrap min-w-[90px]">Jumlah (RM)</th>
                        <th class="py-2 px-3 text-center bg-amber-50/80 whitespace-nowrap min-w-[80px]">Kuantiti</th>
                        <th class="py-2 px-3 text-right bg-amber-50/80 whitespace-nowrap min-w-[90px]">Jumlah (RM)</th>
                        <th class="py-2 px-3 text-center bg-blue-50/80 font-bold whitespace-nowrap min-w-[80px]">Kuantiti</th>
                        <th class="py-2 px-3 text-right bg-blue-50/80 font-bold whitespace-nowrap min-w-[90px]">Nilai (RM)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-mono">
                    @forelse($transactions as $tItem)
                    @php $txn = $tItem->transaction; @endphp
                    <tr class="hover:bg-slate-50 transition divide-x divide-slate-100">
                        <td class="py-2.5 px-4 text-center text-slate-600 whitespace-nowrap">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                        <td class="py-2.5 px-4 whitespace-nowrap font-sans font-medium text-slate-800">
                            <div>{{ $txn->reference_number }}</div>
                            <span class="text-[9px] text-slate-400 font-mono">({{ $txn->kew_ps_type ?? $txn->transaction_type }})</span>
                        </td>
                        <td class="py-2.5 px-4 font-sans text-slate-700">
                            {{ $txn->party_name ?? $txn->store->name }}
                        </td>

                        <!-- Terimaan -->
                        @if($tItem->movement_type === 'IN')
                            <td class="py-2.5 px-3 text-center font-bold text-emerald-800 whitespace-nowrap">{{ number_format($tItem->quantity, 0) }}</td>
                            <td class="py-2.5 px-3 text-right text-slate-700 whitespace-nowrap">{{ number_format($tItem->unit_price, 2) }}</td>
                            <td class="py-2.5 px-3 text-right font-medium text-emerald-900 whitespace-nowrap">{{ number_format($tItem->total_price, 2) }}</td>
                            <td class="py-2.5 px-3 text-center text-slate-300">-</td>
                            <td class="py-2.5 px-3 text-right text-slate-300">-</td>
                        @else
                        <!-- Keluaran -->
                            <td class="py-2.5 px-3 text-center text-slate-300">-</td>
                            <td class="py-2.5 px-3 text-right text-slate-300">-</td>
                            <td class="py-2.5 px-3 text-right text-slate-300">-</td>
                            <td class="py-2.5 px-3 text-center font-bold text-amber-800 whitespace-nowrap">{{ number_format($tItem->quantity, 0) }}</td>
                            <td class="py-2.5 px-3 text-right font-medium text-amber-900 whitespace-nowrap">{{ number_format($tItem->total_price, 2) }}</td>
                        @endif

                        <!-- Baki Semasa (Calculated Ledger) -->
                        <td class="py-2.5 px-3 text-center font-bold text-blue-900 bg-blue-50/30 whitespace-nowrap">
                            {{ number_format($tItem->balance_quantity_after, 0) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-bold text-blue-900 bg-blue-50/30 whitespace-nowrap">
                            {{ number_format($tItem->balance_value_after, 2) }}
                        </td>

                        <!-- Officer -->
                        <td class="py-2.5 px-4 text-center font-sans text-[10px] text-slate-600 whitespace-nowrap">
                            {{ $txn->user->name ?? 'Pegawai Stor' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-8 text-center text-slate-400 font-sans">
                            Tiada rekod transaksi bagi stok ini. Baki awal bermula pada kuantiti sifar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
