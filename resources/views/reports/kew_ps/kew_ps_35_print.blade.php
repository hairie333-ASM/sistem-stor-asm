@extends('layouts.print')

@section('title', 'Sijil Hapus Kira Stok (KEW.PS-35)')
@section('form_code', 'KEW.PS-35')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto border-2 border-slate-700 p-8 rounded-lg mt-4">
    <div class="text-center pb-4 border-b-2 border-slate-600 space-y-1">
        <h2 class="text-base font-extrabold uppercase tracking-widest text-slate-900">AKADEMI SAINS MALAYSIA</h2>
        <h3 class="text-sm font-bold uppercase tracking-wider text-rose-950">SIJIL HAPUS KIRA STOK KERAJAAN</h3>
        <p class="text-xs font-mono font-bold text-slate-700">NO. SIJIL: {{ $lossCase->write_off_cert_number ?? 'KEW.PS-35/ASM/2026/001' }}</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <p class="text-justify">
            Saya mengesahkan bahawa kelulusan hapus kira kehilangan stok telah diberi oleh <strong>Pegawai Pengawal / Kuasa Melulus Perbendaharaan</strong> bagi kes kehilangan berikut:
        </p>

        <div class="p-4 bg-slate-50 border border-slate-300 rounded space-y-1.5 font-semibold">
            <div>No. Rujukan Laporan Akhir (KEW.PS-34): <span class="font-mono text-blue-900">{{ $lossCase->final_report_ref ?? '-' }}</span></div>
            <div>Stor Terlibat: {{ $lossCase->store->name ?? 'Stor Utama' }}</div>
            <div>Tarikh Kejadian Dikesan: {{ $lossCase->discovery_date }}</div>
            <div>Nilai Keseluruhan Dihapus Kira: <span class="font-mono text-rose-800 font-bold text-sm">RM {{ number_format($lossCase->total_loss_value ?? 0, 2) }}</span></div>
            <div>Status Tindakan Surcaj: <span class="uppercase">{{ $lossCase->surcharge_recommended ? 'Syor Surcaj Dikenakan' : 'Tiada Tindakan Surcaj' }}</span></div>
        </div>

        <p class="text-justify">
            Sehubungan itu, tindakan pelarasan keluar baki stok daripada <strong>Daftar Stok (KEW.PS-3 Bahagian B)</strong> dan <strong>Kad Petak (KEW.PS-4)</strong> telah disempurnakan selaras dengan <strong>Tatacara Pengurusan Stor Kerajaan (TPS AM 6.10)</strong>.
        </p>

        <div class="pt-8 flex justify-between items-end">
            <div>
                <div>Tarikh Dikeluarkan:</div>
                <div class="font-bold font-mono">{{ $lossCase->authority_approval_date ? \Carbon\Carbon::parse($lossCase->authority_approval_date)->format('d F Y') : date('d F Y') }}</div>
            </div>

            <div class="text-right">
                <div class="h-16"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan / Pegawai Pengawal</div>
                <div class="font-bold text-slate-800">{{ $lossCase->approver->name ?? 'Pegawai Pengawal ASM' }}</div>
                <div class="text-slate-500">Akademi Sains Malaysia</div>
            </div>
        </div>
    </div>
</div>
@endsection
