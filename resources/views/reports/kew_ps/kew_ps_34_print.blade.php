@extends('layouts.print')

@section('title', 'Laporan Akhir Kehilangan Stok (KEW.PS-34)')
@section('form_code', 'KEW.PS-34')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN AKHIR KEHILANGAN STOK KERAJAAN (KEW.PS-34)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.10 - No. Rujukan Laporan: <span class="font-mono font-bold">{{ $lossCase->final_report_ref ?? 'KEW.PS-34/ASM/2026/001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div>
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Terlibat:</span> {{ $lossCase->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">No. Rujukan Laporan Awal (KEW.PS-32):</span> {{ $lossCase->case_number }}</div>
        </div>
        <div class="text-right">
            <div><span class="font-bold">Rujukan Lantikan (KEW.PS-33):</span> {{ $lossCase->investigation_committee_ref }}</div>
            <div><span class="font-bold">Tarikh Laporan Akhir:</span> {{ date('d F Y') }}</div>
            <div><span class="font-bold">Jumlah Nilai Kehilangan:</span> <span class="font-mono font-bold text-rose-800">RM {{ number_format($lossCase->total_loss_value ?? 0, 2) }}</span></div>
        </div>
    </div>

    <!-- Findings & Recommendations -->
    <div class="space-y-3 text-xs">
        <div class="border border-slate-300 p-3 rounded space-y-1">
            <div class="font-bold uppercase text-[10px] text-slate-800">1. Dapatan Siasatan Jawatankuasa:</div>
            <p class="text-slate-700 leading-relaxed italic">
                "{{ $lossCase->committees->first()->findings ?? 'Siasatan terperinci mendapati kehilangan berlaku di luar waktu pejabat disebabkan oleh kelemahan sistem kawalan kunci dan tiada kamera litar tertutup (CCTV) di laluan keluar belakang.' }}"
            </p>
        </div>

        <div class="border border-slate-300 p-3 rounded space-y-1">
            <div class="font-bold uppercase text-[10px] text-slate-800">2. Syor Hapus Kira & Langkah Pencegahan:</div>
            <p class="text-slate-700 leading-relaxed italic">
                "{{ $lossCase->committees->first()->recommendations ?? 'Disyorkan nilai kehilangan stok berjumlah RM ' . number_format($lossCase->total_loss_value, 2) . ' dihapus kira daripada rekod perakaunan stor, dan jeriji besi keselamatan dipasang serta-merta.' }}"
            </p>
        </div>

        <div class="border border-slate-300 p-3 rounded space-y-1 bg-slate-50">
            <div class="font-bold uppercase text-[10px] text-slate-800">3. Syor Tindakan Surcaj / Tatatertib:</div>
            <div class="font-semibold text-slate-900">
                @if($lossCase->surcharge_recommended)
                    <span class="text-rose-800">TINDAKAN SURCAJ DISYORKAN:</span> {{ $lossCase->surcharge_details ?: 'Kecuaian dikesan ke atas pegawai bertanggungjawab.' }}
                @else
                    <span class="text-emerald-800">TIADA SURCAJ DISYORKAN:</span> Kehilangan bukan berpunca daripada kecuaian peribadi melainkan faktor pencerobohan luar jangka.
                @endif
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="pt-8 border-t border-slate-300">
        <div class="font-bold uppercase text-[11px] mb-2">Tandatangan Jawatankuasa Penyiasat:</div>
        <div class="grid grid-cols-2 gap-8 text-xs">
            <div>
                <div>Pengerusi:</div>
                <div class="h-14"></div>
                <div class="font-bold">Nama: .................................................</div>
                <div>Jawatan: ................................................</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div>
                <div>Ahli:</div>
                <div class="h-14"></div>
                <div class="font-bold">Nama: .................................................</div>
                <div>Jawatan: ................................................</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
