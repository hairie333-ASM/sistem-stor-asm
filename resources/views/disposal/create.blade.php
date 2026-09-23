@extends('layouts.app')

@section('title', 'Cadangan Pelupusan Stok (KEW.PS-20)')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    committees: [
        { officer_name: 'Ir. Ahmad Zaki', position: 'Pengarah Seksyen', department: 'ASM Pengurusan', role: 'PENGERUSI' },
        { officer_name: 'Siti Sarah', position: 'Pegawai Penyelidik', department: 'ASM Operasi', role: 'AHLI' }
    ],
    items: [
        { stock_item_id: '', quantity: 1, condition: 'USANG', justification: 'Stok lapuk melebihi 3 tahun dan tidak boleh diguna', recommended_method: 'SCRAP' }
    ],
    addCommittee() {
        this.committees.push({ officer_name: '', position: '', department: '', role: 'AHLI' });
    },
    removeCommittee(index) {
        if (this.committees.length > 2) {
            this.committees.splice(index, 1);
        }
    },
    addItem() {
        this.items.push({ stock_item_id: '', quantity: 1, condition: 'USANG', justification: '', recommended_method: 'SCRAP' });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.9</span>
                <span class="text-xs text-slate-500">Lembaga Pemeriksa Pelupusan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Laporan Pemeriksaan Pelupusan (KEW.PS-20)</h1>
            <p class="text-sm text-slate-600">Daftar stok yang disyorkan untuk dilupuskan serta senarai Lembaga Pemeriksa (KEW.PS-19).</p>
        </div>
        <a href="{{ route('disposal.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
            Kembali
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

    <form action="{{ route('disposal.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Store & Master Method -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                Maklumat Pelupusan & Lembaga Pemeriksa
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Terlibat <span class="text-rose-500">*</span></label>
                    <select name="store_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Stor --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ old('store_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->code }} - {{ $st->name }} ({{ $st->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kaedah Utama Pelupusan <span class="text-rose-500">*</span></label>
                    <select name="disposal_method" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-bold">
                        <option value="SCRAP">Jualan Sisa / Barangan Lusuh (KEW.PS-26)</option>
                        <option value="SALE">Tender / Sebut Harga (KEW.PS-24/25)</option>
                        <option value="DESTROY">Musnah / Buang / Tanam (KEW.PS-30)</option>
                        <option value="GIFT">Hadiah / Sumbangan (KEW.PS-29)</option>
                        <option value="EXCHANGE">Tukar Barang / Perkhidmatan (KEW.PS-28)</option>
                        <option value="TRADE_IN">Tukar Beli (Trade-In) (KEW.PS-27)</option>
                        <option value="SCHEDULED_WASTE">Buangan Terjadual (E-Waste / Alam Sekitar)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rujukan Surat Lantikan (KEW.PS-19) <span class="text-rose-500">*</span></label>
                    <input type="text" name="committee_appointment_ref" value="{{ old('committee_appointment_ref', 'ASM/PELUPUSAN/LANTIKAN/' . date('Y') . '/001') }}" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Lembaga Pemeriksa (KEW.PS-19) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>
                        Ahli Lembaga Pemeriksa Pelupusan (Minimum 2 Orang)
                    </h3>
                    <p class="text-xs text-slate-500">Pegawai pemeriksa yang dilantik di bawah KEW.PS-19 untuk memeriksa keadaan fizikal stok.</p>
                </div>
                <button type="button" @click="addCommittee()" class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold rounded-lg border border-amber-200 transition">
                    + Tambah Pegawai
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(comm, index) in committees" :key="index">
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pegawai</label>
                            <input type="text" :name="`committees[${index}][officer_name]`" x-model="comm.officer_name" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Jawatan</label>
                            <input type="text" :name="`committees[${index}][position]`" x-model="comm.position" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Bahagian / Agensi</label>
                            <input type="text" :name="`committees[${index}][department]`" x-model="comm.department" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Peranan</label>
                            <select :name="`committees[${index}][role]`" x-model="comm.role" class="w-full text-xs border-slate-300 rounded-lg bg-white">
                                <option value="PENGERUSI">Pengerusi</option>
                                <option value="AHLI">Ahli</option>
                                <option value="SAKSI">Saksi</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 text-center">
                            <button type="button" @click="removeCommittee(index)" :disabled="committees.length <= 2" class="p-2 text-rose-500 hover:text-rose-700 disabled:opacity-25">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Senarai Item Pelupusan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600 mr-2"></span>
                        Senarai Stok Disyorkan Untuk Pelupusan
                    </h3>
                    <p class="text-xs text-slate-500">Kuantiti yang dipilih akan dipindahkan secara automatik ke kuantiti pelupusan (diasingkan dari baki sedia ada).</p>
                </div>
                <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 text-xs font-bold rounded-lg border border-rose-200 transition">
                    + Tambah Stok
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Perihal Stok</label>
                            <select :name="`items[${index}][stock_item_id]`" x-model="item.stock_item_id" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                                <option value="">-- Pilih Stok --</option>
                                @foreach($stockItems as $stock)
                                    <option value="{{ $stock->id }}">{{ $stock->item_code }} - {{ $stock->description }} (Baki: {{ number_format($stock->current_quantity) }} | RM {{ number_format($stock->unit_price, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kuantiti Lupus</label>
                            <input type="number" step="0.01" min="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" required class="w-full text-xs border-slate-300 rounded-lg bg-white font-bold text-rose-700">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Keadaan Stok</label>
                            <select :name="`items[${index}][condition]`" x-model="item.condition" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                                <option value="USANG">Usang / Lapuk</option>
                                <option value="ROSAK">Rosak Tidak Ekonomik</option>
                                <option value="EXPIRED">Tamat Tempoh Luput</option>
                                <option value="TIDAK_DIPERLUKAN">Tidak Diperlukan</option>
                                <option value="LEBIHAN">Lebihan Stok</option>
                                <option value="LAIN_LAIN">Lain-lain</option>
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Justifikasi Pelupusan</label>
                            <input type="text" placeholder="Sebab disyorkan..." :name="`items[${index}][justification]`" x-model="item.justification" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>

                        <div class="md:col-span-1 text-center">
                            <button type="button" @click="removeItem(index)" :disabled="items.length <= 1" class="p-2 text-rose-500 hover:text-rose-700 disabled:opacity-25">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('disposal.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" onclick="return confirm('Hantar permohonan pelupusan ini kepada Lembaga Pemeriksa Pelupusan (KEW.PS-20)?')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Hantar Cadangan Pelupusan (KEW.PS-20)
            </button>
        </div>
    </form>
</div>
@endsection
