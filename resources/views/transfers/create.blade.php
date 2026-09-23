@extends('layouts.app')

@section('title', 'Permohonan Pindahan Stok (KEW.PS-17)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    stockItems: {{ Js::from($stockItemsJson) }},
    items: [
        { stock_item_id: '', requested_quantity: 1, remarks: '', searchQuery: '', dropdownOpen: false }
    ],
    addItem(stockId = null) {
        const found = stockId ? this.stockItems.find(s => s.id == stockId) : null;
        const label = found ? `[${found.code}] ${found.description}` : '';
        this.items.push({ stock_item_id: stockId || '', requested_quantity: 1, remarks: '', searchQuery: label, dropdownOpen: false });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },
    selectItem(index, stock) {
        this.items[index].stock_item_id = stock.id;
        this.items[index].searchQuery = `[${stock.code}] ${stock.description}`;
        this.items[index].dropdownOpen = false;
    },
    clearSelection(index) {
        this.items[index].stock_item_id = '';
        this.items[index].searchQuery = '';
        this.items[index].dropdownOpen = true;
    },
    filteredStocks(query) {
        if (!query || query.trim() === '') {
            return this.stockItems.slice(0, 25);
        }
        const q = query.toLowerCase().trim();
        return this.stockItems.filter(s => 
            s.code.toLowerCase().includes(q) || 
            s.description.toLowerCase().includes(q) ||
            s.category.toLowerCase().includes(q)
        ).slice(0, 30);
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.8</span>
                <span class="text-xs text-slate-500">Borang Pindahan Stok Antara Stor</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Permohonan Pindahan Stok (KEW.PS-17)</h1>
            <p class="text-sm text-slate-600">Lengkapkan maklumat stor pembekal, stor pemohon, dan senarai item stok yang hendak dipindahkan.</p>
        </div>
        <a href="{{ route('transfers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
            Kembali ke Senarai
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
        <div class="font-bold mb-1">Terdapat ralat pada borang:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('transfers.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Store Selection & Purpose -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                Maklumat Pindahan Stor
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Pembekal (Asal) <span class="text-rose-500">*</span></label>
                    <select name="source_store_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Stor Asal --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ old('source_store_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->code }} - {{ $st->name }} ({{ $st->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Pemohon (Destinasi) <span class="text-rose-500">*</span></label>
                    <select name="destination_store_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Stor Destinasi --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ old('destination_store_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->code }} - {{ $st->name }} ({{ $st->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tujuan / Justifikasi Pindahan <span class="text-rose-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}" required placeholder="Contoh: Menampung keperluan operasi mendesak aktiviti makmal penyelidikan ASM" class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Stock Items Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mr-2"></span>
                    Senarai Stok Untuk Dipindahkan
                </h3>
                <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-5">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Perihal Stok (Carian Pintar)</label>
                            <div class="relative" @click.away="item.dropdownOpen = false">
                                <input type="hidden" :name="`items[${index}][stock_item_id]`" :value="item.stock_item_id" required>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        x-model="item.searchQuery" 
                                        @focus="item.dropdownOpen = true"
                                        @input="item.dropdownOpen = true"
                                        @keydown.escape="item.dropdownOpen = false"
                                        @keydown.enter.prevent="if (filteredStocks(item.searchQuery).length > 0) { selectItem(index, filteredStocks(item.searchQuery)[0]); }"
                                        placeholder="🔍 Taip nama atau kod stok..." 
                                        class="w-full text-xs pr-6 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white"
                                    >
                                    <button 
                                        type="button" 
                                        x-show="item.searchQuery" 
                                        @click="clearSelection(index)" 
                                        class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600 text-sm font-bold"
                                    >
                                        &times;
                                    </button>
                                </div>

                                <div 
                                    x-show="item.dropdownOpen" 
                                    class="absolute left-0 right-0 z-50 mt-1 max-h-56 overflow-y-auto bg-white rounded-xl shadow-xl border border-slate-200 divide-y divide-slate-100 text-xs"
                                    style="display: none;"
                                >
                                    <template x-for="stock in filteredStocks(item.searchQuery)" :key="stock.id">
                                        <div 
                                            @click="selectItem(index, stock)" 
                                            class="p-2 hover:bg-blue-50 cursor-pointer transition flex items-center justify-between"
                                            :class="item.stock_item_id == stock.id ? 'bg-blue-50 font-bold' : ''"
                                        >
                                            <div class="truncate mr-2">
                                                <span class="font-mono text-blue-700 font-bold" x-text="stock.code"></span> - <span x-text="stock.description"></span>
                                            </div>
                                            <span class="text-[10px] text-slate-500 whitespace-nowrap" x-text="`Baki: ${stock.quantity} ${stock.uom}`"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredStocks(item.searchQuery).length === 0" class="p-3 text-center text-slate-400 text-xs">
                                        Tiada stok dijumpai
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kuantiti Mohon</label>
                            <input type="number" step="0.01" min="0.01" :name="`items[${index}][requested_quantity]`" x-model="item.requested_quantity" required class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white font-bold text-slate-800">
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                            <input type="text" placeholder="Catatan kegunaan..." :name="`items[${index}][remarks]`" x-model="item.remarks" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                        </div>

                        <div class="md:col-span-1 text-center">
                            <button type="button" @click="removeItem(index)" :disabled="items.length === 1" class="p-2 text-rose-500 hover:text-rose-700 disabled:opacity-30 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('transfers.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Hantar Permohonan KEW.PS-17
            </button>
        </div>
    </form>
</div>
@endsection
