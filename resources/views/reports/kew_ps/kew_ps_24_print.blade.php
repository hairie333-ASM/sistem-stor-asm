@extends('layouts.print')

@section('title', 'Kenyataan Tawaran Tender Pelupusan (KEW.PS-24)')
@section('form_code', 'KEW.PS-24')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">KENYATAAN TAWARAN TENDER PELUPUSAN STOK (KEW.PS-24)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Pelupusan Secara Tender)</p>
    </div>

    <div class="text-xs space-y-3 leading-relaxed">
        <p>1. Tender adalah dipelawa kepada syarikat atau orang perseorangan yang berminat untuk membeli stok kerajaan seperti berikut:</p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Kementerian/Jabatan: Akademi Sains Malaysia (ASM)</div>
            <div>No. Tender: ASM/TENDER/PELUPUSAN/{{ date('Y') }}/01</div>
            <div>Tempat Stok Disimpan: Stor Utama Akademi Sains Malaysia</div>
            <div>Tarikh Tutup Tawaran: {{ date('d F Y', strtotime('+14 days')) }}</div>
        </div>

        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2">Keterangan / Perihal Stok</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Kuantiti</th>
                    <th class="border border-slate-300 p-2 text-right w-28">Harga Rizab (RM)</th>
                    <th class="border border-slate-300 p-2 text-right w-28">Deposit Tender (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-300 p-2 text-center">1</td>
                    <td class="border border-slate-300 p-2 font-medium">Stok Peralatan Makmal & Komputer Lusuh Terpakai</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">1 Lot</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">1,500.00</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">150.00</td>
                </tr>
            </tbody>
        </table>

        <div class="pt-4 space-y-2">
            <div class="font-bold">Syarat-Syarat Tender:</div>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Stok dijual dalam keadaan "sebagaimana adanya" (as is where is).</li>
                <li>Petender yang berjaya hendaklah menjelaskan bayaran penuh dalam tempoh tujuh (7) hari bekerja.</li>
                <li>Pengambilan stok hendaklah dibuat atas perbelanjaan petender sendiri dalam tempoh 14 hari.</li>
            </ul>
        </div>

        <div class="pt-8">
            <div class="font-bold">Urus Setia Tender Pelupusan</div>
            <div>Akademi Sains Malaysia</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
