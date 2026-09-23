@extends('layouts.app')

@section('title', 'Kehilangan & Hapus Kira (TPS AM 6.10)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.10</span>
                <span class="text-xs text-slate-500">Kawalan Kehilangan & Hapus Kira</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Kehilangan & Hapus Kira Stok (KEW.PS-32 hingga 36)</h1>
            <p class="text-sm text-slate-600">Laporan Awal (KEW.PS-32), Jawatankuasa Siasatan (KEW.PS-33), Laporan Akhir (KEW.PS-34), Sijil Hapus Kira (KEW.PS-35) & Laporan Tahunan (KEW.PS-36).</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('loss.create') }}" class="inline-flex items-center px-4 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Lapor Kehilangan Baru (KEW.PS-32)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('loss.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-56">
                <select name="status" class="w-full text-xs border-slate-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                    <option value="">-- Semua Status --</option>
                    <option value="REPORTED" {{ request('status') === 'REPORTED' ? 'selected' : '' }}>Dilaporkan (Menunggu Siasatan)</option>
                    <option value="INVESTIGATING" {{ request('status') === 'INVESTIGATING' ? 'selected' : '' }}>Siasatan Berjalan (KEW.PS-33)</option>
                    <option value="SUBMITTED_FOR_WRITE_OFF" {{ request('status') === 'SUBMITTED_FOR_WRITE_OFF' ? 'selected' : '' }}>Laporan Akhir Dikemukakan (KEW.PS-34)</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Hapus Kira Diluluskan (KEW.PS-35)</option>
                    <option value="SURCHARGE_RECOMMENDED" {{ request('status') === 'SURCHARGE_RECOMMENDED' ? 'selected' : '' }}>Syor Tindakan Surcaj</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                Tapis Rekod
            </button>
            @if(request()->filled('status'))
            <a href="{{ route('loss.index') }}" class="text-xs text-rose-600 hover:underline">
                Kosongkan Tapisan
            </a>
            @endif
        </form>
    </div>

    <!-- Loss Cases List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">No. Kes / Borang</th>
                        <th class="py-3 px-4">Stor Terlibat</th>
                        <th class="py-3 px-4">Tarikh Berlaku / Dikesan</th>
                        <th class="py-3 px-4">No. Laporan Polis</th>
                        <th class="py-3 px-4 text-right">Nilai Kehilangan (RM)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($lossCases as $case)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-rose-700">
                            {{ $case->case_number }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-800">
                            {{ $case->store->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            <div>Berlaku: {{ $case->incident_date }}</div>
                            <div>Dikesan: {{ $case->discovery_date }}</div>
                        </td>
                        <td class="py-3 px-4 text-xs font-mono">
                            {{ $case->police_report_no ?: 'Tiada (Dalaman)' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-rose-700">
                            {{ number_format($case->total_loss_value, 2) }}
                        </td>
                        <td class="py-3 px-4">
                            @if($case->status === 'REPORTED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Laporan Awal (KEW.PS-32)</span>
                            @elseif($case->status === 'INVESTIGATING')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Siasatan (KEW.PS-33)</span>
                            @elseif($case->status === 'SUBMITTED_FOR_WRITE_OFF')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Laporan Akhir (KEW.PS-34)</span>
                            @elseif($case->status === 'APPROVED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Hapus Kira (KEW.PS-35)</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Syor Surcaj</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right space-x-1.5">
                            <a href="{{ route('loss.show', $case) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition">
                                Butiran & Siasatan
                            </a>
                            <a href="{{ route('loss.print-ps32', $case) }}" target="_blank" class="inline-flex items-center px-2 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 rounded text-xs font-semibold transition border border-rose-200">
                                KEW.PS-32
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            Tiada rekod kes kehilangan stok dilaporkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lossCases->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $lossCases->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
