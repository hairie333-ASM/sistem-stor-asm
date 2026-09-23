@extends('layouts.print')

@section('title', 'Pelupusan Secara Tukar Beli (KEW.PS-27)')
@section('form_code', 'KEW.PS-27')

@section('content')
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">BORANG PELUPUSAN SECARA TUKAR BELI (TRADE-IN) (KEW.PS-27)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - Pelupusan Tukar Beli)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1">
            <div><span class="font-bold">Kementerian/Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Nama Pembekal Baharu:</span> ....................................................</div>
            <div><span class="font-bold">No. Pesanan Kerajaan Baharu:</span> ............................................</div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="border border-slate-300 p-3 rounded space-y-2">
                <div class="font-bold border-b pb-1 text-slate-800">A. Stok Lama Dilupuskan (Trade-In)</div>
                <div>Perihal: Peralatan Komputer / Makmal</div>
                <div>Kuantiti: 5 Unit</div>
                <div>Nilai Diskaun Trade-In: <span class="font-bold font-mono">RM 2,500.00</span></div>
            </div>
            <div class="border border-slate-300 p-3 rounded space-y-2">
                <div class="font-bold border-b pb-1 text-slate-800">B. Stok Baharu Diperoleh</div>
                <div>Perihal: Peralatan Komputer Model Terkini</div>
                <div>Harga Asal Baharu: RM 15,000.00</div>
                <div>Harga Bersih Perlu Dibayar: <span class="font-bold font-mono text-emerald-800">RM 12,500.00</span></div>
            </div>
        </div>

        <div class="pt-8 grid grid-cols-2 gap-8">
            <div>
                <div>Diperakukan Oleh:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Stor</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div class="text-right">
                <div>Disahkan Oleh Kuasa Melulus:</div>
                <div class="h-14"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
