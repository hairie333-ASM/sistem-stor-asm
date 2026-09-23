@extends('layouts.print')

@section('title', 'Akuan Penerimaan Buangan Terjadual (KEW.PS-31)')
@section('form_code', 'KEW.PS-31')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">AKUAN PENERIMAAN BUANGAN TERJADUAL (E-WASTE) (KEW.PS-31)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Pelupusan E-Waste / Jabatan Alam Sekitar)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Kementerian/Jabatan: Akademi Sains Malaysia (ASM)</div>
            <div>Kontraktor Berlesen JAS: ..............................................................</div>
            <div>No. Lesen Jabatan Alam Sekitar (JAS): ............................................</div>
            <div>Tarikh Penyerahan: {{ date('d F Y') }}</div>
        </div>

        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2">Keterangan Komponen Elektronik / Sisa</th>
                    <th class="border border-slate-300 p-2 text-center w-24">Kod Buangan JAS</th>
                    <th class="border border-slate-300 p-2 text-center w-24">Kuantiti</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-300 p-2 text-center">1</td>
                    <td class="border border-slate-300 p-2">Papan Litar Komputer, Bateri & Monitor Lusuh</td>
                    <td class="border border-slate-300 p-2 text-center font-mono">SW 110</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">1 Lot</td>
                </tr>
            </tbody>
        </table>

        <div class="pt-8 grid grid-cols-2 gap-8">
            <div>
                <div>Diserahkan Oleh:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Stor ASM</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div class="text-right">
                <div>Diterima Oleh Kontraktor Berlesen:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Wakil Kontraktor & Cop Syarikat</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
