@extends('layouts.app')

@section('title', 'Jana Borang Pembungkusan (KEW.PS-9)')
@section('page_title', 'Borang Pembungkusan Stok (KEW.PS-9)')
@section('page_description', 'Menyediakan butiran pembungkusan dan arahan pengendalian khas bagi penghantaran stok (AM 6.5)')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
    <form method="POST" action="{{ route('requests.packing.store', $stockRequest) }}" class="space-y-6 text-xs">
        @csrf

        <div class="border-b border-slate-200 pb-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">A. Maklumat Bungkusan & Penghantaran</h3>
            <p class="text-xs text-slate-500">Bagi pesanan stok: <strong class="font-mono text-blue-700">{{ $stockRequest->request_number }}</strong></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Bungkusan *</label>
                <select name="package_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="KOTAK KADBOD">Kotak Kadbod</option>
                    <option value="PETI KAYU">Peti Kayu</option>
                    <option value="SAMPUL TEBAL">Sampul Tebal Bergelembung</option>
                    <option value="BEKAS PLASTIK">Bekas Plastik Bertutup</option>
                    <option value="PALET">Palet Berbalut Filem Regang</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Berat Kasar Bungkusan (kg)</label>
                <input type="number" step="0.1" name="weight_kg" value="2.5" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Ukuran Dimensi (cm)</label>
                <input type="text" name="dimensions" value="30 x 25 x 20 cm" placeholder="cth: 40 x 30 x 20 cm" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Penerima</label>
                <input type="text" readonly value="{{ $stockRequest->requester->name }} ({{ $stockRequest->department }})" class="w-full px-3 py-2 border border-slate-200 bg-slate-100 rounded-lg text-slate-600">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1">Alamat / Lokasi Serahan Lengkap *</label>
                <textarea name="delivery_address" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ $stockRequest->department }}, Akademi Sains Malaysia, Tingkat 20 Menara MATRADE, Kuala Lumpur</textarea>
            </div>
        </div>

        <div class="border-b border-slate-200 pb-3 pt-2">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">B. Arahan Pengendalian Khas (Handling Instructions)</h3>
            <p class="text-xs text-slate-500">Tandakan simbol atau arahan keselamatan pengendalian bungkusan.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="FRAGILE" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">🍷 Mudah Pecah</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="KEEP_DRY" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">☂️ Pastikan Kering</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="KEEP_UPRIGHT" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">⬆️ Menegak Ke Atas</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="KEEP_COOL" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">❄️ Kawalan Suhu</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="HEAVY" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">🏋️ Berat</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="CHEMICAL" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">🧪 Bahan Kimia</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="CORROSIVE" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">⚠️ Mengkakis</span>
            </label>
            <label class="flex items-center space-x-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer">
                <input type="checkbox" name="handling_instructions[]" value="POISON" class="rounded text-blue-600">
                <span class="font-medium text-slate-800">☠️ Racun / Toksik</span>
            </label>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('requests.show', $stockRequest) }}" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg font-semibold">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold shadow">
                Jana Borang KEW.PS-9 & Label Bungkusan
            </button>
        </div>
    </form>
</div>
@endsection
