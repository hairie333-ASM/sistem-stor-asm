@extends('layouts.app')

@section('title', 'Pusat Laporan & Borang KEW.PS (TPS)')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.1 – AM 6.10</span>
                <span class="text-xs text-slate-500">Pekeliling Perbendaharaan Malaysia</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Direktori Borang & Laporan Rasmi KEW.PS</h1>
            <p class="text-sm text-slate-600">Pusat sehenti akses, janaan cetakan berformat kerajaan dan pengesahan borang KEW.PS-1 hingga KEW.PS-36 bagi tahun {{ $currentYear }}.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.kew-ps-14') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Kedudukan Stok Suku Tahun (KEW.PS-14)
            </a>
            <a href="{{ route('reports.stock-valuation') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                Penilaian Stok Semasa
            </a>
        </div>
    </div>

    <!-- Quick Analytic Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('reports.kew-ps-14') }}" class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 hover:border-blue-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">KEW.PS-14</span>
                <span class="text-xs text-slate-400">Tahunan / Suku Tahun</span>
            </div>
            <div class="mt-3 text-lg font-bold text-slate-800">Kedudukan Stok & Pusingan</div>
            <div class="text-xs text-slate-500 mt-1">Formula AM 6.6 Kadar Pusingan Stok (Sasaran ≥ 4.0)</div>
        </a>

        <a href="{{ route('reports.stock-valuation') }}" class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 hover:border-emerald-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded">PENILAIAN</span>
                <span class="text-xs text-slate-400">Semasa</span>
            </div>
            <div class="mt-3 text-lg font-bold text-slate-800">Ringkasan Nilai Stok</div>
            <div class="text-xs text-slate-500 mt-1">Baki nilai keseluruhan stok mengikut Kategori & Kumpulan A/B</div>
        </a>

        <a href="{{ route('reports.reorder') }}" class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded">PARAS STOK</span>
                <span class="text-xs text-slate-400">AM 6.3</span>
            </div>
            <div class="mt-3 text-lg font-bold text-slate-800">Laporan Menokok Stok</div>
            <div class="text-xs text-slate-500 mt-1">Senarai item di bawah paras pesanan semula & paras minimum</div>
        </a>

        <a href="{{ route('stock.expiry') }}" class="p-5 bg-white rounded-xl shadow-sm border border-slate-200 hover:border-rose-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded">KEW.PS-6</span>
                <span class="text-xs text-slate-400">Kawalan Luput</span>
            </div>
            <div class="mt-3 text-lg font-bold text-slate-800">Pemantauan Tarikh Luput</div>
            <div class="text-xs text-slate-500 mt-1">Kawalan stok luput 6 bulan, 3 bulan, dan telah tamat tempoh</div>
        </a>
    </div>

    <!-- Statutory Forms Directory Accordion / Grid -->
    <div class="space-y-6">
        <h2 class="text-lg font-bold text-slate-800 flex items-center">
            <span class="w-3 h-3 rounded-full bg-blue-600 mr-2.5"></span>
            Senarai Lengkap Borang Rasmi TPS (AM 6.1 – AM 6.10)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- 1. AM 6.2 Penerimaan -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700">AM 6.2 — Penerimaan</span>
                    <span class="text-xs text-slate-400">2 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-1</span>
                            <div class="text-xs text-slate-500">Borang Terimaan Barang-Barang (BTB)</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-1') }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-2</span>
                            <div class="text-xs text-slate-500">Borang Penolakan Barang-Barang (BPB)</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-2') }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline">Cetak</a>
                    </li>
                </ul>
            </div>

            <!-- 2. AM 6.3 Rekod Stok -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">AM 6.3 — Rekod Stok</span>
                    <span class="text-xs text-slate-400">4 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-3</span>
                            <div class="text-xs text-slate-500">Daftar Stok (Bahagian A & B)</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-3') }}" target="_blank" class="text-xs font-semibold text-emerald-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-4</span>
                            <div class="text-xs text-slate-500">Kad Petak (Bin Card Stor)</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-4') }}" target="_blank" class="text-xs font-semibold text-emerald-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-5</span>
                            <div class="text-xs text-slate-500">Senarai Stok Kumpulan A & B</div>
                        </div>
                        <a href="{{ route('stock.group-ab') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Buka</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-6</span>
                            <div class="text-xs text-slate-500">Kedudukan Stok Tarikh Luput</div>
                        </div>
                        <a href="{{ route('stock.expiry') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Buka</a>
                    </li>
                </ul>
            </div>

            <!-- 3. AM 6.5 Pengeluaran -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700">AM 6.5 — Pengeluaran</span>
                    <span class="text-xs text-slate-400">3 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-7</span>
                            <div class="text-xs text-slate-500">Borang Pesanan Antara Stor</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-7') }}" target="_blank" class="text-xs font-semibold text-purple-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-8</span>
                            <div class="text-xs text-slate-500">Borang Pesanan Individu/Bahagian</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-8') }}" target="_blank" class="text-xs font-semibold text-purple-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-9</span>
                            <div class="text-xs text-slate-500">Borang Pembungkusan Stok</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-9') }}" target="_blank" class="text-xs font-semibold text-purple-600 hover:underline">Cetak</a>
                    </li>
                </ul>
            </div>

            <!-- 4. AM 6.6 Verifikasi & Pelarasan -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">AM 6.6 — Verifikasi</span>
                    <span class="text-xs text-slate-400">7 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-10</span>
                            <div class="text-xs text-slate-500">Pelantikan Pemverifikasi Stor</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-10') }}" target="_blank" class="text-xs font-semibold text-amber-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-11</span>
                            <div class="text-xs text-slate-500">Jadual Verifikasi Stor</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-11') }}" target="_blank" class="text-xs font-semibold text-amber-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-12</span>
                            <div class="text-xs text-slate-500">Laporan Verifikasi Stor</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-12') }}" target="_blank" class="text-xs font-semibold text-amber-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-13</span>
                            <div class="text-xs text-slate-500">Sijil Verifikasi Stor</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-13') }}" target="_blank" class="text-xs font-semibold text-amber-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-14</span>
                            <div class="text-xs text-slate-500">Laporan Kedudukan Stok Tahunan</div>
                        </div>
                        <a href="{{ route('reports.kew-ps-14') }}" class="text-xs font-semibold text-amber-600 hover:underline">Buka</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-15 & 16</span>
                            <div class="text-xs text-slate-500">Laporan & Kelulusan Pelarasan</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-15') }}" target="_blank" class="text-xs font-semibold text-amber-600 hover:underline">Cetak</a>
                    </li>
                </ul>
            </div>

            <!-- 5. AM 6.8 Pindahan Stok -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-cyan-700">AM 6.8 — Pindahan</span>
                    <span class="text-xs text-slate-400">2 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-17</span>
                            <div class="text-xs text-slate-500">Borang Pindahan Stok Antara Stor</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-17') }}" target="_blank" class="text-xs font-semibold text-cyan-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-18</span>
                            <div class="text-xs text-slate-500">Laporan Tahunan Pindahan Stok</div>
                        </div>
                        <a href="{{ route('transfers.report-ps18') }}" target="_blank" class="text-xs font-semibold text-cyan-600 hover:underline">Cetak</a>
                    </li>
                </ul>
            </div>

            <!-- 6. AM 6.9 Pelupusan Stok -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">AM 6.9 — Pelupusan</span>
                    <span class="text-xs text-slate-400">13 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-19</span>
                            <div class="text-xs text-slate-500">Pelantikan Lembaga Pemeriksa</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-19') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-20</span>
                            <div class="text-xs text-slate-500">Laporan Lembaga Pemeriksa</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-20') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-21</span>
                            <div class="text-xs text-slate-500">Surat Kelulusan Pelupusan</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-21') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-22 & 23</span>
                            <div class="text-xs text-slate-500">Sijil & Laporan Tahunan Pelupusan</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-22') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-24 hingga 31</span>
                            <div class="text-xs text-slate-500">Kenyataan Tender, Hadiah, Sisa & Musnah</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-24') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat</a>
                    </li>
                </ul>
            </div>

            <!-- 7. AM 6.10 Kehilangan & Hapus Kira -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700">AM 6.10 — Hapus Kira</span>
                    <span class="text-xs text-slate-400">5 Borang</span>
                </div>
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-32</span>
                            <div class="text-xs text-slate-500">Laporan Awal Kehilangan Stok</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-32') }}" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-33</span>
                            <div class="text-xs text-slate-500">Pelantikan Jawatankuasa Penyiasat</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-33') }}" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-34</span>
                            <div class="text-xs text-slate-500">Laporan Akhir Kehilangan Stok</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-34') }}" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">Cetak</a>
                    </li>
                    <li class="flex items-center justify-between hover:bg-slate-50 p-1.5 rounded transition">
                        <div>
                            <span class="font-bold text-slate-800">KEW.PS-35 & 36</span>
                            <div class="text-xs text-slate-500">Sijil & Laporan Tahunan Hapus Kira</div>
                        </div>
                        <a href="{{ route('reports.form', 'kew-ps-35') }}" target="_blank" class="text-xs font-semibold text-rose-600 hover:underline">Cetak</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
