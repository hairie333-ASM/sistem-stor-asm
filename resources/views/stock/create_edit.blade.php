@extends('layouts.app')

@php
    $isEdit = isset($stock);
@endphp

@section('title', $isEdit ? 'Kemaskini Item Stok: ' . $stock->stock_code : 'Daftar Item Stok Baru')
@section('page_title', $isEdit ? 'Kemaskini Maklumat Item Stok' : 'Pendaftaran Item Stok Baru')
@section('page_description', 'Borang pendaftaran katalog stok stor kerajaan mengikut spesifikasi TPS AM 6.1')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
    <form method="POST" action="{{ $isEdit ? route('stock.update', $stock) : route('stock.store') }}" class="space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="border-b border-slate-200 pb-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">A. Maklumat Pengenalan Stok</h3>
            <p class="text-xs text-slate-500">Kod stok, perihal dan kategori item.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">No. Kod Stok (Stock Code) *</label>
                <input type="text" name="stock_code" value="{{ old('stock_code', $stock->stock_code ?? '') }}" {{ $isEdit ? 'readonly' : 'required' }} placeholder="cth: ASM-AT-009" class="w-full px-3 py-2 border border-slate-300 rounded-lg {{ $isEdit ? 'bg-slate-100 text-slate-600' : '' }} focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">No. Kad KEW.PS-3</label>
                <input type="text" name="kad_no" value="{{ old('kad_no', $stock->kad_no ?? '') }}" placeholder="cth: KAD-2026-009" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">Perihal Stok (Description) *</label>
                <input type="text" name="description" value="{{ old('description', $stock->description ?? '') }}" required placeholder="cth: Kertas Fotokopi A4 80gsm Putih" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kategori Stok *</label>
                <select name="category_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $stock->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Unit Pengukuran (UOM) *</label>
                <select name="uom_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">Pilih Unit</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ old('uom_id', $stock->uom_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-b border-slate-200 pb-3 pt-2">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">B. Klasifikasi TPS, Lokasi & Paras Stok</h3>
            <p class="text-xs text-slate-500">Penentuan Kumpulan A/B, kadar pergerakan dan paras 3-2-1 bulan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kumpulan TPS (AM 6.3) *</label>
                <select name="stock_group" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="A" {{ old('stock_group', $stock->stock_group ?? 'B') == 'A' ? 'selected' : '' }}>Kumpulan A (Nilai Tertinggi 30%)</option>
                    <option value="B" {{ old('stock_group', $stock->stock_group ?? 'B') == 'B' ? 'selected' : '' }}>Kumpulan B (Baki 70%)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pergerakan Stok (Movement) *</label>
                <select name="movement" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="CEPAT" {{ old('movement', $stock->movement ?? 'CEPAT') == 'CEPAT' ? 'selected' : '' }}>CEPAT (Fast Moving)</option>
                    <option value="PERLAHAN" {{ old('movement', $stock->movement ?? 'CEPAT') == 'PERLAHAN' ? 'selected' : '' }}>PERLAHAN (Slow Moving)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Lokasi Default</label>
                <select name="default_location_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">Pilih Lokasi Rak</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('default_location_id', $stock->default_location_id ?? '') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->full_code }} ({{ $loc->store->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-rose-700 mb-1">Paras Minimum (1 Bulan) *</label>
                <input type="number" step="1" name="min_level" value="{{ old('min_level', $stock->min_level ?? 10) }}" required class="w-full px-3 py-2 border border-rose-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-amber-700 mb-1">Paras Menokok (2 Bulan) *</label>
                <input type="number" step="1" name="reorder_level" value="{{ old('reorder_level', $stock->reorder_level ?? 20) }}" required class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Paras Maksimum (3 Bulan) *</label>
                <input type="number" step="1" name="max_level" value="{{ old('max_level', $stock->max_level ?? 30) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Harga Seunit Semasa (RM) *</label>
                <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $stock->unit_price ?? '0.00') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">Nama Pembekal Utama</label>
                <input type="text" name="supplier_name" value="{{ old('supplier_name', $stock->supplier_name ?? '') }}" placeholder="cth: Syarikat Pembekal Utama Sdn Bhd" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        </div>

        <div class="border-b border-slate-200 pb-3 pt-2">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">C. Kawalan Khas & Catatan</h3>
            <p class="text-xs text-slate-500">Kawalan tarikh luput, kelompok (batch) dan nombor siri.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <label class="flex items-center space-x-2 p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" name="is_expiry_controlled" value="1" {{ old('is_expiry_controlled', $stock->is_expiry_controlled ?? false) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-800">Kawalan Tarikh Luput</span>
            </label>

            <label class="flex items-center space-x-2 p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" name="is_batch_controlled" value="1" {{ old('is_batch_controlled', $stock->is_batch_controlled ?? false) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-800">Kawalan No. Kelompok (Batch)</span>
            </label>

            <label class="flex items-center space-x-2 p-3 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer">
                <input type="checkbox" name="is_serial_controlled" value="1" {{ old('is_serial_controlled', $stock->is_serial_controlled ?? false) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-slate-800">Kawalan Nombor Siri</span>
            </label>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
            <textarea name="remarks" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('remarks', $stock->remarks ?? '') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('stock.index') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-semibold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition">
                {{ $isEdit ? 'Kemaskini Item Stok' : 'Daftar Item Stok' }}
            </button>
        </div>
    </form>
</div>
@endsection
