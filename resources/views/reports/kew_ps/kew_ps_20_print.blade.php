@extends('layouts.print')

@section('title', 'Laporan Lembaga Pemeriksa Pelupusan (KEW.PS-20)')
@section('form_code', 'KEW.PS-20')

@section('content')
<div class="space-y-4">
    <div class="text-center pb-2 border-b border-slate-300">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">LAPORAN LEMBAGA PEMERIKSA PELUPUSAN STOK (KEW.PS-20)</h2>
        <p class="text-[11px] text-slate-600">(Tatacara Pengurusan Stor AM 6.9 - No. Rujukan: <span class="font-mono font-bold">{{ $disposal->disposal_number ?? 'PS20/ASM/2026/0001' }}</span>)</p>
    </div>

    <!-- Metadata Grid -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div>
            <div><span class="font-bold">Kementerian / Jabatan:</span> Akademi Sains Malaysia (ASM)</div>
            <div><span class="font-bold">Stor Terlibat:</span> {{ $disposal->store->name ?? 'Stor Utama' }}</div>
            <div><span class="font-bold">Kaedah Disyorkan:</span> {{ $disposal->disposal_method }}</div>
        </div>
        <div class="text-right">
            <div><span class="font-bold">Rujukan Lantikan (KEW.PS-19):</span> {{ $disposal->committee_appointment_ref ?? '-' }}</div>
            <div><span class="font-bold">Tarikh Pemeriksaan:</span> {{ $disposal->created_at ? $disposal->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><span class="font-bold">Jumlah Nilai Asal:</span> <span class="font-mono font-bold">RM {{ number_format($disposal->total_original_value ?? 0, 2) }}</span></div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left border-collapse border border-slate-400 text-xs">
        <thead>
            <tr class="bg-slate-100 font-bold border-b border-slate-400 uppercase text-[10px]">
                <th class="border border-slate-300 p-2 text-center w-8">Bil</th>
                <th class="border border-slate-300 p-2 w-28">No. Kod Stok</th>
                <th class="border border-slate-300 p-2">Perihal Barang / Stok</th>
                <th class="border border-slate-300 p-2 text-center w-14">Unit</th>
                <th class="border border-slate-300 p-2 text-center w-16">Kuantiti</th>
                <th class="border border-slate-300 p-2 text-right w-20">Harga (RM)</th>
                <th class="border border-slate-300 p-2 text-right w-24">Jumlah (RM)</th>
                <th class="border border-slate-300 p-2 text-center w-20">Keadaan</th>
                <th class="border border-slate-300 p-2">Justifikasi & Syor Kaedah</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @if(isset($disposal->items) && count($disposal->items) > 0)
                @foreach($disposal->items as $idx => $item)
                @php $total += $item->total_price; @endphp
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->stockItem->item_code ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $item->stockItem->description ?? '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center">{{ $item->stockItem->uom->code ?? '' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-bold font-mono">{{ number_format($item->quantity) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-right font-mono font-bold">{{ number_format($item->total_price, 2) }}</td>
                    <td class="border border-slate-300 p-2 text-center text-[10px] font-semibold">{{ $item->condition }}</td>
                    <td class="border border-slate-300 p-2 text-[11px] leading-tight">
                        <div>{{ $item->justification }}</div>
                        <div class="font-bold text-slate-700">Syor: {{ $item->recommended_method }}</div>
                    </td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="9" class="p-4 text-center text-slate-400">Tiada item dipaparkan.</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="bg-slate-50 font-bold border-t border-slate-400">
                <td colspan="6" class="border border-slate-300 p-2 text-right uppercase">Jumlah Nilai Perolehan Asal:</td>
                <td class="border border-slate-300 p-2 text-right font-mono text-sm">RM {{ number_format($total, 2) }}</td>
                <td colspan="2" class="border border-slate-300 p-2"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Perakuan Lembaga Pemeriksa -->
    <div class="pt-6 border-t border-slate-200">
        <div class="font-bold uppercase text-[11px] mb-2">Perakuan Ahli Lembaga Pemeriksa Pelupusan:</div>
        <p class="text-xs text-slate-600 mb-4">
            Kami dengan ini memperakukan bahawa pemeriksaan fizikal telah dibuat ke atas setiap stok di atas dan bersetuju dengan keadaan serta syor kaedah pelupusan yang dicadangkan.
        </p>
        <div class="grid grid-cols-2 gap-8 text-xs">
            <div>
                <div>1. Pengerusi:</div>
                <div class="h-12"></div>
                <div class="font-bold">Nama: .................................................</div>
                <div>Jawatan: ................................................</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
            <div>
                <div>2. Ahli:</div>
                <div class="h-12"></div>
                <div class="font-bold">Nama: .................................................</div>
                <div>Jawatan: ................................................</div>
                <div>Tarikh: {{ date('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
