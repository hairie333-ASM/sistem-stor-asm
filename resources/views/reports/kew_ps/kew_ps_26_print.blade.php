@extends('layouts.print')

@section('title', 'Jualan Sisa / Barangan Lusuh (KEW.PS-26)')
@section('form_code', 'KEW.PS-26')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG SEBUT HARGA JUALAN SISA / BARANGAN LUSUH (KEW.PS-26)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Kaedah Jualan Sisa)</p>
    </div>

    <div class="text-xs space-y-3 leading-relaxed">
        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1">
            <div><span class="font-bold">Kementerian/Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Tarikh:</span> {{ date('d F Y') }}</div>
        </div>

        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2">Jenis Barangan Sisa</th>
                    <th class="border border-slate-300 p-2 text-center w-24">Anggaran Berat / Kuantiti</th>
                    <th class="border border-slate-300 p-2 text-right w-32">Kadar Tawaran / Unit (RM)</th>
                    <th class="border border-slate-300 p-2 text-right w-32">Jumlah Tawaran (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-300 p-2 text-center">1</td>
                    <td class="border border-slate-300 p-2">Besi Buruk / Rak Rosak</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">150 KG</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">1.20 / KG</td>
                    <td class="border border-slate-300 p-2 text-right font-mono font-bold">180.00</td>
                </tr>
            </tbody>
        </table>

        <div class="pt-6">
            <div>Tandatangan Pembeli Sisa: .................................................</div>
            <div>Nama Pembeli / Syarikat Berdaftar: .................................................</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
