@extends('layouts.app')

@section('title', 'Pindahan Stok Antara Stor (KEW.PS-17)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.8</span>
                <span class="text-xs text-slate-500">Pindahan Stok Kerajaan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Pindahan Stok Antara Stor (KEW.PS-17)</h1>
            <p class="text-sm text-slate-600">Kawalan pemindahan stok antara Stor Pusat, Stor Utama, dan Stor Unit secara berintegriti.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('transfers.report-ps18') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition border border-slate-300">
                <svg class="w-4 h-4 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Laporan Tahunan KEW.PS-18
            </a>
            <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Permohonan Pindahan Baru
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('transfers.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-48">
                <select name="status" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Status --</option>
                    <option value="SUBMITTED" {{ request('status') === 'SUBMITTED' ? 'selected' : '' }}>Dihantar (Menunggu Kelulusan)</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Diluluskan (Sedia Dihantar)</option>
                    <option value="DISPATCHED" {{ request('status') === 'DISPATCHED' ? 'selected' : '' }}>Dalam Penghantaran</option>
                    <option value="RECEIVED" {{ request('status') === 'RECEIVED' ? 'selected' : '' }}>Selesai Diterima</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                Tapis Rekod
            </button>
            @if(request()->filled('status'))
            <a href="{{ route('transfers.index') }}" class="text-xs text-rose-600 hover:underline">
                Kosongkan Tapisan
            </a>
            @endif
        </form>
    </div>

    <!-- Transfers List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">No. Pindahan</th>
                        <th class="py-3 px-4">Stor Pembekal (Asal)</th>
                        <th class="py-3 px-4">Stor Pemohon (Destinasi)</th>
                        <th class="py-3 px-4">Tujuan</th>
                        <th class="py-3 px-4 text-center">Bil. Item</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tarikh</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($transfers as $tr)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-blue-700">
                            {{ $tr->transfer_number }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-slate-800">{{ $tr->sourceStore->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-emerald-800">{{ $tr->destinationStore->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600 max-w-xs truncate">
                            {{ $tr->purpose }}
                        </td>
                        <td class="py-3 px-4 text-center font-bold">
                            {{ $tr->items->count() }}
                        </td>
                        <td class="py-3 px-4">
                            @if($tr->status === 'SUBMITTED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu Kelulusan</span>
                            @elseif($tr->status === 'APPROVED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Diluluskan</span>
                            @elseif($tr->status === 'DISPATCHED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Dihantar (Dalam Perjalanan)</span>
                            @elseif($tr->status === 'RECEIVED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Selesai Diterima</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500">
                            {{ $tr->created_at->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a href="{{ route('transfers.show', $tr) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition">
                                Butiran
                            </a>
                            <a href="{{ route('transfers.print-ps17', $tr) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded text-xs font-semibold transition border border-amber-200">
                                KEW.PS-17
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Tiada rekod pindahan stok antara stor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
