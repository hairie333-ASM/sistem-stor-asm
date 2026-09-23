@extends('layouts.app')

@section('title', 'Katalog Stok Induk')
@section('page_title', 'Katalog Stok Induk (Inventory Master)')
@section('page_description', 'Senarai induk semua barangan inventori stor kerajaan mengikut pengelasan TPS AM 6.1')

@section('page_actions')
    <a href="{{ route('stock.export-csv', request()->query()) }}" class="px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1">
        <span>📊</span>
        <span>Eksport CSV</span>
    </a>
    <a href="{{ route('stock.print-catalogue') }}" target="_blank" class="px-3 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1">
        <span>🖨️</span>
        <span>Cetak Katalog</span>
    </a>
    @if(auth()->user()->isPemohon())
    <a href="{{ route('requests.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1">
        <span>➕</span>
        <span>Borang Pesanan Stok (KEW.PS-8)</span>
    </a>
    @elseif(auth()->user()->hasRole(['admin', 'pegawai_stor']))
    <a href="{{ route('stock.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1">
        <span>+</span>
        <span>Daftar Stok Baru</span>
    </a>
    @endif
@endsection

@section('content')
<div class="space-y-4">

    <!-- Search & Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('stock.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <!-- Search -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Carian Perihal / Kod</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kod stok, perihal..." class="w-full px-3 py-1.5 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-600 focus:outline-none">
            </div>

            <!-- Category -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-600 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Group A/B -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kumpulan TPS</label>
                <select name="stock_group" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-600 focus:outline-none">
                    <option value="">Semua Kumpulan</option>
                    <option value="A" {{ request('stock_group') == 'A' ? 'selected' : '' }}>Kumpulan A (Nilai Tertinggi 30%)</option>
                    <option value="B" {{ request('stock_group') == 'B' ? 'selected' : '' }}>Kumpulan B (Baki 70%)</option>
                </select>
            </div>

            <!-- Movement -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pergerakan</label>
                <select name="movement" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-600 focus:outline-none">
                    <option value="">Semua Pergerakan</option>
                    <option value="CEPAT" {{ request('movement') == 'CEPAT' ? 'selected' : '' }}>Cepat Bergerak (Fast)</option>
                    <option value="PERLAHAN" {{ request('movement') == 'PERLAHAN' ? 'selected' : '' }}>Perlahan Bergerak (Slow)</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold transition">
                    Tapis
                </button>
                <a href="{{ route('stock.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-medium transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kod Stok / No. Kad</th>
                        <th class="py-3 px-4">Perihal Stok</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Kump / Gerak</th>
                        <th class="py-3 px-4 text-center">Lokasi</th>
                        <th class="py-3 px-4 text-right">Harga Seunit</th>
                        <th class="py-3 px-4 text-center">Baki Kuantiti</th>
                        <th class="py-3 px-4 text-right">Jumlah Nilai (RM)</th>
                        <th class="py-3 px-4 text-center">Paras Stok</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    @php $levelStatus = $item->stock_level_status; @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 whitespace-nowrap">
                            <a href="{{ route('stock-register.show', $item) }}" class="font-bold text-blue-700 hover:underline">
                                {{ $item->stock_code }}
                            </a>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->kad_no ?? 'Tiada Kad' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-900 max-w-xs">{{ $item->description }}</div>
                            <div class="text-[10px] text-slate-400">{{ $item->supplier_name ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            {{ $item->category->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $item->stock_group === 'A' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                Kumpulan {{ $item->stock_group }}
                            </span>
                            <div class="text-[9px] text-slate-400 mt-0.5">{{ $item->movement }}</div>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                {{ $item->defaultLocation->full_code ?? 'Belum Ditentu' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap font-medium text-slate-800">
                            RM {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="font-black text-slate-900 text-sm">{{ number_format($item->current_quantity, 0) }}</div>
                            <div class="text-[10px] text-slate-400">{{ $item->uom->code ?? 'Unit' }}</div>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap font-bold text-slate-900">
                            RM {{ number_format($item->total_value, 2) }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $levelStatus['badge'] }}">
                                {{ $levelStatus['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-1.5">
                                @if(auth()->user()->isPemohon())
                                    <a href="{{ route('requests.create', ['stock_id' => $item->id]) }}" title="Pohon Stok Ini (KEW.PS-8)" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-[11px] font-semibold flex items-center space-x-1 shadow-sm">
                                        <span>➕</span>
                                        <span>Pohon</span>
                                    </a>
                                @else
                                    <a href="{{ route('stock-register.show', $item) }}" title="Lihat Daftar Stok KEW.PS-3" class="p-1 rounded hover:bg-slate-100 text-blue-600">
                                        📋
                                    </a>
                                    <a href="{{ route('stock.kad-petak', $item) }}" title="Lihat Kad Petak KEW.PS-4" class="p-1 rounded hover:bg-slate-100 text-indigo-600">
                                        🏷️
                                    </a>
                                    @if(auth()->user()->hasRole(['admin', 'pegawai_stor']))
                                    <a href="{{ route('stock.edit', $item) }}" title="Kemaskini Stok" class="p-1 rounded hover:bg-slate-100 text-amber-600">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('stock.toggle-status', $item) }}" class="inline" onsubmit="return confirm('Tukar status item stok ini?')">
                                        @csrf
                                        <button type="submit" title="{{ $item->status === 'INACTIVE' ? 'Aktifkan Semula' : 'Nyahaktifkan' }}" class="p-1 rounded hover:bg-slate-100 text-slate-500">
                                            {{ $item->status === 'INACTIVE' ? '🟢' : '⏸️' }}
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-slate-400">
                            Tiada rekod item stok ditemui mengikut tapisan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $items->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
