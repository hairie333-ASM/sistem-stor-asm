@extends('layouts.print')

@section('title', 'Pelantikan Jawatankuasa Penyiasat (KEW.PS-33)')
@section('form_code', 'KEW.PS-33')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">PELANTIKAN JAWATANKUASA PENYIASAT KEHILANGAN STOK (KEW.PS-33)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.10 - No. Rujukan: <span class="font-mono font-bold">{{ $lossCase->investigation_committee_ref ?? 'ASM/KEHILANGAN/LANTIKAN/2026/001' }}</span>)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="flex justify-between">
            <div>
                <div>Kepada:</div>
                @if(isset($lossCase->committees))
                    @foreach($lossCase->committees as $c)
                        <div class="font-bold text-slate-800">{{ $c->role }}: {{ $c->officer_name }} ({{ $c->position }})</div>
                    @endforeach
                @endif
                <div class="text-slate-500">Akademi Sains Malaysia</div>
            </div>
            <div class="text-right">
                <div>Tarikh Surat: {{ date('d F Y') }}</div>
            </div>
        </div>

        <div class="pt-2">
            <div class="font-bold uppercase text-[11px]">TUAN/PUAN,</div>
            <div class="font-bold uppercase text-[12px] text-rose-950 mt-1">
                PELANTIKAN SEBAGAI JAWATANKUASA PENYIASAT KEHILANGAN STOK KERAJAAN
            </div>
        </div>

        <p>
            Dengan hormatnya saya merujuk kepada Laporan Awal Kehilangan Stok (KEW.PS-32) No. Rujukan <strong>{{ $lossCase->case_number }}</strong> bertarikh <strong>{{ $lossCase->created_at ? $lossCase->created_at->format('d/m/Y') : date('d/m/Y') }}</strong>.
        </p>

        <p>
            2. Tuan/puan dengan ini dilantik sebagai <strong>Jawatankuasa Penyiasat</strong> untuk menjalankan siasatan terperinci ke atas kehilangan stok di:
        </p>

        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1 font-semibold">
            <div>Stor Terlibat: {{ $lossCase->store->name ?? 'Stor Utama ASM' }}</div>
            <div>Nilai Kehilangan: RM {{ number_format($lossCase->total_loss_value ?? 0, 2) }}</div>
        </div>

        <p>
            3. Tanggungjawab Jawatankuasa Penyiasat adalah:
        </p>
        <ul class="list-disc list-inside space-y-1 pl-4">
            <li>Menyiasat cara kehilangan berlaku dan pihak yang bertanggungjawab secara langsung atau tidak langsung.</li>
            <li>Menilai sama ada kehilangan berpunca daripada kecuaian pegawai, kegagalan mematuhi peraturan atau kelemahan sistem kawalan dalaman.</li>
            <li>Mengesyorkan tindakan tatatertib atau surcaj sekiranya didapati berlaku kecuaian.</li>
            <li>Menyediakan dan mengemukakan <strong>Laporan Akhir Kehilangan Stok (KEW.PS-34)</strong> dalam tempoh dua (2) bulan dari tarikh surat ini.</li>
        </ul>

        <p>
            Sekian, terima kasih.
        </p>

        <div class="pt-8">
            <div class="font-bold">"BERKHIDMAT UNTUK NEGARA"</div>
            <div class="h-16"></div>
            <div class="font-bold border-t border-slate-400 inline-block pt-1 pr-12">Pegawai Pengawal / Ketua Jabatan</div>
            <div>Akademi Sains Malaysia</div>
        </div>
    </div>
</div>
@endsection
