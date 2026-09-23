@extends('layouts.app')

@section('title', 'Pelupusan Stok (TPS AM 6.9)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.9</span>
                <span class="text-xs text-slate-500">Pelupusan Stok Kerajaan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Pelupusan Stok Kerajaan (KEW.PS-19 hingga KEW.PS-31)</h1>
            <p class="text-sm text-slate-600">Pengurusan Lembaga Pemeriksa (KEW.PS-19), Perakuan Pelupusan (KEW.PS-20), Kelulusan (KEW.PS-21), dan Sijil Pelupusan (KEW.PS-22/23).</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('disposal.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Cadangan Pelupusan Baru (KEW.PS-20)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('disposal.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-56">
                <select name="status" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Status --</option>
                    <option value="PROPOSED" {{ request('status') === 'PROPOSED' ? 'selected' : '' }}>Dicadangkan (Menunggu Kelulusan)</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Diluluskan (Sedia Tindakan Fizikal)</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai Dilupuskan (KEW.PS-22/23)</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                Tapis Rekod
            </button>
            @if(request()->filled('status'))
            <a href="{{ route('disposal.index') }}" class="text-xs text-rose-600 hover:underline">
                Kosongkan Tapisan
            </a>
            @endif
        </form>
    </div>

    <!-- Disposals List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">No. Rujukan</th>
                        <th class="py-3 px-4">Stor Terlibat</th>
                        <th class="py-3 px-4">Kaedah Pelupusan</th>
                        <th class="py-3 px-4 text-center">Bil. Item</th>
                        <th class="py-3 px-4 text-right">Nilai Asal (RM)</th>
                        <th class="py-3 px-4 text-right">Hasil Diperoleh (RM)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($disposals as $disp)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-blue-700">
                            {{ $disp->disposal_number }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-800">
                            {{ $disp->store->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-800">
                                {{ $disp->disposal_method }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-bold">
                            {{ $disp->items->count() }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-semibold text-slate-800">
                            {{ number_format($disp->total_original_value, 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-700">
                            {{ number_format($disp->total_revenue, 2) }}
                        </td>
                        <td class="py-3 px-4">
                            @if($disp->status === 'PROPOSED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu Kelulusan</span>
                            @elseif($disp->status === 'APPROVED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Diluluskan (Sedia Tindakan)</span>
                            @elseif($disp->status === 'COMPLETED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Selesai (Sijil Dikeluarkan)</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right space-x-1.5">
                            <a href="{{ route('disposal.show', $disp) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition">
                                Butiran
                            </a>
                            <a href="{{ route('disposal.print-ps20', $disp) }}" target="_blank" class="inline-flex items-center px-2 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded text-xs font-semibold transition border border-amber-200">
                                KEW.PS-20
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            Tiada rekod pelupusan stok ditemui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($disposals->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $disposals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
