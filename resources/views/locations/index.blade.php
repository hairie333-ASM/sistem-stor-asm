@extends('layouts.app')

@section('title', 'Pengurusan Lokasi & Susun Atur Stor')
@section('page_title', 'Lokasi & Susun Atur Penyimpanan (AM 6.4)')
@section('page_description', 'Hierarki stor, gudang/seksyen, baris, rak, tingkat dan petak mengikut kod lokasi standard TPS')

@section('page_actions')
    <a href="{{ route('locations.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>+</span>
        <span>Daftar Lokasi Baru</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Store Selection Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($stores as $st)
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $st->code }}</span>
                    <span class="text-[10px] uppercase font-bold text-slate-400">{{ $st->store_type }}</span>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">{{ $st->name }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $st->address }}</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
                <span>{{ $st->sections->count() }} Seksyen / Gudang</span>
                <span class="font-semibold text-slate-700">{{ $st->locations->count() }} Lokasi Petak</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Location Listing Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Senarai Kod Lokasi Standard</h4>
                <p class="text-xs text-slate-500">Format: [Kod Stor]-[Seksyen]-[Baris]-[Rak]-[Tingkat]-[Petak]</p>
            </div>
            <form method="GET" action="{{ route('locations.index') }}" class="flex items-center space-x-2 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kod lokasi / rak..." class="px-3 py-1.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <button type="submit" class="px-3 py-1.5 bg-blue-700 text-white rounded-lg font-semibold">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Kod Lokasi Penuh</th>
                        <th class="py-3 px-4">Stor & Seksyen</th>
                        <th class="py-3 px-4 text-center">Baris</th>
                        <th class="py-3 px-4 text-center">Rak</th>
                        <th class="py-3 px-4 text-center">Tingkat</th>
                        <th class="py-3 px-4 text-center">Petak</th>
                        <th class="py-3 px-4 text-center">Kapasiti</th>
                        <th class="py-3 px-4 text-center">Item Disimpan</th>
                        <th class="py-3 px-4 text-center">Label & QR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($locations as $loc)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-mono font-black text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200 text-xs">
                                {{ $loc->full_code }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-900">{{ $loc->store->name ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $loc->section->name ?? 'Gudang Utama' }}</div>
                        </td>
                        <td class="py-3 px-4 text-center font-mono">{{ $loc->row }}</td>
                        <td class="py-3 px-4 text-center font-mono font-bold">{{ $loc->rack }}</td>
                        <td class="py-3 px-4 text-center font-mono">{{ $loc->level }}</td>
                        <td class="py-3 px-4 text-center font-mono">{{ $loc->bin }}</td>
                        <td class="py-3 px-4 text-center text-slate-600">{{ $loc->capacity ?? '500' }} unit</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">
                                {{ $loc->stockItems->count() }} jenis stok
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <a href="{{ route('locations.print-label', $loc) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-medium text-[11px] shadow-sm inline-flex items-center space-x-1">
                                <span>🏷️</span>
                                <span>Cetak Label QR</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">
                            Tiada lokasi penyimpanan didaftarkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $locations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
