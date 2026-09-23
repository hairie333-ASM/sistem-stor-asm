@extends('layouts.print')

@section('title', 'Senarai Stok Bertarikh Luput (KEW.PS-6)')
@section('form_code', 'KEW.PS-6')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">SENARAI STOK BERTARIKH LUPUT</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.3 - Pemantauan Stok Yang Mempunyai Tarikh Luput Bagi Tahun {{ date('Y') }})</p>
    </div>

    <div class="flex justify-between items-center text-xs">
        <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
        <div><span class="font-bold">Tarikh Pemantauan:</span> {{ date('d/m/Y') }}</div>
    </div>

    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-10">Bil</th>
                <th class="border border-slate-300 p-2 w-28">No. Kod Stok</th>
                <th class="border border-slate-300 p-2">Perihal Stok</th>
                <th class="border border-slate-300 p-2 text-center w-24">No. Kelompok (Batch)</th>
                <th class="border border-slate-300 p-2 text-center w-20">Kuantiti</th>
                <th class="border border-slate-300 p-2 text-center w-24">Tarikh Luput</th>
                <th class="border border-slate-300 p-2 text-center w-24">Baki Tempoh</th>
                <th class="border border-slate-300 p-2 text-center w-24">Status Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $batches = \App\Models\StockBatch::with('stockItem')
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc')
                    ->take(50)
                    ->get();
            @endphp
            @forelse($batches as $idx => $b)
            @php 
                $days = $b->expiry_date ? now()->diffInDays(\Carbon\Carbon::parse($b->expiry_date), false) : null;
            @endphp
            <tr>
                <td class="border border-slate-300 p-1.5 text-center">{{ $idx + 1 }}</td>
                <td class="border border-slate-300 p-1.5 font-mono font-bold">{{ $b->stockItem->stock_code ?? '-' }}</td>
                <td class="border border-slate-300 p-1.5">{{ $b->stockItem->description ?? '-' }}</td>
                <td class="border border-slate-300 p-1.5 text-center font-mono">{{ $b->batch_number ?? '-' }}</td>
                <td class="border border-slate-300 p-1.5 text-center">{{ number_format($b->remaining_quantity) }}</td>
                <td class="border border-slate-300 p-1.5 text-center font-mono">{{ $b->expiry_date ? \Carbon\Carbon::parse($b->expiry_date)->format('d/m/Y') : 'Tiada' }}</td>
                <td class="border border-slate-300 p-1.5 text-center font-bold {{ $days < 0 ? 'text-red-700' : ($days <= 90 ? 'text-amber-700' : 'text-emerald-700') }}">
                    {{ $days !== null ? ($days < 0 ? 'Tamat Tempoh' : $days . ' hari') : '-' }}
                </td>
                <td class="border border-slate-300 p-1.5 text-center text-[10px]">
                    {{ $days < 0 ? 'Asingkan (Pelupusan)' : ($days <= 90 ? 'Keluarkan Segera (MDKD)' : 'Simpan Teratur') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="border border-slate-300 p-4 text-center text-slate-500">
                    Semua stok bertarikh luput dipantau dalam keadaan selamat dan teratur.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="grid grid-cols-2 gap-8 text-xs pt-8">
        <div class="border-t border-slate-400 pt-2 space-y-1">
            <div>Disediakan Oleh (Pegawai Stor):</div>
            <div class="font-bold">Ahmad Zulkifli (Pegawai Stor Kanan)</div>
            <div class="text-[10px] text-slate-500">Tarikh: {{ date('d/m/Y') }}</div>
        </div>
        <div class="border-t border-slate-400 pt-2 space-y-1">
            <div>Disahkan Oleh:</div>
            <div class="font-bold">Dr. Hazami Habib (Ketua Jabatan)</div>
            <div class="text-[10px] text-slate-500">Tarikh: {{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>
@endsection
