@extends('layouts.app')

@section('title', 'Pemulangan Stok (TPS AM 6.5)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.5</span>
                <span class="text-xs text-slate-500">Kawalan & Pengeluaran Stok</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Pemulangan Stok Tidak Digunakan / Rosak</h1>
            <p class="text-sm text-slate-600">Pengurusan rekod pemulangan stok terpakai, lebihan, atau rosak untuk kemasukan semula ke daftar atau kuarantin.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('returns.create') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Borang Pemulangan Baru
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">No. Rujukan</th>
                        <th class="py-3 px-4">Stor / Bahagian</th>
                        <th class="py-3 px-4">Pemohon</th>
                        <th class="py-3 px-4">Jenis Pemulangan</th>
                        <th class="py-3 px-4">Bilangan Item</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tarikh</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($returns as $ret)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-blue-700">
                            {{ $ret->return_number }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-medium text-slate-800">{{ $ret->store->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div>{{ $ret->user->name ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $ret->user->department ?? '' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @if($ret->return_type === 'FULL')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">Penuh (Semua)</span>
                            @elseif($ret->return_type === 'PARTIAL')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-purple-100 text-purple-800">Sebahagian</span>
                            @elseif($ret->return_type === 'UNUSED')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">Belum Digunakan</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-rose-100 text-rose-800">Rosak / Rosak Fizikal</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium">
                            {{ $ret->items->count() }} item
                        </td>
                        <td class="py-3 px-4">
                            @if($ret->status === 'PENDING_INSPECTION')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                    Menunggu Pemeriksaan
                                </span>
                            @elseif($ret->status === 'ACCEPTED')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Diterima & Diselaraskan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                    Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500">
                            {{ $ret->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('returns.show', $ret) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition">
                                Butiran & Pemeriksaan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Tiada rekod pemulangan stok dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $returns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
