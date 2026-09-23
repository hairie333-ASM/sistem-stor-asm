@extends('layouts.app')

@section('title', 'Permohonan Pesanan Stok: ' . $formType)
@section('page_title', $formType === 'KEW.PS-7' ? 'Borang Permohonan Stok Antara Stor (KEW.PS-7)' : 'Borang Permohonan Stok Individu (KEW.PS-8)')
@section('page_description', 'Mengemukakan pesanan barangan stor mengikut prosedur Pekeliling Perbendaharaan AM 6.5')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" x-data="{
    formType: '{{ $formType }}',
    catalogModalOpen: false,
    catalogSearchQuery: '',
    selectedCategoryFilter: '',
    activeRowIndexForPicker: null,

    stockItems: {{ Js::from($stockItemsJson) }},
    categories: {{ Js::from($categories) }},

    items: [
        {
            stock_item_id: '{{ request('stock_id') ?? ($stockItems->first()->id ?? 1) }}',
            requested_quantity: 1,
            remarks: '',
            searchQuery: '',
            dropdownOpen: false
        }
    ],

    init() {
        this.items.forEach(item => {
            const found = this.stockItems.find(s => s.id == item.stock_item_id);
            if (found) {
                item.searchQuery = `${found.code} - ${found.description} (Baki: ${Number(found.quantity).toLocaleString()} ${found.uom})`;
            }
        });
    },

    addItem(stockId = null) {
        const defaultId = stockId || (this.stockItems[0] ? this.stockItems[0].id : 1);
        const found = this.stockItems.find(s => s.id == defaultId);
        const label = found ? `${found.code} - ${found.description} (Baki: ${Number(found.quantity).toLocaleString()} ${found.uom})` : '';
        this.items.push({
            stock_item_id: defaultId,
            requested_quantity: 1,
            remarks: '',
            searchQuery: label,
            dropdownOpen: false
        });
    },

    removeItem(idx) {
        if (this.items.length > 1) this.items.splice(idx, 1);
    },

    selectItem(index, stock) {
        this.items[index].stock_item_id = stock.id;
        this.items[index].searchQuery = `${stock.code} - ${stock.description} (Baki: ${Number(stock.quantity).toLocaleString()} ${stock.uom})`;
        this.items[index].dropdownOpen = false;
    },

    clearSelection(index) {
        this.items[index].stock_item_id = '';
        this.items[index].searchQuery = '';
        this.items[index].dropdownOpen = true;
    },

    handleBlur(index) {
        const currentItem = this.items[index];
        const stock = this.stockItems.find(s => s.id == currentItem.stock_item_id);
        if (stock) {
            currentItem.searchQuery = `${stock.code} - ${stock.description} (Baki: ${Number(stock.quantity).toLocaleString()} ${stock.uom})`;
        }
        currentItem.dropdownOpen = false;
    },

    filteredStocks(query) {
        if (!query || query.trim() === '') {
            return this.stockItems.slice(0, 30);
        }
        const q = query.toLowerCase().trim();
        return this.stockItems.filter(s => 
            s.code.toLowerCase().includes(q) || 
            s.description.toLowerCase().includes(q) ||
            s.category.toLowerCase().includes(q)
        ).slice(0, 40);
    },

    openCatalogModal(index = null) {
        this.activeRowIndexForPicker = index;
        this.catalogSearchQuery = '';
        this.selectedCategoryFilter = '';
        this.catalogModalOpen = true;
    },

    filteredModalStocks() {
        let list = this.stockItems;
        if (this.selectedCategoryFilter) {
            list = list.filter(s => s.category_id == this.selectedCategoryFilter);
        }
        if (this.catalogSearchQuery && this.catalogSearchQuery.trim() !== '') {
            const q = this.catalogSearchQuery.toLowerCase().trim();
            list = list.filter(s => 
                s.code.toLowerCase().includes(q) || 
                s.description.toLowerCase().includes(q) ||
                s.category.toLowerCase().includes(q)
            );
        }
        return list.slice(0, 60);
    },

    chooseFromModal(stock) {
        if (this.activeRowIndexForPicker !== null && this.items[this.activeRowIndexForPicker]) {
            this.selectItem(this.activeRowIndexForPicker, stock);
        } else {
            const existing = this.items.find(i => i.stock_item_id == stock.id);
            if (existing) {
                existing.requested_quantity = parseInt(existing.requested_quantity) + 1;
            } else {
                this.addItem(stock.id);
            }
        }
        this.catalogModalOpen = false;
        this.activeRowIndexForPicker = null;
    }
}">
    <form method="POST" action="{{ route('requests.store') }}" class="space-y-6 text-xs">
        @csrf
        <input type="hidden" name="form_type" :value="formType">

        <!-- Form Selection Toggle (KEW.PS-7 only for Storekeepers / Admin) -->
        @if(auth()->user()->hasRole(['admin', 'pegawai_stor']))
        <div class="flex space-x-2 border-b border-slate-200 pb-4">
            <button type="button" @click="formType = 'KEW.PS-8'" :class="formType === 'KEW.PS-8' ? 'bg-blue-700 text-white font-bold' : 'bg-slate-100 text-slate-700'" class="px-4 py-2 rounded-lg text-xs transition">
                Borang Individu (KEW.PS-8)
            </button>
            <button type="button" @click="formType = 'KEW.PS-7'" :class="formType === 'KEW.PS-7' ? 'bg-purple-700 text-white font-bold' : 'bg-slate-100 text-slate-700'" class="px-4 py-2 rounded-lg text-xs transition">
                Borang Antara Stor (KEW.PS-7)
            </button>
        </div>
        @else
        <div class="border-b border-slate-200 pb-3 mb-2 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800 border border-blue-200">
                    Borang Permohonan Stok Individu / Bahagian (KEW.PS-8)
                </span>
            </div>
            <span class="text-[11px] text-slate-500">Tatacara Pengurusan Stor AM 6.5</span>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Stor Pembekal / Sumber *</label>
                <select name="store_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Shown only for KEW.PS-7 -->
            <div x-show="formType === 'KEW.PS-7'">
                <label class="block font-semibold text-purple-800 mb-1">Stor Pemohon (Destinasi) *</label>
                <select name="requesting_store_id" class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:outline-none">
                    <option value="">Pilih Stor Memohon</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Bahagian / Cawangan / Unit *</label>
                <input type="text" name="department" value="{{ old('department', auth()->user()->department) }}" required placeholder="cth: Bahagian Pentadbiran" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahap Keutamaan *</label>
                <select name="priority" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="NORMAL">Biasa (Normal)</option>
                    <option value="URGENT">Segera (Urgent)</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block font-semibold text-slate-700 mb-1">Tujuan / Kegunaan Permohonan *</label>
                <input type="text" name="purpose" value="{{ old('purpose') }}" required placeholder="cth: Bekalan alat tulis untuk penganjuran Mesyuarat Lembaga Pentadbir ASM" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        </div>

        <!-- Items Table -->
        <div class="border-t border-slate-200 pt-4">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Senarai Barangan Yang Dipesan</h3>
                    <p class="text-[11px] text-slate-500">Cari kod atau nama stok dalam kotak carian, atau klik butang Katalog Pintar.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" @click="openCatalogModal()" class="px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-300 rounded-lg font-bold hover:bg-amber-100 transition flex items-center space-x-1.5 shadow-sm">
                        <span>🔍</span>
                        <span>Carian Katalog Pintar</span>
                    </button>
                    <button type="button" @click="addItem()" class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg font-bold hover:bg-blue-100 transition shadow-sm flex items-center space-x-1">
                        <span>+</span>
                        <span>Tambah Item</span>
                    </button>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-visible">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Item Stok (Katalog & Carian Pantas)</th>
                            <th class="py-2.5 px-2 text-center w-28">Kuantiti Dimohon *</th>
                            <th class="py-2.5 px-2">Catatan Tambahan</th>
                            <th class="py-2.5 px-2 text-center w-12">Padam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-2 px-3 align-top">
                                    <!-- Searchable Stock Dropdown Component -->
                                    <div class="relative" @click.away="handleBlur(index)">
                                        <!-- Real Hidden Form Input for Stock Item ID -->
                                        <input type="hidden" :name="'items[' + index + '][stock_item_id]'" :value="item.stock_item_id" required>

                                        <div class="flex items-center space-x-1.5">
                                            <!-- Live Search Input -->
                                            <div class="relative flex-1">
                                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    x-model="item.searchQuery" 
                                                    @focus="item.dropdownOpen = true"
                                                    @input="item.dropdownOpen = true"
                                                    @keydown.escape="item.dropdownOpen = false"
                                                    @keydown.enter.prevent="if (filteredStocks(item.searchQuery).length > 0) { selectItem(index, filteredStocks(item.searchQuery)[0]); }"
                                                    placeholder="🔍 Taip kod, perihal stok (cth: P255, fail, kertas)..."
                                                    class="w-full pl-8 pr-7 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white text-slate-900 shadow-sm font-medium"
                                                >
                                                <!-- Clear Input Button -->
                                                <button 
                                                    type="button" 
                                                    x-show="item.searchQuery" 
                                                    @click="clearSelection(index)" 
                                                    class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600 text-sm font-bold"
                                                    title="Padam teks carian"
                                                >
                                                    &times;
                                                </button>
                                            </div>

                                            <!-- Modal Trigger Button for Row -->
                                            <button 
                                                type="button" 
                                                @click="openCatalogModal(index)" 
                                                title="Buka katalog carian untuk baris ini"
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-blue-100 hover:text-blue-700 text-slate-600 border border-slate-300 rounded-lg text-xs font-semibold transition flex items-center space-x-1 flex-shrink-0"
                                            >
                                                <span>🔍</span>
                                                <span class="hidden sm:inline text-[11px]">Katalog</span>
                                            </button>
                                        </div>

                                        <!-- Dropdown Menu Results -->
                                        <div 
                                            x-show="item.dropdownOpen" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute left-0 right-0 z-50 mt-1 max-h-64 overflow-y-auto bg-white rounded-xl shadow-2xl border border-slate-200 divide-y divide-slate-100 text-xs"
                                            style="display: none;"
                                        >
                                            <!-- Result count bar -->
                                            <div class="px-3 py-1.5 bg-slate-50 text-[11px] text-slate-500 font-medium flex justify-between items-center sticky top-0 z-10 border-b border-slate-200">
                                                <span x-text="`Padanan: ${filteredStocks(item.searchQuery).length} item (daripada ${stockItems.length})`"></span>
                                                <span class="text-[10px] text-blue-600 font-semibold">Klik atau Enter untuk pilih</span>
                                            </div>

                                            <!-- Matching Items -->
                                            <template x-for="stock in filteredStocks(item.searchQuery)" :key="stock.id">
                                                <div 
                                                    @click="selectItem(index, stock)" 
                                                    class="p-2 hover:bg-blue-50 cursor-pointer transition flex items-center justify-between space-x-2.5"
                                                    :class="item.stock_item_id == stock.id ? 'bg-blue-50/80 border-l-4 border-blue-600 font-bold' : ''"
                                                >
                                                    <!-- Image Thumbnail -->
                                                    <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-200 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-xs">
                                                        <template x-if="stock.image_url">
                                                            <img :src="stock.image_url" :alt="stock.description" class="w-full h-full object-contain p-0.5">
                                                        </template>
                                                        <template x-if="!stock.image_url">
                                                            <span class="text-sm">📦</span>
                                                        </template>
                                                    </div>
                                                    <div class="space-y-0.5 flex-1 min-w-0">
                                                        <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                                            <span class="font-mono font-bold text-blue-700 bg-blue-100/70 px-1.5 py-0.5 rounded text-[10px]" x-text="stock.code"></span>
                                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600" x-text="stock.category"></span>
                                                        </div>
                                                        <div class="font-medium text-slate-800 truncate" x-text="stock.description" :title="stock.description"></div>
                                                    </div>
                                                    <div class="text-right flex-shrink-0">
                                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold" 
                                                              :class="stock.quantity > 10 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                                              x-text="`Baki: ${Number(stock.quantity).toLocaleString()} ${stock.uom}`">
                                                        </span>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Empty Results Message -->
                                            <div x-show="filteredStocks(item.searchQuery).length === 0" class="p-4 text-center text-slate-500">
                                                <p class="font-semibold text-slate-700">Tiada stok sepadan dengan carian ini</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">Sila cuba kata kunci lain atau buka Katalog Pintar</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-2 text-center align-top">
                                    <input type="number" step="1" min="1" :name="'items[' + index + '][requested_quantity]'" x-model="item.requested_quantity" required class="w-full text-center px-2 py-1.5 border border-slate-300 rounded font-bold text-slate-900 focus:ring-1 focus:ring-blue-600">
                                </td>
                                <td class="py-2 px-2 align-top">
                                    <input type="text" :name="'items[' + index + '][remarks]'" x-model="item.remarks" placeholder="Catatan jika ada..." class="w-full px-2 py-1.5 border border-slate-300 rounded focus:ring-1 focus:ring-blue-600">
                                </td>
                                <td class="py-2 px-2 text-center align-top pt-3">
                                    <button type="button" @click="removeItem(index)" class="text-rose-600 hover:text-rose-900 font-bold text-sm" title="Padam baris">&times;</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('requests.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg font-semibold">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold shadow">
                Hantar Permohonan Stok
            </button>
        </div>
    </form>

    <!-- Modal Carian Katalog Pintar ASM -->
    <div 
        x-show="catalogModalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="catalogModalOpen = false"
    >
        <div 
            @click.away="catalogModalOpen = false"
            class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-200 flex flex-col max-h-[85vh]"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-navy-900 text-white flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🔍</span>
                    <div>
                        <h3 class="font-bold text-sm">Carian Katalog Pintar Stok ASM</h3>
                        <p class="text-[11px] text-slate-300">Cari daripada 900+ item stok mengikut nama, kod perakaunan, atau kategori.</p>
                    </div>
                </div>
                <button type="button" @click="catalogModalOpen = false" class="text-slate-400 hover:text-white text-2xl font-bold p-1 leading-none">
                    &times;
                </button>
            </div>

            <!-- Filter & Search Controls -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 space-y-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        type="text" 
                        x-model="catalogSearchQuery" 
                        placeholder="Taip perkataan carian (cth: fail, pen, kertas, P255, toner, marker, buku, reagen)..." 
                        class="w-full pl-9 pr-4 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white shadow-sm font-medium"
                        autofocus
                    >
                </div>

                <!-- Category Pills -->
                <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 text-[11px]">
                    <button 
                        type="button" 
                        @click="selectedCategoryFilter = ''" 
                        :class="selectedCategoryFilter === '' ? 'bg-blue-700 text-white font-bold' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-100'" 
                        class="px-2.5 py-1 rounded-full whitespace-nowrap transition"
                    >
                        Semua Kategori (<span x-text="stockItems.length"></span>)
                    </button>
                    <template x-for="cat in categories" :key="cat.id">
                        <button 
                            type="button" 
                            @click="selectedCategoryFilter = cat.id" 
                            :class="selectedCategoryFilter === cat.id ? 'bg-blue-700 text-white font-bold' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-100'" 
                            class="px-2.5 py-1 rounded-full whitespace-nowrap transition"
                            x-text="cat.name"
                        ></button>
                    </template>
                </div>
            </div>

            <!-- Results List -->
            <div class="flex-1 overflow-y-auto p-4 divide-y divide-slate-100">
                <template x-for="stock in filteredModalStocks()" :key="stock.id">
                    <div class="py-2.5 flex items-center justify-between hover:bg-slate-50 px-3 rounded-xl transition space-x-3 border border-transparent hover:border-slate-200 mb-1">
                        <!-- Image Thumbnail -->
                        <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-xs">
                            <template x-if="stock.image_url">
                                <img :src="stock.image_url" :alt="stock.description" class="w-full h-full object-contain p-1">
                            </template>
                            <template x-if="!stock.image_url">
                                <span class="text-xl">📦</span>
                            </template>
                        </div>
                        <div class="space-y-1 flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded text-[11px]" x-text="stock.code"></span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700" x-text="stock.category"></span>
                            </div>
                            <div class="font-semibold text-slate-800 text-xs" x-text="stock.description"></div>
                            <div class="text-[11px] text-slate-500">
                                Baki Stok Sedia Ada: <strong class="text-emerald-700 font-bold" x-text="`${Number(stock.quantity).toLocaleString()} ${stock.uom}`"></strong>
                            </div>
                        </div>
                        <div>
                            <button 
                                type="button" 
                                @click="chooseFromModal(stock)" 
                                class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1"
                            >
                                <span>+</span>
                                <span>Pilih Item</span>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="filteredModalStocks().length === 0" class="py-12 text-center text-slate-500">
                    <p class="text-sm font-bold text-slate-700">Tiada stok dijumpai</p>
                    <p class="text-xs text-slate-400 mt-1">Sila tukar kata kunci carian atau pilih kategori lain.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs text-slate-500">
                <span x-text="`Menunjukkan ${filteredModalStocks().length} hasil carian`"></span>
                <button type="button" @click="catalogModalOpen = false" class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-semibold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
