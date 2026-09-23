@extends('layouts.app')

@section('title', 'Penilaian & Imbangan Nilai Stok')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-emerald-100 text-emerald-800 border border-emerald-300">Laporan Kewangan TPS</span>
                <span class="text-xs text-slate-500">Nilai Pegangan Aset Semasa</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Laporan Penilaian & Baki Nilai Stok</h1>
            <p class="text-sm text-slate-600">Pecahan penilaian inventori stok semasa mengikut Kategori Stok dan Kumpulan A & B (KEW.PS-5).</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </button>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-1">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Nilai Keseluruhan Stok</div>
            <div class="text-3xl font-extrabold font-mono text-blue-700">RM {{ number_format($totalValuation, 2) }}</div>
            <div class="text-xs text-slate-400">Pegangan inventori semasa dalam stor ASM</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-700">Nilai Kumpulan A</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">Bernilai Tinggi (30%)</span>
            </div>
            <div class="text-3xl font-extrabold font-mono text-purple-700">RM {{ number_format($groupAValuation, 2) }}</div>
            <div class="text-xs text-slate-400">{{ $totalValuation > 0 ? round(($groupAValuation / $totalValuation) * 100, 1) : 0 }}% daripada jumlah nilai keseluruhan</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Nilai Kumpulan B</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Bernilai Sederhana/Rendah (70%)</span>
            </div>
            <div class="text-3xl font-extrabold font-mono text-emerald-700">RM {{ number_format($groupBValuation, 2) }}</div>
            <div class="text-xs text-slate-400">{{ $totalValuation > 0 ? round(($groupBValuation / $totalValuation) * 100, 1) : 0 }}% daripada jumlah nilai keseluruhan</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">Kod Stok</th>
                        <th class="py-3 px-4">Perihal Stok</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Kumpulan</th>
                        <th class="py-3 px-4 text-center">Baki Kuantiti</th>
                        <th class="py-3 px-4 text-right">Harga Seunit (RM)</th>
                        <th class="py-3 px-4 text-right">Jumlah Nilai (RM)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($items as $item)
                    @php $val = $item->current_quantity * $item->unit_price; @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-blue-700">
                            {{ $item->item_code }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800">
                            {{ $item->description }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            {{ $item->category->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $item->stock_group === 'A' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800' }}">
                                Kump. {{ $item->stock_group }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-slate-800">
                            {{ number_format($item->current_quantity) }} {{ $item->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-700">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                            {{ number_format($val, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            Tiada rekod stok ditemui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300 text-xs">
                    <tr>
                        <td colspan="6" class="py-3 px-4 uppercase text-slate-800 text-right">Jumlah Nilai Keseluruhan Inventori Semasa:</td>
                        <td class="py-3 px-4 text-right font-mono text-base font-extrabold text-blue-800">RM {{ number_format($totalValuation, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
