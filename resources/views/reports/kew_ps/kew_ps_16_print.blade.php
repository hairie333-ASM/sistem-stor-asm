@extends('layouts.print')

@section('title', 'Perakuan Kelulusan Pelarasan Stok (KEW.PS-16)')
@section('form_code', 'KEW.PS-16')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">PERAKUAN KELULUSAN PELARASAN STOK (KEW.PS-16)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.6 - No. Perakuan: <span class="font-mono font-bold">{{ $adjustment->perakuan_number ?? 'KEW.PS-16/ASM/2026/0001' }}</span>)</p>
    </div>

    <div class="text-xs space-y-4 leading-relaxed">
        <div class="p-3 bg-slate-50 border border-slate-300 rounded space-y-1">
            <div><span class="font-bold">No. Rujukan Laporan (KEW.PS-15):</span> {{ $adjustment->adjustment_number }}</div>
            <div><span class="font-bold">Stor Terlibat:</span> {{ $adjustment->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Sebab Pelarasan:</span> {{ $adjustment->reason }}</div>
            <div><span class="font-bold">Tarikh Diluluskan:</span> {{ $adjustment->approved_at ? \Carbon\Carbon::parse($adjustment->approved_at)->format('d F Y') : date('d F Y') }}</div>
        </div>

        <p class="text-justify">
            Setelah meneliti Laporan Pelarasan Stok (KEW.PS-15) yang dikemukakan serta berpuas hati dengan penjelasan dan justifikasi punca selisih stok fizikal berbanding rekod buku, saya dengan ini:
        </p>

        <div class="p-4 bg-emerald-50 border border-emerald-300 rounded text-center font-bold text-emerald-950 uppercase text-xs">
            MELULUSKAN PELARASAN STOK SEBANYAK {{ $adjustment->items->count() }} ITEM SEPERTI YANG DISENARAIKAN DALAM BORANG KEW.PS-15
        </div>

        <div class="space-y-1 pt-2">
            <span class="font-bold">Ulasan / Arahan Ketua Jabatan:</span>
            <p class="p-3 border border-slate-200 rounded italic text-slate-700 bg-slate-50">
                "{{ $adjustment->approval_remarks ?: 'Diluluskan mengikut peraturan kewangan perbendaharaan AM 6.6. Sila kemaskini baki Daftar Stok KEW.PS-3 dan Kad Petak KEW.PS-4 serta-merta.' }}"
            </p>
        </div>

        <p class="text-justify text-[11px] text-slate-600">
            * Nota: Kelulusan ini membolehkan Pegawai Stor menyelaraskan angka baki fizikal dan baki sistem tanpa sebarang pindaan manual tidak sah.
        </p>

        <div class="pt-10 flex justify-between items-end">
            <div>
                <div>Tarikh Kelulusan:</div>
                <div class="font-bold font-mono">{{ date('d/m/Y') }}</div>
            </div>

            <div class="text-right">
                <div class="h-16"></div>
                <div class="font-bold border-t border-slate-400 inline-block pt-1 pl-12">Ketua Jabatan / Pegawai Pengawal</div>
                <div class="font-bold text-slate-800">{{ $adjustment->approver->name ?? 'Ketua Jabatan ASM' }}</div>
                <div class="text-slate-500">Akademi Sains Malaysia</div>
            </div>
        </div>
    </div>
</div>
@endsection
