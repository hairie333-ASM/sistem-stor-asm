@extends('layouts.app')

@section('title', 'Daftar Lokasi Penyimpanan Baru')
@section('page_title', 'Pendaftaran Lokasi Penyimpanan (AM 6.4)')
@section('page_description', 'Membina kod lokasi hierarki bagi memudahkan carian stok dan sistem Masuk-Dahulu-Keluar-Dahulu (MDKD)')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
    <form method="POST" action="{{ route('locations.store') }}" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Pilih Stor *</label>
            <select name="store_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @foreach($stores as $st)
                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Seksyen / Gudang (Warehouse / Section)</label>
            <select name="section_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <option value="">Pilih Seksyen</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }} ({{ $sec->code }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Baris (Row) *</label>
                <input type="text" name="row" value="{{ old('row', '01') }}" required placeholder="cth: 01" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Rak (Rack) *</label>
                <input type="text" name="rack" value="{{ old('rack', 'RA01') }}" required placeholder="cth: RA01" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tingkat (Level) *</label>
                <input type="text" name="level" value="{{ old('level', '01') }}" required placeholder="cth: 01" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Petak (Bin) *</label>
                <input type="text" name="bin" value="{{ old('bin', '01') }}" required placeholder="cth: 01" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Kapasiti Anggaran (Unit)</label>
            <input type="number" name="capacity" value="{{ old('capacity', 500) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs">
            <strong>Contoh Penjanaan Kod Penuh:</strong> Kod lokasi berformat rasmi seperti <code class="font-bold">STR-UTAMA-SEC-A-01-RA01-01-01</code> akan dijana secara automatik.
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('locations.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-semibold">Batal</a>
            <button type="submit" class="px-5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold shadow">Daftar Lokasi</button>
        </div>
    </form>
</div>
@endsection
