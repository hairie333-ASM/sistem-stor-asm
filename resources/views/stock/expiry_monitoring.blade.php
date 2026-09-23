@extends('layouts.app')

@section('title', 'Pemantauan Stok Bertarikh Luput (KEW.PS-6)')
@section('page_title', 'Pemantauan Stok Bertarikh Luput — KEW.PS-6')
@section('page_description', 'Senarai kelompok stok bertarikh luput dan amaran tempoh hayat simpanan (Pekeliling AM 6.3 & AM 6.4 MDKD)')

@section('page_actions')
    <a href="{{ route('reports.form', 'kew-ps-6') }}" target="_blank" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>🖨️</span>
        <span>Cetak Borang KEW.PS-6</span>
    </a>
@endsection

@section('content')
<div class="space-y-4">

    <!-- Filter Pills -->
    <div class="flex flex-wrap gap-2 text-xs">
        <a href="{{ route('stock.expiry') }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ !request('filter') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100' }}">
            Semua Kelompok
        </a>
        <a href="{{ route('stock.expiry', ['filter' => 'expired']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ request('filter') === 'expired' ? 'bg-red-700 text-white border-red-700' : 'bg-white text-red-700 border-red-300 hover:bg-red-50' }}">
            🔴 Telah Luput
        </a>
        <a href="{{ route('stock.expiry', ['filter' => 'critical']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ request('filter') === 'critical' ? 'bg-rose-700 text-white border-rose-700' : 'bg-white text-rose-700 border-rose-300 hover:bg-rose-50' }}">
            ⚠️ Kritikal (&lt; 30 Hari)
        </a>
        <a href="{{ route('stock.expiry', ['filter' => 'near_60']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ request('filter') === 'near_60' ? 'bg-amber-700 text-white border-amber-700' : 'bg-white text-amber-700 border-amber-300 hover:bg-amber-50' }}">
            🟡 30 - 60 Hari
        </a>
        <a href="{{ route('stock.expiry', ['filter' => 'near_90']) }}" class="px-3 py-1.5 rounded-lg border font-semibold {{ request('filter') === 'near_90' ? 'bg-yellow-700 text-white border-yellow-700' : 'bg-white text-yellow-700 border-yellow-300 hover:bg-yellow-50' }}">
            ⏳ 60 - 90 Hari
        </a>
    </div>

    <!-- Expiry Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Kod Stok / No. Kelompok</th>
                        <th class="py-3 px-4">Perihal Stok</th>
                        <th class="py-3 px-4">Lokasi Rak</th>
                        <th class="py-3 px-4 text-center">Baki Kuantiti</th>
                        <th class="py-3 px-4 text-center">Tarikh Luput</th>
                        <th class="py-3 px-4 text-center">Baki Hari</th>
                        <th class="py-3 px-4 text-center">Status Tempoh</th>
                        <th class="py-3 px-4 text-right">Nilai Baki (RM)</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($batches as $b)
                    @php $status = $b->expiry_status; @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-mono font-bold text-blue-700">{{ $b->stockItem->stock_code }}</span>
                            <div class="text-[10px] text-slate-500 font-mono">{{ $b->batch_number }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-900">{{ $b->stockItem->description }}</div>
                            <div class="text-[10px] text-slate-400">{{ $b->stockItem->category->name ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-700 whitespace-nowrap">
                            {{ $b->location->full_code ?? $b->stockItem->defaultLocation->full_code ?? 'Lokasi Am' }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="font-black text-slate-900 text-sm">{{ number_format($b->remaining_quantity, 0) }}</span>
                            <span class="text-[10px] text-slate-500">{{ $b->stockItem->uom->code }}</span>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-slate-900 whitespace-nowrap">
                            {{ $b->expiry_date ? $b->expiry_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-bold whitespace-nowrap">
                            @if(isset($status['days']))
                                <span class="{{ $status['days'] < 0 ? 'text-rose-700' : ($status['days'] <= 30 ? 'text-rose-600' : 'text-slate-800') }}">
                                    {{ $status['days'] < 0 ? abs($status['days']) . ' hari lepas' : $status['days'] . ' hari' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $status['badge'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-slate-900 whitespace-nowrap">
                            RM {{ number_format($b->remaining_quantity * $b->unit_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if(isset($status['days']) && $status['days'] < 0)
                                <a href="{{ route('disposal.create') }}" class="px-2 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white font-semibold text-[10px]">
                                    Cadang Pelupusan
                                </a>
                            @else
                                <a href="{{ route('stock-register.show', $b->stockItem) }}" class="text-blue-600 hover:underline">
                                    Kad Stok →
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">
                            Tiada rekod kelompok stok bertarikh luput ditemui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $batches->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
