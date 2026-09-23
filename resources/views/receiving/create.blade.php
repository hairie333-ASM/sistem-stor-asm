@extends('layouts.app')

@section('title', 'Daftar Borang Terimaan Barang-Barang (BTB)')
@section('page_title', 'Borang Terimaan Barang-Barang (BTB) — KEW.PS-1')
@section('page_description', 'Pendaftaran barang yang diterima sebelum pemeriksaan fizikal dijalankan (Pekeliling AM 6.2)')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" x-data="{
    stockItems: {{ Js::from($stockItemsJson) }},
    rows: [
        { stock_item_id: '{{ $stockItems->first()->id ?? 1 }}', ordered_quantity: 10, do_quantity: 10, received_quantity: 10, unit_price: 15.00, batch_number: '', expiry_date: '', location_id: '', searchQuery: '', dropdownOpen: false }
    ],
    init() {
        this.rows.forEach(r => {
            const found = this.stockItems.find(s => s.id == r.stock_item_id);
            if (found) {
                r.searchQuery = `${found.code} - ${found.description}`;
            }
        });
    },
    addRow(stockId = null) {
        const defaultId = stockId || (this.stockItems[0] ? this.stockItems[0].id : 1);
        const found = this.stockItems.find(s => s.id == defaultId);
        const label = found ? `${found.code} - ${found.description}` : '';
        this.rows.push({ stock_item_id: defaultId, ordered_quantity: 1, do_quantity: 1, received_quantity: 1, unit_price: 0.00, batch_number: '', expiry_date: '', location_id: '', searchQuery: label, dropdownOpen: false });
    },
    removeRow(index) {
        if (this.rows.length > 1) this.rows.splice(index, 1);
    },
    selectRowStock(index, stock) {
        this.rows[index].stock_item_id = stock.id;
        this.rows[index].searchQuery = `${stock.code} - ${stock.description}`;
        this.rows[index].dropdownOpen = false;
    },
    clearRowStock(index) {
        this.rows[index].stock_item_id = '';
        this.rows[index].searchQuery = '';
        this.rows[index].dropdownOpen = true;
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
    <form method="POST" action="{{ route('receiving.store') }}" class="space-y-6 text-xs">
        @csrf

        <div class="border-b border-slate-200 pb-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">A. Maklumat Terimaan & Dokumen Pembekal</h3>
            <p class="text-xs text-slate-500">Semak maklumat pesanan rasmi, nota hantaran (DO) dan pengangkutan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Stor Penerima *</label>
                <select name="store_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Terimaan (AM 6.2) *</label>
                <select name="receipt_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="PURCHASE">Pembelian Tempatan (Purchase)</option>
                    <option value="TRANSFER">Pindahan daripada Agensi / Stor Lain</option>
                    <option value="GIFT">Hadiah / Sumbangan Rasmi</option>
                    <option value="SEIZURE">Lucut Hak / Rampasan</option>
                    <option value="OTHER">Sumber Lain Diluluskan</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Pembekal / Syarikat *</label>
                <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" required placeholder="cth: Sinar Cahaya Enterprise" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-3">
                <label class="block font-semibold text-slate-700 mb-1">Alamat Pembekal</label>
                <input type="text" name="supplier_address" value="{{ old('supplier_address') }}" placeholder="Alamat lengkap syarikat pembekal..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">No. Pesanan Tempatan / Kontrak</label>
                <input type="text" name="po_contract_number" value="{{ old('po_contract_number') }}" placeholder="cth: ASM/PO/2026/099" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tarikh Pesanan Tempatan</label>
                <input type="date" name="po_contract_date" value="{{ old('po_contract_date') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">No. Nota Hantaran (DO) *</label>
                <input type="text" name="delivery_order_number" value="{{ old('delivery_order_number') }}" required placeholder="cth: DO-99218" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tarikh Nota Hantaran (DO) *</label>
                <input type="date" name="delivery_order_date" value="{{ old('delivery_order_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Maklumat Pengangkutan / No. Lori</label>
                <input type="text" name="carrier_info" value="{{ old('carrier_info') }}" placeholder="cth: Lori WXT 8821 / PosLaju" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pegawai Teknikal (Jika Perlu)</label>
                <select name="technical_officer_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">Tiada Pegawai Teknikal Diperlukan</option>
                    @foreach($technicalOfficers as $officer)
                        <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->position }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-b border-slate-200 pb-3 pt-2 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">B. Senarai Barangan Yang Diterima</h3>
                <p class="text-xs text-slate-500">Periksa kuantiti dipesan, kuantiti dalam DO dan kuantiti fizikal yang sampai.</p>
            </div>
            <button type="button" @click="addRow()" class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg font-bold hover:bg-blue-100 transition">
                + Tambah Baris
            </button>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-2.5 px-3">Item Stok (Katalog)</th>
                        <th class="py-2.5 px-2 text-center w-24">Kuantiti Pesanan</th>
                        <th class="py-2.5 px-2 text-center w-24">Kuantiti DO</th>
                        <th class="py-2.5 px-2 text-center w-24">Kuantiti Fizikal</th>
                        <th class="py-2.5 px-2 text-right w-28">Harga Seunit (RM)</th>
                        <th class="py-2.5 px-2 w-28">No. Kelompok</th>
                        <th class="py-2.5 px-2 w-28">Tarikh Luput</th>
                        <th class="py-2.5 px-2 text-center w-12">Padam</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 align-top">
                                <div class="relative" @click.away="row.dropdownOpen = false">
                                    <input type="hidden" :name="'items[' + index + '][stock_item_id]'" :value="row.stock_item_id" required>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            x-model="row.searchQuery" 
                                            @focus="row.dropdownOpen = true"
                                            @input="row.dropdownOpen = true"
                                            @keydown.escape="row.dropdownOpen = false"
                                            @keydown.enter.prevent="if (filteredStocks(row.searchQuery).length > 0) { selectRowStock(index, filteredStocks(row.searchQuery)[0]); }"
                                            placeholder="🔍 Taip nama atau kod stok..." 
                                            class="w-full text-xs pr-6 border border-slate-300 rounded focus:ring-1 focus:ring-blue-600 bg-white"
                                        >
                                        <button 
                                            type="button" 
                                            x-show="row.searchQuery" 
                                            @click="clearRowStock(index)" 
                                            class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600 text-sm font-bold"
                                        >
                                            &times;
                                        </button>
                                    </div>

                                    <div 
                                        x-show="row.dropdownOpen" 
                                        class="absolute left-0 right-0 z-50 mt-1 max-h-56 overflow-y-auto bg-white rounded-xl shadow-xl border border-slate-200 divide-y divide-slate-100 text-xs"
                                        style="display: none;"
                                    >
                                        <template x-for="stock in filteredStocks(row.searchQuery)" :key="stock.id">
                                            <div 
                                                @click="selectRowStock(index, stock)" 
                                                class="p-2 hover:bg-blue-50 cursor-pointer transition flex items-center justify-between"
                                                :class="row.stock_item_id == stock.id ? 'bg-blue-50 font-bold' : ''"
                                            >
                                                <div class="truncate mr-2">
                                                    <span class="font-mono text-blue-700 font-bold" x-text="stock.code"></span> - <span x-text="stock.description"></span>
                                                </div>
                                                <span class="text-[10px] text-slate-500 whitespace-nowrap" x-text="stock.category"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredStocks(row.searchQuery).length === 0" class="p-3 text-center text-slate-400 text-xs">
                                            Tiada stok dijumpai
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-2 text-center">
                                <input type="number" step="1" min="0" :name="'items[' + index + '][ordered_quantity]'" x-model="row.ordered_quantity" required class="w-full text-center px-2 py-1.5 border border-slate-300 rounded">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <input type="number" step="1" min="0" :name="'items[' + index + '][do_quantity]'" x-model="row.do_quantity" required class="w-full text-center px-2 py-1.5 border border-slate-300 rounded">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <input type="number" step="1" min="0" :name="'items[' + index + '][received_quantity]'" x-model="row.received_quantity" required class="w-full text-center px-2 py-1.5 border border-slate-300 rounded font-bold text-blue-900 bg-blue-50/50">
                            </td>
                            <td class="py-2 px-2 text-right">
                                <input type="number" step="0.01" min="0" :name="'items[' + index + '][unit_price]'" x-model="row.unit_price" required class="w-full text-right px-2 py-1.5 border border-slate-300 rounded">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" :name="'items[' + index + '][batch_number]'" x-model="row.batch_number" placeholder="cth: BCH-001" class="w-full px-2 py-1.5 border border-slate-300 rounded font-mono text-[11px]">
                            </td>
                            <td class="py-2 px-2">
                                <input type="date" :name="'items[' + index + '][expiry_date]'" x-model="row.expiry_date" class="w-full px-2 py-1.5 border border-slate-300 rounded">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" @click="removeRow(index)" class="text-rose-600 hover:text-rose-900 font-bold text-sm">&times;</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan Penerimaan</label>
            <textarea name="remarks" rows="2" placeholder="Catatan fizikal bungkusan, keadaan kenderaan pengangkut dsb..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('receiving.index') }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg font-semibold">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold shadow">
                Simpan & Teruskan Ke Pemeriksaan Fizikal
            </button>
        </div>
    </form>
</div>
@endsection
