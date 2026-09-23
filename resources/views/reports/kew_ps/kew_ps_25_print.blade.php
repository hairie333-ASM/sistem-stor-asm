@extends('layouts.print')

@section('title', 'Kenyataan Sebut Harga Pelupusan (KEW.PS-25)')
@section('form_code', 'KEW.PS-25')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">KENYATAAN SEBUT HARGA PELUPUSAN STOK (KEW.PS-25)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Pelupusan Secara Sebut Harga)</p>
    </div>

    <div class="text-xs space-y-3 leading-relaxed">
        <p>Tawaran Sebut Harga adalah dipelawa untuk pelupusan stok kerajaan berikut:</p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Kementerian/Jabatan: Akademi Sains Malaysia (ASM)</div>
            <div>No. Sebut Harga: ASM/SH/PELUPUSAN/{{ date('Y') }}/01</div>
            <div>Tarikh Lawatan Tapak: {{ date('d F Y', strtotime('+3 days')) }}</div>
            <div>Tarikh Tutup Sebut Harga: {{ date('d F Y', strtotime('+10 days')) }}</div>
        </div>

        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2">Keterangan Stok</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Kuantiti</th>
                    <th class="border border-slate-300 p-2 text-right w-28">Harga Rizab (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-300 p-2 text-center">1</td>
                    <td class="border border-slate-300 p-2 font-medium">Bahan Kertas Lusuh / Karton Kotak Terpakai</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">1 Lot</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">300.00</td>
                </tr>
            </tbody>
        </table>

        <div class="pt-6">
            <div class="font-bold">Urus Setia Sebut Harga Pelupusan</div>
            <div>Akademi Sains Malaysia</div>
            <div>Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
