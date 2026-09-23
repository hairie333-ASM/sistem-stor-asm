@extends('layouts.print')

@section('title', 'Laporan Kedudukan Stok (KEW.PS-14)')
@section('form_code', 'KEW.PS-14')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN KEDUDUKAN STOK TAHUNAN & KADAR PUSINGAN (KEW.PS-14)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.6 - Tahun Kewangan: <span class="font-bold font-mono">{{ $year }}</span>)</p>
    </div>

    <div class="text-xs">
        <span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)
    </div>

    <!-- Table of 4 Quarters -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-12">Suku</th>
                <th class="border border-slate-300 p-2">Tempoh Suku Tahun</th>
                <th class="border border-slate-300 p-2 text-right w-36">Baki Awal Stok (RM) (a)</th>
                <th class="border border-slate-300 p-2 text-right w-36">Nilai Terimaan (RM) (b)</th>
                <th class="border border-slate-300 p-2 text-right w-36">Nilai Pengeluaran (RM) (c)</th>
                <th class="border border-slate-300 p-2 text-right w-36">Baki Akhir Stok (RM) (a+b-c)</th>
            </tr>
        </thead>
        <tbody class="font-mono">
            @foreach($turnoverData['quarters'] as $q)
            <tr>
                <td class="border border-slate-300 p-2 text-center font-sans font-bold">{{ $q['quarter'] }}</td>
                <td class="border border-slate-300 p-2 font-sans font-medium">{{ $q['label'] }}</td>
                <td class="border border-slate-300 p-2 text-right">{{ number_format($q['opening_value'], 2) }}</td>
                <td class="border border-slate-300 p-2 text-right text-blue-900 font-semibold">{{ number_format($q['receipt_value'], 2) }}</td>
                <td class="border border-slate-300 p-2 text-right text-purple-900 font-semibold">{{ number_format($q['issue_value'], 2) }}</td>
                <td class="border border-slate-300 p-2 text-right font-bold text-slate-900">{{ number_format($q['closing_value'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-400 font-mono">
            <tr>
                <td colspan="2" class="border border-slate-300 p-2 uppercase font-sans text-right">Jumlah Tahunan:</td>
                <td class="border border-slate-300 p-2 text-right font-sans text-slate-400">-</td>
                <td class="border border-slate-300 p-2 text-right text-blue-900">RM {{ number_format($turnoverData['annual_receipt_total'], 2) }}</td>
                <td class="border border-slate-300 p-2 text-right text-purple-900">RM {{ number_format($turnoverData['annual_issue_total'], 2) }}</td>
                <td class="border border-slate-300 p-2 text-right text-slate-900">RM {{ number_format($turnoverData['current_year_closing'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Formula Box -->
    <div class="border border-slate-400 p-3 rounded text-xs space-y-2 bg-slate-50">
        <div class="font-bold uppercase text-[11px] text-blue-950">Pengiraan Kadar Pusingan Stok Tahunan (KPS):</div>
        <div class="font-mono text-[11px] leading-relaxed">
            <div>KPS = Jumlah Nilai Pengeluaran Stok Tahunan / [ (Baki Akhir Tahun {{ $turnoverData['prev_year'] }} + Baki Akhir Tahun {{ $turnoverData['year'] }}) / 2 ]</div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;= RM {{ number_format($turnoverData['annual_issue_total'], 2) }} / [ (RM {{ number_format($turnoverData['prev_year_closing'], 2) }} + RM {{ number_format($turnoverData['current_year_closing'], 2) }}) / 2 ]</div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;= RM {{ number_format($turnoverData['annual_issue_total'], 2) }} / RM {{ number_format($turnoverData['average_stock_value'], 2) }}</div>
            <div class="font-bold text-sm text-slate-900 pt-1">&nbsp;&nbsp;&nbsp;&nbsp;= {{ $turnoverData['turnover_rate'] }} kali</div>
            <div class="font-semibold text-slate-600">&nbsp;&nbsp;&nbsp;&nbsp;Piawaian Sasaran Pekeliling Perbendaharaan: &ge; 4.0 kali setahun.</div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 gap-8 pt-6 text-xs">
        <div>
            <div>Disediakan Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Pengurusan Stor</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
        <div class="text-right">
            <div>Disahkan Oleh:</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan / Pegawai Pengawal</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
