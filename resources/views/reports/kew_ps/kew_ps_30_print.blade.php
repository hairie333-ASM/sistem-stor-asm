@extends('layouts.print')

@section('title', 'Sijil Akuan Pemusnahan Stok (KEW.PS-30)')
@section('form_code', 'KEW.PS-30')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SIJIL AKUAN PEMUSNAHAN / PENENGGELAMAN STOK (KEW.PS-30)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Kaedah Musnah / Tanam / Bakar)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <p class="text-justify">
            Kami dengan ini mengesahkan bahawa stok kerajaan di bawah telah dimusnahkan secara fizikal selaras dengan kelulusan yang diberikan di bawah TPS AM 6.9:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Kementerian/Jabatan: Akademi Sains Malaysia (ASM)</div>
            <div>Tarikh Pemusnahan: {{ date('d F Y') }}</div>
            <div>Tempat Pemusnahan: Tapak Pelupusan / Premis Berdaftar</div>
            <div>Cara Pemusnahan: Ditimbus / Dibakar / Dihancurkan Mesin Rincih</div>
        </div>

        <table class="w-full text-left border-collapse border border-slate-400 text-xs">
            <thead>
                <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                    <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                    <th class="border border-slate-300 p-2">Perihal Stok</th>
                    <th class="border border-slate-300 p-2 text-center w-24">Kuantiti</th>
                    <th class="border border-slate-300 p-2 text-right w-32">Nilai Asal (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-slate-300 p-2 text-center">1</td>
                    <td class="border border-slate-300 p-2 font-medium">Bahan Kimia Terpakai / Reagen Makmal Tamat Tempoh</td>
                    <td class="border border-slate-300 p-2 text-center font-bold">10 Botol</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">1,800.00</td>
                </tr>
            </tbody>
        </table>

        <div class="pt-8 grid grid-cols-2 gap-8">
            <div>
                <div>Pegawai Pelaksana Pemusnahan:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Pelaksana</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div class="text-right">
                <div>Pegawai Saksi Bebas:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Saksi Bebas</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
