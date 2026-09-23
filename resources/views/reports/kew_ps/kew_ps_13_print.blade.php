@extends('layouts.print')

@section('title', 'Sijil Verifikasi Stor (KEW.PS-13)')
@section('form_code', 'KEW.PS-13')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto border-2 border-slate-700 p-8 rounded-lg mt-4">
    <div class="text-center pb-4 border-b-2 border-slate-600 space-y-1">
        <h2 class="text-base font-extrabold uppercase tracking-widest text-slate-900">AKADEMI SAINS MALAYSIA</h2>
        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-950">SIJIL VERIFIKASI STOR</h3>
        <p class="text-xs font-mono font-bold text-slate-700">NO. SIJIL: {{ $verification->cert_number ?? 'KEW.PS-13/ASM/2026/001' }}</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed pt-2">
        <p class="text-center font-serif text-sm italic text-slate-700">
            Adalah dengan ini diperakui bahawa:
        </p>

        <div class="p-4 bg-slate-50 border border-slate-300 rounded text-center space-y-2 font-semibold text-slate-900">
            <div class="text-sm font-bold text-blue-900">{{ $verification->store->name ?? 'STOR UTAMA ASM' }}</div>
            <div class="text-xs text-slate-600">Kategori: {{ $verification->store->category ?? 'Stor Utama' }}</div>
            <div class="text-xs text-slate-600">Tahun Kewangan: {{ $verification->year ?? date('Y') }}</div>
        </div>

        <p class="text-justify">
            Telah diperiksa dan diverifikasi secara fizikal 100% oleh Pegawai Pemverifikasi Stor yang dilantik selaras dengan peruntukan <strong>Tatacara Pengurusan Stor Kerajaan (TPS AM 6.6)</strong>.
        </p>

        <p class="text-justify">
            Pemeriksaan mendapati rekod transaksi di dalam Daftar Stok (KEW.PS-3) dan Kad Petak (KEW.PS-4) adalah teratur dan selaras dengan kuantiti stok fizikal sebenar (termasuk tindakan pelarasan yang diluluskan).
        </p>

        <div class="pt-10 flex justify-between items-end">
            <div>
                <div>Tarikh Dikeluarkan:</div>
                <div class="font-bold font-mono">{{ $verification->approved_at ? \Carbon\Carbon::parse($verification->approved_at)->format('d F Y') : date('d F Y') }}</div>
            </div>

            <div class="text-right">
                <div class="h-16"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan / Pegawai Pengawal</div>
                <div class="font-bold text-slate-800">{{ $verification->approvalOfficer->name ?? 'Ketua Pegawai Eksekutif' }}</div>
                <div class="text-slate-500">Akademi Sains Malaysia</div>
            </div>
        </div>
    </div>
</div>
@endsection
