@extends('layouts.app')

@section('title', 'Pelupusan ' . $disposal->disposal_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.9</span>
                <span class="text-xs text-slate-500">Borang Pelupusan Stok</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $disposal->disposal_number }}</h1>
                @if($disposal->status === 'PROPOSED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Kelulusan</span>
                @elseif($disposal->status === 'APPROVED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Diluluskan (Sedia Tindakan)</span>
                @elseif($disposal->status === 'COMPLETED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai Dilupuskan (KEW.PS-22)</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('disposal.print-ps19', $disposal) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition border border-slate-300">
                KEW.PS-19
            </a>
            <a href="{{ route('disposal.print-ps20', $disposal) }}" target="_blank" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded text-xs font-semibold transition border border-amber-200">
                KEW.PS-20
            </a>
            @if(in_array($disposal->status, ['APPROVED', 'COMPLETED']))
            <a href="{{ route('disposal.print-ps21', $disposal) }}" target="_blank" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-800 rounded text-xs font-semibold transition border border-blue-200">
                KEW.PS-21
            </a>
            @endif
            @if($disposal->status === 'COMPLETED')
            <a href="{{ route('disposal.print-ps22', $disposal) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition shadow-sm">
                Sijil KEW.PS-22
            </a>
            <a href="{{ route('disposal.print-ps23', $disposal) }}" target="_blank" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 rounded text-xs font-semibold transition border border-purple-200">
                KEW.PS-23
            </a>
            @endif
            <a href="{{ route('disposal.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition ml-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Metadata Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Maklumat Permohonan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Stor:</span> <span class="font-bold text-slate-800">{{ $disposal->store->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Kaedah:</span> <span class="font-semibold text-slate-800">{{ $disposal->disposal_method }}</span></div>
                <div><span class="text-slate-500 text-xs">Nilai Asal Perolehan:</span> <span class="font-bold font-mono text-slate-800">RM {{ number_format($disposal->total_original_value, 2) }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Daftar:</span> <span class="text-slate-700">{{ $disposal->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Lembaga Pemeriksa (KEW.PS-19)</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">No. Rujukan Lantikan:</span> <span class="font-mono text-xs text-slate-700">{{ $disposal->committee_appointment_ref }}</span></div>
                @foreach($disposal->committees as $comm)
                <div class="text-xs">
                    <span class="font-bold text-slate-700">{{ $comm->role }}:</span> 
                    <span class="text-slate-800">{{ $comm->officer_name }}</span> ({{ $comm->position }})
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Kelulusan & Penyempurnaan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Rujukan Kelulusan:</span> <span class="font-mono font-bold text-blue-800">{{ $disposal->approval_reference ?: 'Menunggu Kelulusan' }}</span></div>
                <div><span class="text-slate-500 text-xs">Sijil Pelupusan:</span> <span class="font-mono font-bold text-emerald-800">{{ $disposal->completion_cert_number ?: 'Belum Dijana' }}</span></div>
                <div><span class="text-slate-500 text-xs">Hasil Diperoleh:</span> <span class="font-mono font-bold text-emerald-700">RM {{ number_format($disposal->total_revenue, 2) }}</span></div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-800">Senarai Stok Dalam Laporan Lembaga Pemeriksa (KEW.PS-20)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4">Kod & Perihal Stok</th>
                        <th class="py-3 px-4 text-center">Kuantiti Lupus</th>
                        <th class="py-3 px-4 text-right">Harga Seunit (RM)</th>
                        <th class="py-3 px-4 text-right">Jumlah Nilai (RM)</th>
                        <th class="py-3 px-4">Keadaan Semasa</th>
                        <th class="py-3 px-4">Justifikasi & Syor Kaedah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($disposal->items as $item)
                    <tr>
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                            <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-rose-700">
                            {{ number_format($item->quantity) }} {{ $item->stockItem->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-700">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                            {{ number_format($item->total_price, 2) }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800">
                                {{ $item->condition }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            <div>{{ $item->justification }}</div>
                            <div class="font-bold text-slate-700 mt-0.5">Syor: {{ $item->recommended_method }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Forms depending on Lifecycle -->
    <!-- 1. APPROVAL ACTION (Kuasa Melulus) -->
    @if($disposal->status === 'PROPOSED')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h4 class="text-base font-bold text-blue-900">Tindakan Kuasa Melulus Pelupusan (KEW.PS-21)</h4>
            <p class="text-xs text-blue-800 mt-1">Luluskan laporan Lembaga Pemeriksa dan keluarkan Surat Kelulusan Pelupusan Stok (KEW.PS-21).</p>
        </div>
        @if(auth()->user() && auth()->user()->hasRole(['ketua_jabatan', 'admin', 'pegawai_pelulus']))
        <form action="{{ route('disposal.approve', $disposal) }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Luluskan pelupusan stok ini di bawah KEW.PS-21?')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Luluskan Pelupusan (KEW.PS-21)
            </button>
        </form>
        @else
        <span class="text-xs font-semibold text-blue-800 bg-white px-3 py-1.5 rounded-lg border border-blue-300">Menunggu Kelulusan Kuasa Melulus</span>
        @endif
    </div>

    <!-- 2. PHYSICAL COMPLETION ACTION (Urus Setia Pelupusan / Pegawai Stor) -->
    @elseif($disposal->status === 'APPROVED')
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 space-y-4">
        <div>
            <h4 class="text-base font-bold text-emerald-900">Tindakan Pelupusan Fizikal & Pengeluaran Sijil Pelupusan (KEW.PS-22)</h4>
            <p class="text-xs text-emerald-800 mt-1">Setelah tindakan fizikal (jualan, pemusnahan, pindahan, dsb.) selesai disaksikan, sahkan untuk mengeluarkan stok dari buku inventori dan menjana Sijil KEW.PS-22.</p>
        </div>
        <form action="{{ route('disposal.complete', $disposal) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-emerald-800 uppercase tracking-wider mb-1">Hasil Pelupusan Diperoleh (RM, Jika Ada)</label>
                <input type="number" step="0.01" min="0" name="total_revenue" value="0.00" class="w-full text-sm font-bold border-emerald-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 bg-white">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" onclick="return confirm('Sahkan pelupusan fizikal telah sempurna? Kuantiti stok akan dihapus kira dari inventori aktif secara kekal.')" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow transition">
                    Sahkan Pelupusan & Jana Sijil KEW.PS-22
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
