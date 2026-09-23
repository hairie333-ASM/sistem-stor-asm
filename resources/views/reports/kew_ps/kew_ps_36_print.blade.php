@extends('layouts.print')

@section('title', 'Laporan Tahunan Kehilangan & Hapus Kira (KEW.PS-36)')
@section('form_code', 'KEW.PS-36')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN TAHUNAN KEHILANGAN DAN HAPUS KIRA STOK (KEW.PS-36)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.10 - Tahun: <span class="font-bold font-mono">{{ date('Y') }}</span>)</p>
    </div>

    <div class="text-xs">
        <span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)
    </div>

    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-8">Bil</th>
                <th class="border border-slate-300 p-2 w-32">Kementerian/Jabatan</th>
                <th class="border border-slate-300 p-2 text-center w-28">Bil. Kes Kehilangan</th>
                <th class="border border-slate-300 p-2 text-right w-36">Nilai Kehilangan (RM)</th>
                <th class="border border-slate-300 p-2 text-right w-36">Nilai Hapus Kira (RM)</th>
                <th class="border border-slate-300 p-2 text-center w-32">Tindakan Surcaj</th>
                <th class="border border-slate-300 p-2">Catatan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-slate-300 p-2 text-center">1</td>
                <td class="border border-slate-300 p-2 font-bold">Akademi Sains Malaysia (ASM)</td>
                <td class="border border-slate-300 p-2 text-center font-bold font-mono">1 Kes</td>
                <td class="border border-slate-300 p-2 text-right font-mono">{{ number_format($lossCase->total_loss_value ?? 0, 2) }}</td>
                <td class="border border-slate-300 p-2 text-right font-mono font-bold text-rose-800">{{ number_format($lossCase->total_loss_value ?? 0, 2) }}</td>
                <td class="border border-slate-300 p-2 text-center font-semibold text-[11px]">{{ $lossCase->surcharge_recommended ? 'Ada Surcaj' : 'Tiada' }}</td>
                <td class="border border-slate-300 p-2 text-slate-600 text-[11px]">Sijil Hapus Kira KEW.PS-35 dikeluarkan.</td>
            </tr>
        </tbody>
        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-400 font-mono">
            <tr>
                <td colspan="3" class="border border-slate-300 p-2 uppercase font-sans text-right">Jumlah:</td>
                <td class="border border-slate-300 p-2 text-right">RM {{ number_format($lossCase->total_loss_value ?? 0, 2) }}</td>
                <td class="border border-slate-300 p-2 text-right text-rose-800">RM {{ number_format($lossCase->total_loss_value ?? 0, 2) }}</td>
                <td colspan="2" class="border border-slate-300 p-2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="pt-8 text-xs">
        <div>Disediakan Oleh:</div>
        <div class="h-16"></div>
        <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Ketua Jabatan / Pegawai Pengawal</div>
        <div>Tarikh: {{ date('d/m/Y') }}</div>
    </div>
</div>
@endsection
