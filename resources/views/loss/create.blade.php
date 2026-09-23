@extends('layouts.app')

@section('title', 'Laporan Awal Kehilangan Stok (KEW.PS-32)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    items: [
        { stock_item_id: '', quantity: 1, circumstances: '' }
    ],
    addItem() {
        this.items.push({ stock_item_id: '', quantity: 1, circumstances: '' });
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
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-rose-100 text-rose-800 border border-rose-300">TPS AM 6.10</span>
                <span class="text-xs text-slate-500">Laporan Awal Kehilangan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Laporan Awal Kehilangan Stok (KEW.PS-32)</h1>
            <p class="text-sm text-slate-600">Laporkan sebarang kehilangan stok dalam tempoh 2 hari bekerja dari tarikh dikesan mengikut AM 6.10.</p>
        </div>
        <a href="{{ route('loss.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
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

    <form action="{{ route('loss.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Maklumat Kejadian -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-600 mr-2"></span>
                Maklumat Kejadian & Penemuan Kehilangan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Terlibat <span class="text-rose-500">*</span></label>
                    <select name="store_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                        <option value="">-- Pilih Stor --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ old('store_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->code }} - {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tarikh Kejadian (Dianggarkan) <span class="text-rose-500">*</span></label>
                    <input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tarikh Dikesan / Diketahui <span class="text-rose-500">*</span></label>
                    <input type="date" name="discovery_date" value="{{ old('discovery_date', date('Y-m-d')) }}" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Perihal & Cara Kehilangan Dikesan <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Jelaskan bagaimana kehilangan dikesan, lokasi sebenar kejadian dan tanda pencerobohan jika ada..." class="w-full text-sm border-slate-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Maklumat Laporan Polis (Wajib jika berlaku kecurian/pencerobohan) -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Maklumat Laporan Polis (Jika Berlaku Pencerobohan/Jenayah)</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">No. Laporan Polis</label>
                        <input type="text" name="police_report_no" value="{{ old('police_report_no') }}" placeholder="Contoh: TRA/01234/26" class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Tarikh Laporan Polis</label>
                        <input type="date" name="police_report_date" value="{{ old('police_report_date') }}" class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Status Tindakan Polis</label>
                        <input type="text" name="police_action_status" value="{{ old('police_action_status') }}" placeholder="Contoh: Dalam siasatan IPD" class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Senarai Item Kehilangan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600 mr-2"></span>
                    Senarai Stok Yang Hilang
                </h3>
                <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 text-xs font-bold rounded-lg border border-rose-200 transition">
                    + Tambah Stok
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-5">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Perihal Stok</label>
                            <select :name="`items[${index}][stock_item_id]`" x-model="item.stock_item_id" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                                <option value="">-- Pilih Stok --</option>
                                @foreach($stockItems as $stock)
                                    <option value="{{ $stock->id }}">{{ $stock->item_code }} - {{ $stock->description }} (RM {{ number_format($stock->unit_price, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kuantiti Hilang</label>
                            <input type="number" step="0.01" min="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" required class="w-full text-xs border-slate-300 rounded-lg bg-white font-bold text-rose-700">
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Keadaan / Perincian Kehilangan</label>
                            <input type="text" placeholder="Contoh: Hilang dari almari berkunci..." :name="`items[${index}][circumstances]`" x-model="item.circumstances" class="w-full text-xs border-slate-300 rounded-lg bg-white">
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
            <a href="{{ route('loss.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" onclick="return confirm('Kemukakan Laporan Awal Kehilangan Stok (KEW.PS-32)?')" class="px-6 py-2.5 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-sm font-bold shadow transition">
                Hantar Laporan Awal (KEW.PS-32)
            </button>
        </div>
    </form>
</div>
@endsection
