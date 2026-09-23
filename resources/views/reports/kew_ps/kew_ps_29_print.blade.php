@extends('layouts.print')

@section('title', 'Akuan Penerimaan Hadiah / Sumbangan (KEW.PS-29)')
@section('form_code', 'KEW.PS-29')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SIJIL AKUAN PENERIMAAN HADIAH / SUMBANGAN STOK (KEW.PS-29)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Pelupusan Secara Hadiah)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <p class="text-justify">
            Adalah dengan ini disahkan bahawa stok kerajaan seperti tersenarai di bawah telah diserahkan sebagai hadiah / sumbangan secara rasmi daripada:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Pemberi: Akademi Sains Malaysia (ASM)</div>
            <div>Penerima Hadiah / Pertubuhan: ................................................................</div>
            <div>No. Surat Kelulusan Perbendaharaan / Kuasa Melulus: .....................................</div>
        </div>

        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2">Perihal Stok</th>
                    <th class="border border-slate-300 p-2 text-center w-24">Kuantiti</th>
                    <th class="border border-slate-300 p-2 text-right w-32">Nilai Semasa (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-300 p-2 text-center">1</td>
                    <td class="border border-slate-300 p-2">Buku Rujukan / Terbitan Saintifik ASM</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">200 Naskhah</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">2,000.00</td>
                </tr>
            </tbody>
        </table>

        <div class="pt-8 grid grid-cols-2 gap-8">
            <div>
                <div>Diserahkan Oleh:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Wakil ASM</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div class="text-right">
                <div>Diterima Oleh Penerima Hadiah:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Nama Penerima & Cop Rasmi</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
