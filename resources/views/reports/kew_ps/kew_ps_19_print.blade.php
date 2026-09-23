@extends('layouts.print')

@section('title', 'Pelantikan Lembaga Pemeriksa Pelupusan (KEW.PS-19)')
@section('form_code', 'KEW.PS-19')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SURAT PELANTIKAN LEMBAGA PEMERIKSA PELUPUSAN STOK (KEW.PS-19)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - No. Rujukan: <span class="font-mono font-bold">{{ $disposal->committee_appointment_ref ?? 'ASM/PELUPUSAN/LANTIKAN/2026/001' }}</span>)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="flex justify-between">
            <div>
                <div>Kepada Ahli Lembaga Pemeriksa:</div>
                @if(isset($disposal->committees))
                    @foreach($disposal->committees as $c)
                        <div class="font-bold text-slate-800">{{ $c->role }}: {{ $c->officer_name }} ({{ $c->position }})</div>
                    @endforeach
                @endif
                <div class="text-slate-500">Akademi Sains Malaysia</div>
            </div>
            <div class="text-right">
                <div>Tarikh: {{ date('d F Y') }}</div>
            </div>
        </div>

        <div class="pt-2">
            <div class="font-bold uppercase text-[11px]">TUAN/PUAN,</div>
            <div class="font-bold uppercase text-[12px] text-blue-950 mt-1">
                PELANTIKAN SEBAGAI LEMBAGA PEMERIKSA PELUPUSAN STOK KERAJAAN
            </div>
        </div>

        <p>
            Dengan hormatnya dimaklumkan bahawa menurut Tatacara Pengurusan Stor Kerajaan (TPS AM 6.9), tuan/puan dengan ini dilantik sebagai <strong>Lembaga Pemeriksa Pelupusan Stok</strong> bagi menjalankan pemeriksaan ke atas stok yang dicadangkan untuk dilupuskan di:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded font-bold">
            Stor: {{ $disposal->store->name ?? 'Stor Utama ASM' }}
        </div>

        <p>
            2. Lembaga Pemeriksa hendaklah memeriksa keadaan fizikal stok secara terperinci, menilai kewajaran pelupusan, dan memperakukan kaedah pelupusan yang paling ekonomik dan selamat di dalam <strong>Laporan Lembaga Pemeriksa Pelupusan Stok (KEW.PS-20)</strong>.
        </p>

        <p>
            Sekian, terima kasih.
        </p>

        <div class="pt-8">
            <div class="font-bold">"BERKHIDMAT UNTUK NEGARA"</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Ketua Jabatan / Pegawai Pengawal</div>
            <div>Akademi Sains Malaysia</div>
        </div>
    </div>
</div>
@endsection
