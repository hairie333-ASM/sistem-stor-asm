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
<div class="space-y-4" x-data="{
    viewMode: '{{ request('view', 'table') }}',
    previewModalOpen: false,
    previewImg: '',
    previewTitle: '',
    previewCode: '',
    previewKadNo: '',
    previewCategory: '',
    previewPrice: '',
    previewQty: '',
    previewUom: '',
    previewStatus: '',
    previewStatusBadge: '',
    previewLocation: '',
    previewUrl: '',
    previewRequestUrl: '',

    openPreview(item) {
        this.previewImg = item.img;
        this.previewTitle = item.title;
        this.previewCode = item.code;
        this.previewKadNo = item.kad;
        this.previewCategory = item.category;
        this.previewPrice = item.price;
        this.previewQty = item.qty;
        this.previewUom = item.uom;
        this.previewStatus = item.statusLabel;
        this.previewStatusBadge = item.statusBadge;
        this.previewLocation = item.location;
        this.previewUrl = item.url;
        this.previewRequestUrl = item.requestUrl;
        this.previewModalOpen = true;
    }
}">

    <!-- Search & Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('stock.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <input type="hidden" name="view" :value="viewMode">

            <!-- Search -->
            <div class="lg:col-span-2">
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

            <!-- Photo Filter Toggle -->
            <div class="flex items-center pt-5">
                <label class="inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="with_image" value="1" {{ request('with_image') ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <span class="ml-2 text-xs font-semibold text-slate-700">📸 Ada Gambar</span>
                </label>
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

    <!-- View Mode Switcher Header -->
    <div class="flex flex-wrap justify-between items-center gap-3 bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-xs">
        <div class="flex items-center space-x-2 text-xs text-slate-600">
            <span>Menunjukkan <strong class="text-slate-900">{{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }}</strong> daripada <strong class="text-slate-900">{{ number_format($items->total()) }}</strong> item stok</span>
            @if(request('with_image'))
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                    📸 Ditapis: Dengan Gambar Sahaja
                </span>
            @endif
        </div>

        <!-- Toggle Table vs Cards -->
        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-xs">
            <button 
                type="button" 
                @click="viewMode = 'table'" 
                :class="viewMode === 'table' ? 'bg-white text-blue-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'" 
                class="px-3 py-1.5 rounded-md transition flex items-center space-x-1.5"
            >
                <span>📋</span>
                <span>Jadual</span>
            </button>
            <button 
                type="button" 
                @click="viewMode = 'grid'" 
                :class="viewMode === 'grid' ? 'bg-white text-blue-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'" 
                class="px-3 py-1.5 rounded-md transition flex items-center space-x-1.5"
            >
                <span>🗂️</span>
                <span>Galeri Kad</span>
            </button>
        </div>
    </div>

    <!-- 1. TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[950px]">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-3 text-center w-14">Gambar</th>
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
                    @php 
                        $levelStatus = $item->stock_level_status; 
                        $img = $item->display_image;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <!-- Product Image Thumbnail -->
                        <td class="py-2 px-3 text-center whitespace-nowrap">
                            @if($img)
                                <button 
                                    type="button" 
                                    @click="openPreview({
                                        img: '{{ $img }}',
                                        title: '{{ addslashes($item->description) }}',
                                        code: '{{ $item->stock_code }}',
                                        kad: '{{ $item->kad_no ?? $item->catalog_code ?? '-' }}',
                                        category: '{{ $item->category->name ?? '-' }}',
                                        price: 'RM {{ number_format($item->unit_price, 2) }}',
                                        qty: '{{ number_format($item->current_quantity, 0) }}',
                                        uom: '{{ $item->uom->code ?? 'Unit' }}',
                                        statusLabel: '{{ $levelStatus['label'] }}',
                                        statusBadge: '{{ $levelStatus['badge'] }}',
                                        location: '{{ $item->defaultLocation->full_code ?? 'Belum Ditentu' }}',
                                        url: '{{ route('stock-register.show', $item) }}',
                                        requestUrl: '{{ route('requests.create', ['stock_id' => $item->id]) }}'
                                    })"
                                    class="group relative w-12 h-12 rounded-lg bg-white border border-slate-200 overflow-hidden flex items-center justify-center hover:border-blue-500 hover:shadow-md transition shadow-2xs mx-auto"
                                    title="Klik untuk besarkan gambar"
                                >
                                    <img src="{{ $img }}" alt="{{ $item->description }}" class="w-full h-full object-contain p-0.5 group-hover:scale-110 transition duration-150">
                                    <span class="absolute inset-0 bg-blue-900/10 opacity-0 group-hover:opacity-100 flex items-center justify-center text-[10px] text-blue-700 font-bold">🔍</span>
                                </button>
                            @else
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs font-bold mx-auto" title="Tiada Gambar Rasmi">
                                    @if(str_contains(strtolower($item->category->name ?? ''), 'alat tulis'))
                                        ✏️
                                    @elseif(str_contains(strtolower($item->category->name ?? ''), 'cenderamata'))
                                        🎁
                                    @elseif(str_contains(strtolower($item->category->name ?? ''), 'penerbitan'))
                                        📚
                                    @else
                                        💻
                                    @endif
                                </div>
                            @endif
                        </td>
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
                                    <a href="{{ route('stock-register.show', $item) }}" title="Lihat Daftar Stok KEW.PS-3" class="p-1.5 rounded hover:bg-slate-100 text-blue-600">
                                        📋
                                    </a>
                                    <a href="{{ route('stock.kad-petak', $item) }}" title="Lihat Kad Petak KEW.PS-4" class="p-1.5 rounded hover:bg-slate-100 text-indigo-600">
                                        🏷️
                                    </a>
                                    @if(auth()->user()->hasRole(['admin', 'pegawai_stor']))
                                    <a href="{{ route('stock.edit', $item) }}" title="Kemaskini Stok" class="p-1.5 rounded hover:bg-slate-100 text-amber-600">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('stock.toggle-status', $item) }}" class="inline" onsubmit="return confirm('Tukar status item stok ini?')">
                                        @csrf
                                        <button type="submit" title="{{ $item->status === 'INACTIVE' ? 'Aktifkan Semula' : 'Nyahaktifkan' }}" class="p-1.5 rounded hover:bg-slate-100 text-slate-500">
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
                        <td colspan="11" class="py-12 text-center text-slate-400">
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

    <!-- 2. GRID / CARDS GALLERY VIEW -->
    <div x-show="viewMode === 'grid'" style="display: none;" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($items as $item)
            @php 
                $levelStatus = $item->stock_level_status; 
                $img = $item->display_image;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-lg hover:border-blue-400 transition duration-200 flex flex-col justify-between overflow-hidden group">
                <div>
                    <!-- Product Image Container -->
                    <div class="relative h-44 bg-gradient-to-b from-slate-50 to-white flex items-center justify-center p-3 border-b border-slate-100 overflow-hidden">
                        @if($img)
                            <img 
                                src="{{ $img }}" 
                                alt="{{ $item->description }}" 
                                class="max-h-full max-w-full object-contain cursor-pointer group-hover:scale-105 transition duration-200"
                                @click="openPreview({
                                    img: '{{ $img }}',
                                    title: '{{ addslashes($item->description) }}',
                                    code: '{{ $item->stock_code }}',
                                    kad: '{{ $item->kad_no ?? $item->catalog_code ?? '-' }}',
                                    category: '{{ $item->category->name ?? '-' }}',
                                    price: 'RM {{ number_format($item->unit_price, 2) }}',
                                    qty: '{{ number_format($item->current_quantity, 0) }}',
                                    uom: '{{ $item->uom->code ?? 'Unit' }}',
                                    statusLabel: '{{ $levelStatus['label'] }}',
                                    statusBadge: '{{ $levelStatus['badge'] }}',
                                    location: '{{ $item->defaultLocation->full_code ?? 'Belum Ditentu' }}',
                                    url: '{{ route('stock-register.show', $item) }}',
                                    requestUrl: '{{ route('requests.create', ['stock_id' => $item->id]) }}'
                                })"
                            >
                            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/90 text-blue-700 border border-blue-200 shadow-2xs backdrop-blur-xs flex items-center space-x-1">
                                <span>📸</span>
                                <span>Katalog</span>
                            </span>
                        @else
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <span class="text-4xl">
                                    @if(str_contains(strtolower($item->category->name ?? ''), 'alat tulis'))
                                        ✏️
                                    @elseif(str_contains(strtolower($item->category->name ?? ''), 'cenderamata'))
                                        🎁
                                    @elseif(str_contains(strtolower($item->category->name ?? ''), 'penerbitan'))
                                        📚
                                    @else
                                        💻
                                    @endif
                                </span>
                                <span class="text-[10px] font-medium text-slate-400 mt-1">Tiada Gambar Rasmi</span>
                            </div>
                        @endif

                        <!-- Top Badges -->
                        <div class="absolute top-2.5 right-2.5 flex flex-col items-end space-y-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->stock_group === 'A' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }} shadow-2xs">
                                Kumpulan {{ $item->stock_group }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-500 bg-white/90 px-1.5 py-0.2 rounded border border-slate-200">
                                {{ $item->movement }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-2.5">
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                {{ $item->stock_code }}
                            </span>
                            <span class="text-slate-500 font-mono text-[10px]">
                                {{ $item->kad_no ?? 'KOD ' . ($item->catalog_code ?? '-') }}
                            </span>
                        </div>

                        <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2 h-10" title="{{ $item->description }}">
                            <a href="{{ route('stock-register.show', $item) }}" class="hover:text-blue-700 transition">
                                {{ $item->description }}
                            </a>
                        </h3>

                        <div class="text-[11px] text-slate-500">
                            {{ $item->category->name ?? 'Kategori Am' }}
                        </div>

                        <!-- Quantity & Price Grid -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Baki Stok:</span>
                                <span class="text-base font-black text-slate-900">{{ number_format($item->current_quantity, 0) }}</span>
                                <span class="text-[10px] text-slate-500">{{ $item->uom->code ?? 'Unit' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block">Harga Seunit:</span>
                                <span class="text-sm font-bold text-blue-900">RM {{ number_format($item->unit_price, 2) }}</span>
                                <span class="text-[10px] text-slate-500 block">Jumlah: RM {{ number_format($item->total_value, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $levelStatus['badge'] }}">
                                {{ $levelStatus['label'] }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                📍 {{ $item->defaultLocation->full_code ?? 'Stor' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="p-4 pt-0">
                    <div class="border-t border-slate-100 pt-3 flex items-center justify-between gap-2">
                        @if(auth()->user()->isPemohon())
                            <a 
                                href="{{ route('requests.create', ['stock_id' => $item->id]) }}" 
                                class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-sm transition text-center flex items-center justify-center space-x-1"
                            >
                                <span>➕</span>
                                <span>Pohon Stok Ini</span>
                            </a>
                        @else
                            <a 
                                href="{{ route('stock-register.show', $item) }}" 
                                class="flex-1 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-[11px] font-bold border border-blue-200 transition text-center"
                                title="Buka Kad KEW.PS-3"
                            >
                                📋 KEW.PS-3
                            </a>
                            <a 
                                href="{{ route('stock.kad-petak', $item) }}" 
                                class="flex-1 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold border border-indigo-200 transition text-center"
                                title="Buka Kad Petak KEW.PS-4"
                            >
                                🏷️ Kad Petak
                            </a>
                            @if(auth()->user()->hasRole(['admin', 'pegawai_stor']))
                            <a 
                                href="{{ route('stock.edit', $item) }}" 
                                class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-bold transition"
                                title="Kemaskini"
                            >
                                ✏️
                            </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                <p class="text-base font-bold text-slate-700">Tiada item stok ditemui</p>
                <p class="text-xs text-slate-400 mt-1">Sila laraskan semula kata kunci carian atau tetapan tapisan.</p>
            </div>
            @endforelse
        </div>

        @if($items->hasPages())
        <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-sm">
            {{ $items->links() }}
        </div>
        @endif
    </div>

    <!-- Product Image Lightbox Modal -->
    <div 
        x-show="previewModalOpen" 
        style="display: none;" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="previewModalOpen = false"
    >
        <div 
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden flex flex-col transform transition-all"
            @click.outside="previewModalOpen = false"
        >
            <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <div>
                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded" x-text="previewCode"></span>
                    <span class="text-xs text-slate-500 font-mono ml-2" x-text="`No. Kad: ${previewKadNo}`"></span>
                </div>
                <button type="button" @click="previewModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
            </div>

            <div class="p-6 flex flex-col items-center text-center space-y-4">
                <!-- Large Image Frame -->
                <div class="w-64 h-64 bg-slate-50 rounded-2xl border border-slate-200 p-3 flex items-center justify-center shadow-inner">
                    <img :src="previewImg" :alt="previewTitle" class="max-h-full max-w-full object-contain">
                </div>

                <div class="space-y-1">
                    <h3 class="text-base font-bold text-slate-900" x-text="previewTitle"></h3>
                    <p class="text-xs text-slate-500" x-text="previewCategory"></p>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-3 gap-3 w-full text-xs bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Harga Seunit:</span>
                        <strong class="text-slate-900" x-text="previewPrice"></strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Baki Kuantiti:</span>
                        <strong class="text-slate-900" x-text="`${previewQty} ${previewUom}`"></strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Lokasi:</span>
                        <strong class="text-slate-900" x-text="previewLocation"></strong>
                    </div>
                </div>

                <div class="w-full flex justify-between items-center pt-2">
                    <span class="text-xs px-2.5 py-1 rounded-full font-bold border" :class="previewStatusBadge" x-text="previewStatus"></span>
                    <div class="space-x-2">
                        @if(auth()->user()->isPemohon())
                            <a :href="previewRequestUrl" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow transition">
                                ➕ Pohon Sekarang
                            </a>
                        @else
                            <a :href="previewUrl" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow transition">
                                📋 Buka Kad KEW.PS-3
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
