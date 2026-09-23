@extends('layouts.app')

@section('title', 'Laporan Paras Stok & Menokok (AM 6.3)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-rose-100 text-rose-800 border border-rose-300">TPS AM 6.3</span>
                <span class="text-xs text-slate-500">Kawalan Paras Stok 3-2-1 Bulan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Laporan Menokok Stok & Paras Kritikal</h1>
            <p class="text-sm text-slate-600">Senarai item stok yang telah mencecah atau berada di bawah Paras Menokok (Reorder Level) dan Paras Minimum.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Senarai Pesanan
            </button>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Alert Note -->
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-sm flex items-start space-x-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <span class="font-bold">Formula Menokok Stok TPS AM 6.3:</span>
            <p class="text-xs text-amber-800 mt-0.5">
                Kuantiti Menokok = <strong>Paras Maksimum (3 bulan) – Baki Stok Semasa</strong>. Pesanan perolehan baru hendaklah dimulakan sebaik baki stok mencecah Paras Menokok (2 bulan) bagi mengelakkan terputus bekalan.
            </p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">Kod & Perihal Stok</th>
                        <th class="py-3 px-4 text-center">Baki Semasa</th>
                        <th class="py-3 px-4 text-center">Paras Min (1 bln)</th>
                        <th class="py-3 px-4 text-center">Paras Menokok (2 bln)</th>
                        <th class="py-3 px-4 text-center">Paras Maks (3 bln)</th>
                        <th class="py-3 px-4 text-center">Cadangan Tambah</th>
                        <th class="py-3 px-4 text-right">Anggaran Kos (RM)</th>
                        <th class="py-3 px-4">Status Amaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($criticalItems as $item)
                    @php
                        $suggestedReorder = max(0, $item->maximum_level - $item->current_quantity);
                        $estCost = $suggestedReorder * $item->unit_price;
                        $isBelowMin = $item->current_quantity <= $item->minimum_level;
                    @endphp
                    <tr class="hover:bg-slate-50 transition {{ $isBelowMin ? 'bg-rose-50/40' : '' }}">
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $item->item_code }}</span>
                            <div class="font-medium text-slate-800">{{ $item->description }}</div>
                            <div class="text-xs text-slate-400">{{ $item->category->name ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-center font-extrabold text-base {{ $isBelowMin ? 'text-rose-700' : 'text-amber-700' }}">
                            {{ number_format($item->current_quantity) }} {{ $item->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-slate-600">
                            {{ number_format($item->minimum_level) }}
                        </td>
                        <td class="py-3 px-4 text-center text-xs font-semibold text-amber-700">
                            {{ number_format($item->reorder_level) }}
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-slate-600">
                            {{ number_format($item->maximum_level) }}
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-bold text-blue-700 bg-blue-50/50">
                            +{{ number_format($suggestedReorder) }} {{ $item->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                            {{ number_format($estCost, 2) }}
                        </td>
                        <td class="py-3 px-4">
                            @if($item->current_quantity <= 0)
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-200 text-rose-900 animate-pulse">Habis Stok (Kritikal)</span>
                            @elseif($isBelowMin)
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">Bawah Paras Min</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">Perlu Menokok</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">
                            Semua stok berada pada paras yang sihat dan mencukupi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
