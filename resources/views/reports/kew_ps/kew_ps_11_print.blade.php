@extends('layouts.print')

@section('title', 'Jadual Verifikasi Stor (KEW.PS-11)')
@section('form_code', 'KEW.PS-11')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">JADUAL VERIFIKASI STOR BAGI TAHUN {{ $verification->year ?? date('Y') }}</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.6 - No. Jadual: <span class="font-mono font-bold">{{ $verification->verification_number ?? 'PS11/ASM/2026/0001' }}</span>)</p>
    </div>

    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                <th class="border border-slate-300 p-2 w-36">Nama Stor</th>
                <th class="border border-slate-300 p-2 w-28">Kategori Stor</th>
                <th class="border border-slate-300 p-2 w-28">Tarikh Dijadualkan</th>
                <th class="border border-slate-300 p-2">Pegawai Pemverifikasi Dilantik</th>
                <th class="border border-slate-300 p-2 w-32">Status Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-slate-300 p-2 text-center">1</td>
                <td class="border border-slate-300 p-2 font-bold">{{ $verification->store->name ?? 'Stor Utama' }}</td>
                <td class="border border-slate-300 p-2">{{ $verification->store->category ?? 'Stor Utama' }}</td>
                <td class="border border-slate-300 p-2 font-mono">{{ $verification->scheduled_date ?? date('d/m/Y') }}</td>
                <td class="border border-slate-300 p-2">
                    <div>1. {{ $verification->verifier1->name ?? 'Pegawai 1' }} ({{ $verification->verifier1->department ?? 'ASM' }})</div>
                    <div>2. {{ $verification->verifier2->name ?? 'Pegawai 2' }} ({{ $verification->verifier2->department ?? 'ASM' }})</div>
                </td>
                <td class="border border-slate-300 p-2 font-bold text-blue-900">{{ $verification->status }}</td>
            </tr>
        </tbody>
    </table>

    <div class="pt-8 text-xs">
        <div>Disediakan dan Diluluskan Oleh:</div>
        <div class="h-16"></div>
        <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Ketua Jabatan</div>
        <div>Tarikh: {{ date('d/m/Y') }}</div>
    </div>
</div>
@endsection
