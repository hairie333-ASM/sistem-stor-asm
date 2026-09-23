@extends('layouts.app')

@section('title', 'Rekod Pemeriksaan Keselamatan & Kebersihan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    category: 'SECURITY',
    score: 85,
    get status() {
        return this.score >= 80 ? 'PASS' : 'ACTION_REQUIRED';
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.7</span>
                <span class="text-xs text-slate-500">Pemeriksaan Keselamatan & Kebersihan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Daftar Pemeriksaan Keselamatan & Kebersihan</h1>
            <p class="text-sm text-slate-600">Semakan berjadual amalan keselamatan fizikal, pencegahan kebakaran, kebersihan, dan kawalan serangga.</p>
        </div>
        <a href="{{ route('safety.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
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

    <form action="{{ route('safety.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                Maklumat Asas Pemeriksaan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Diperiksa <span class="text-rose-500">*</span></label>
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
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tarikh Pemeriksaan <span class="text-rose-500">*</span></label>
                    <input type="date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Pemeriksaan <span class="text-rose-500">*</span></label>
                    <select name="category" x-model="category" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-bold">
                        <option value="SECURITY">Keselamatan Fizikal & Kawalan Kunci (AM 6.7.1)</option>
                        <option value="FIRE_SAFETY">Pencegahan & Alat Pemadam Api (AM 6.7.2)</option>
                        <option value="CLEANLINESS">Kebersihan Premis & Penyusunan Rak (AM 6.7.3)</option>
                        <option value="PEST_CONTROL">Kawalan Makhluk & Haiwan Perosak (AM 6.7.4)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Skor Pematuhan (%) <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" max="100" name="score" x-model="score" required class="w-full text-sm font-bold text-center border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keputusan Status</label>
                        <select name="status" :value="status" class="w-full text-sm font-bold border-slate-300 rounded-lg bg-slate-50">
                            <option value="PASS">Memuaskan (Lulus)</option>
                            <option value="ACTION_REQUIRED">Tindakan Pembetulan Diperlukan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Senarai Semak Pematuhan Berdasarkan Kategori -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mr-2"></span>
                Senarai Semak Pematuhan (Checklist)
            </h3>

            <!-- Checklist Security -->
            <div x-show="category === 'SECURITY'" class="space-y-3">
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">1. Pintu utama berkunci dengan mangga keselamatan bertaraf SIRIM / berjeriji.</span>
                    <input type="checkbox" name="checklist[pintu_berkunci]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">2. Peti simpanan anak kunci stor dikunci dan dipegang oleh Pegawai Stor berkuasa.</span>
                    <input type="checkbox" name="checklist[peti_kunci]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">3. Paparan tanda amaran "DILARANG MASUK KECUALI YANG BERKENAAN" dipamerkan jelas.</span>
                    <input type="checkbox" name="checklist[tanda_amaran]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">4. Buku daftar pelawat stor diselenggara dan diisi oleh setiap pelawat.</span>
                    <input type="checkbox" name="checklist[buku_pelawat]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
            </div>

            <!-- Checklist Fire Safety -->
            <div x-show="category === 'FIRE_SAFETY'" class="space-y-3" style="display: none;">
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">1. Alat pemadam api (dry powder/CO2) sah tempoh pemeriksaan Jabatan Bomba.</span>
                    <input type="checkbox" name="checklist[fire_extinguisher_valid]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">2. Laluan keluar kecemasan tidak dihalang oleh kotak atau barangan stor.</span>
                    <input type="checkbox" name="checklist[fire_exit_clear]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">3. Tanda amaran "DILARANG MEROKOK" dipamerkan dengan jelas.</span>
                    <input type="checkbox" name="checklist[no_smoking_sign]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">4. Pendawaian elektrik dalam keadaan baik tanpa sebarang sambungan haram / terdedah.</span>
                    <input type="checkbox" name="checklist[electrical_wiring]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
            </div>

            <!-- Checklist Cleanliness -->
            <div x-show="category === 'CLEANLINESS'" class="space-y-3" style="display: none;">
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">1. Lantai stor disapu, bersih dan bebas daripada habuk serta tompokan cecair/minyak.</span>
                    <input type="checkbox" name="checklist[floor_clean]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">2. Susunan stok di atas rak mengikut prinsip MDKD (Masuk Dulu, Keluar Dulu).</span>
                    <input type="checkbox" name="checklist[rack_arrangement]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">3. Kotak kosong atau sisa bungkusan dibuang serta-merta ke tong kitar semula.</span>
                    <input type="checkbox" name="checklist[empty_boxes_removed]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
            </div>

            <!-- Checklist Pest Control -->
            <div x-show="category === 'PEST_CONTROL'" class="space-y-3" style="display: none;">
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">1. Tiada tanda kehadiran tikus, anai-anai, atau serangga perosak pada kotak stok.</span>
                    <input type="checkbox" name="checklist[no_pest_signs]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">2. Jadual semburan kawalan serangga berkala dijalankan mengikut kontrak pembekal.</span>
                    <input type="checkbox" name="checklist[pest_spraying_schedule]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-800">3. Palet kayu disembur anti-anai-anai dan tidak menyentuh dinding secara rapat.</span>
                    <input type="checkbox" name="checklist[wooden_pallets_treated]" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Catatan & Tindakan Pembetulan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-2 border-b border-slate-100">Catatan & Tindakan Pembetulan Diperlukan</h3>
            <textarea name="remarks" rows="3" placeholder="Sila nyatakan sebarang ketidakpatuhan atau tindakan susulan yang mesti diambil oleh Pegawai Stor..." class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('remarks') }}</textarea>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('safety.index') }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Simpan Rekod Pemeriksaan (AM 6.7)
            </button>
        </div>
    </form>
</div>
@endsection
