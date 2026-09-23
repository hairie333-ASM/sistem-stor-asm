@extends('layouts.app')

@section('title', 'Penentuan Kumpulan A & B (KEW.PS-5)')
@section('page_title', 'Penentuan Kumpulan Stok A & B — KEW.PS-5')
@section('page_description', 'Pengelasan tahunan stok mengikut nilai pembelian tahunan: 30% Kumpulan A dan 70% Kumpulan B (Pekeliling AM 6.3)')

@section('page_actions')
    <form method="POST" action="{{ route('stock.group-ab.apply') }}" onsubmit="return confirm('Sahkan pengemaskinian Kumpulan A & B bagi semua item stok?')">
        @csrf
        <button type="submit" class="px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>⚙️</span>
            <span>Jalankan Semakan Semula Kumpulan A/B</span>
        </button>
    </form>
    <a href="{{ route('reports.form', 'kew-ps-5') }}" target="_blank" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow transition">
        🖨️ Cetak KEW.PS-5
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Keseluruhan Item</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $groupData['total_items'] }} jenis</div>
            <div class="text-xs text-slate-400 mt-1">Didaftarkan dalam sistem</div>
        </div>

        <div class="bg-purple-50 p-5 rounded-2xl border border-purple-200 shadow-sm">
            <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Kumpulan A (30% Nilai Tertinggi)</span>
            <div class="text-2xl font-black text-purple-900 mt-1">{{ $groupData['group_a_count'] }} item</div>
            <div class="text-xs text-purple-600 mt-1">Kawalan inventori & verifikasi rapi</div>
        </div>

        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Kumpulan B (Baki 70%)</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $groupData['group_b_count'] }} item</div>
            <div class="text-xs text-slate-500 mt-1">Kawalan inventori standard</div>
        </div>
    </div>

    <!-- Group A Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-purple-900 text-white px-6 py-3 flex justify-between items-center">
            <h3 class="text-sm font-bold tracking-wide">SENARAI ITEM KUMPULAN A (30% Pembelian Tertinggi)</h3>
            <span class="text-xs text-purple-200 font-medium">{{ $groupData['group_a_count'] }} Item Disenaraikan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Bil</th>
                        <th class="py-3 px-4">No. Kod Stok</th>
                        <th class="py-3 px-4">Perihal Stok</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-right">Harga Seunit (RM)</th>
                        <th class="py-3 px-4 text-right">Nilai Pembelian Tahunan (RM)</th>
                        <th class="py-3 px-4 text-center">Status Kumpulan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($groupData['group_a'] as $idx => $row)
                    @php $item = $row['item']; @endphp
                    <tr class="hover:bg-purple-50/40 transition">
                        <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 font-mono font-bold text-purple-900">{{ $item->stock_code }}</td>
                        <td class="py-3 px-4 font-medium text-slate-900">{{ $item->description }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $item->category->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">{{ $item->uom->code }}</td>
                        <td class="py-3 px-4 text-right">RM {{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 px-4 text-right font-black text-purple-900">
                            RM {{ number_format($row['annual_value'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full font-bold bg-purple-100 text-purple-800 text-[10px]">
                                KUMPULAN A
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Group B Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-800 text-white px-6 py-3 flex justify-between items-center">
            <h3 class="text-sm font-bold tracking-wide">SENARAI ITEM KUMPULAN B (Baki 70%)</h3>
            <span class="text-xs text-slate-300 font-medium">{{ $groupData['group_b_count'] }} Item Disenaraikan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Bil</th>
                        <th class="py-3 px-4">No. Kod Stok</th>
                        <th class="py-3 px-4">Perihal Stok</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-right">Harga Seunit (RM)</th>
                        <th class="py-3 px-4 text-right">Nilai Pembelian Tahunan (RM)</th>
                        <th class="py-3 px-4 text-center">Status Kumpulan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($groupData['group_b'] as $idx => $row)
                    @php $item = $row['item']; @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $item->stock_code }}</td>
                        <td class="py-3 px-4 font-medium text-slate-900">{{ $item->description }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $item->category->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">{{ $item->uom->code }}</td>
                        <td class="py-3 px-4 text-right">RM {{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 px-4 text-right font-bold text-slate-900">
                            RM {{ number_format($row['annual_value'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700 text-[10px]">
                                KUMPULAN B
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
