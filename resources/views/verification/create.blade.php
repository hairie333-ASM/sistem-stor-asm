@extends('layouts.app')

@section('title', 'Jadual Verifikasi Stor (KEW.PS-11)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.6</span>
                <span class="text-xs text-slate-500">Pelantikan & Jadual Verifikasi</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Daftar Jadual Verifikasi Stor (KEW.PS-11)</h1>
            <p class="text-sm text-slate-600">Lantik dua (2) orang Pegawai Pemverifikasi Bebas mengikut Surat Pelantikan KEW.PS-10.</p>
        </div>
        <a href="{{ route('verification.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
            Kembali ke Senarai
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
        <div class="font-bold mb-1">Sila perbetulkan ralat berikut:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('verification.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                Maklumat Verifikasi Stor
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Yang Akan Diperiksa <span class="text-rose-500">*</span></label>
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
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun Verifikasi <span class="text-rose-500">*</span></label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" min="2020" max="2050" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Rujukan Surat Pelantikan (KEW.PS-10) <span class="text-rose-500">*</span></label>
                    <input type="text" name="appointment_letter_ref" value="{{ old('appointment_letter_ref', 'ASM/STOR/PELANTIKAN/' . date('Y') . '/001') }}" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tarikh Pemeriksaan Dijadualkan <span class="text-rose-500">*</span></label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Pegawai Pemverifikasi Bebas -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <h4 class="text-sm font-bold text-slate-800">Pelantikan 2 Pegawai Pemverifikasi Bebas (AM 6.6)</h4>
                </div>
                <p class="text-xs text-slate-500">Pegawai yang dilantik tidak boleh bertugas di stor yang diverifikasi dan tidak mengurus transaksi stor tersebut.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pegawai Pemverifikasi 1 <span class="text-rose-500">*</span></label>
                        <select name="verifier_1_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Pegawai 1 --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('verifier_1_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->position ?? $u->role->name }}) - {{ $u->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pegawai Pemverifikasi 2 <span class="text-rose-500">*</span></label>
                        <select name="verifier_2_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Pegawai 2 --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('verifier_2_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->position ?? $u->role->name }}) - {{ $u->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('verification.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Daftar Jadual & Lantik Pemverifikasi
            </button>
        </div>
    </form>
</div>
@endsection
