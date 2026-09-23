@extends('layouts.print')

@section('title', 'Tukar Barang / Perkhidmatan (KEW.PS-28)')
@section('form_code', 'KEW.PS-28')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG PELUPUSAN SECARA TUKAR BARANG / PERKHIDMATAN (KEW.PS-28)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Pelupusan Tukar Barang)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1">
            <div><span class="font-bold">Kementerian/Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Pihak/Agensi Yang Bersetuju Menukar:</span> ............................................</div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="border border-slate-300 p-3 rounded space-y-2">
                <div class="font-bold border-b pb-1 text-slate-800">Stok Kerajaan Yang Diberi:</div>
                <div>Perihal: Lebihan Kertas & Fail Arkib</div>
                <div>Kuantiti: 100 Rim</div>
                <div>Nilai Anggaran: RM 1,200.00</div>
            </div>
            <div class="border border-slate-300 p-3 rounded space-y-2">
                <div class="font-bold border-b pb-1 text-slate-800">Barang/Perkhidmatan Diterima:</div>
                <div>Perihal: Perkhidmatan Servis Mesin Pencetak & Toner</div>
                <div>Nilai Anggaran Perkhidmatan: RM 1,200.00</div>
            </div>
        </div>

        <div class="pt-8 grid grid-cols-2 gap-8">
            <div>
                <div>Tandatangan Pegawai Stor:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Stor</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div class="text-right">
                <div>Kelulusan Kuasa Melulus:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
