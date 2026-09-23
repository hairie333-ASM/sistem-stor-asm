@extends('layouts.print')

@section('title', 'Surat Pelantikan Pemverifikasi Stor (KEW.PS-10)')
@section('form_code', 'KEW.PS-10')

@section('content')
<div class="space-y-6">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SURAT PELANTIKAN PEGAWAI PEMVERIFIKASI STOR (KEW.PS-10)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.6 - Rujukan: <span class="font-mono font-bold">{{ $verification->appointment_letter_ref ?? 'ASM/STOR/LANTIKAN/2026/001' }}</span>)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="flex justify-between">
            <div>
                <div>Kepada:</div>
                <div class="font-bold text-slate-800">1. {{ $verification->verifier1->name ?? 'Pegawai 1' }} ({{ $verification->verifier1->position ?? 'Pegawai' }})</div>
                <div class="font-bold text-slate-800">2. {{ $verification->verifier2->name ?? 'Pegawai 2' }} ({{ $verification->verifier2->position ?? 'Pegawai' }})</div>
                <div class="text-slate-500">Akademi Sains Malaysia</div>
            </div>
            <div class="text-right">
                <div>Tarikh Surat: {{ date('d F Y') }}</div>
            </div>
        </div>

        <div class="pt-4">
            <div class="font-bold uppercase text-[11px]">TUAN/PUAN,</div>
            <div class="font-bold uppercase text-[12px] text-blue-950 mt-1">
                PELANTIKAN SEBAGAI PEGAWAI PEMVERIFIKASI STOR AKADEMI SAINS MALAYSIA BAGI TAHUN {{ $verification->year ?? date('Y') }}
            </div>
        </div>

        <p>
            Dengan hormatnya saya merujuk kepada perkara di atas dan Tatacara Pengurusan Stor Kerajaan (TPS AM 6.6).
        </p>

        <p>
            2. Sukacita dimaklumkan bahawa tuan/puan dengan ini dilantik sebagai <strong>Pegawai Pemverifikasi Stor</strong> untuk melaksanakan verifikasi tahunan bagi stor berikut:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Nama Stor: {{ $verification->store->name ?? 'Stor Utama ASM' }}</div>
            <div>Kategori Stor: {{ $verification->store->category ?? 'Stor Utama' }}</div>
            <div>Tarikh Pemeriksaan Dijadualkan: {{ $verification->scheduled_date ?? date('d/m/Y') }}</div>
        </div>

        <p>
            3. Tanggungjawab tuan/puan merangkumi:
        </p>
        <ul class="list-disc list-inside space-y-1 pl-4">
            <li>Membuat kiraan fizikal 100% ke atas semua stok yang disimpan di dalam stor.</li>
            <li>Membandingkan baki fizikal sebenar dengan baki di dalam Kad Petak (KEW.PS-4) dan Daftar Stok (KEW.PS-3).</li>
            <li>Memeriksa keadaan stok sama ada baik, rosak, usang atau tidak bergerak.</li>
            <li>Menyediakan dan menandatangani Laporan Verifikasi Stor (KEW.PS-12) untuk dikemukakan kepada Ketua Jabatan.</li>
        </ul>

        <p>
            Sekian, terima kasih.
        </p>

        <div class="pt-8">
            <div class="font-bold">"MALAYSIA MADANI"</div>
            <div class="font-bold">"BERKHIDMAT UNTUK NEGARA"</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Ketua Jabatan / Pegawai Pengawal</div>
            <div>Akademi Sains Malaysia</div>
        </div>
    </div>
</div>
@endsection
