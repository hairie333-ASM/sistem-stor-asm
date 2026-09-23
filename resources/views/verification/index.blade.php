@extends('layouts.app')

@section('title', 'Verifikasi Stor & Pelarasan (TPS AM 6.6)')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'verifications' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.6</span>
                <span class="text-xs text-slate-500">Pemeriksaan & Verifikasi Stor</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Verifikasi Stor Tahunan & Pelarasan Stok</h1>
            <p class="text-sm text-slate-600">Pelantikan Pemverifikasi Bebas (KEW.PS-10), Jadual Verifikasi (KEW.PS-11), Laporan (KEW.PS-12), Sijil (KEW.PS-13), dan Pelarasan (KEW.PS-15/16).</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('verification.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Jadual Verifikasi Baru (KEW.PS-10/11)
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-slate-200">
        <nav class="flex space-x-8">
            <button @click="activeTab = 'verifications'" 
                    :class="activeTab === 'verifications' ? 'border-blue-600 text-blue-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="py-3 px-1 border-b-2 text-sm flex items-center space-x-2 transition">
                <span>Verifikasi Stor (KEW.PS-11 hingga 13)</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">{{ $verifications->total() }}</span>
            </button>
            <button @click="activeTab = 'adjustments'" 
                    :class="activeTab === 'adjustments' ? 'border-blue-600 text-blue-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="py-3 px-1 border-b-2 text-sm flex items-center space-x-2 transition">
                <span>Pelarasan Stok (KEW.PS-15 & KEW.PS-16)</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-800">{{ $adjustments->total() }}</span>
            </button>
        </nav>
    </div>

    <!-- Tab 1: Verifications -->
    <div x-show="activeTab === 'verifications'" class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                            <th class="py-3 px-4">No. Jadual</th>
                            <th class="py-3 px-4">Stor Diperiksa</th>
                            <th class="py-3 px-4">Tahun</th>
                            <th class="py-3 px-4">Pegawai Pemverifikasi Stor</th>
                            <th class="py-3 px-4">Tarikh Jadual</th>
                            <th class="py-3 px-4">Status & Pembekuan</th>
                            <th class="py-3 px-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($verifications as $v)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-700">
                                {{ $v->verification_number }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $v->store->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">
                                {{ $v->year }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div>1. <span class="font-semibold text-slate-800">{{ $v->verifier1->name ?? '-' }}</span></div>
                                <div>2. <span class="font-semibold text-slate-800">{{ $v->verifier2->name ?? '-' }}</span></div>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                {{ $v->scheduled_date }}
                            </td>
                            <td class="py-3 px-4">
                                @if($v->status === 'SCHEDULED')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">Dijadualkan</span>
                                @elseif($v->status === 'IN_PROGRESS')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 animate-pulse">
                                        Pemeriksaan Berjalan (Dibekukan)
                                    </span>
                                @elseif($v->status === 'COMPLETED')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Kiraan Selesai (KEW.PS-12)
                                    </span>
                                @elseif($v->status === 'APPROVED')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        Sijil Dikeluarkan (KEW.PS-13)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1.5">
                                <a href="{{ route('verification.show', $v) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition">
                                    Butiran / Kiraan
                                </a>
                                @if($v->status === 'APPROVED')
                                <a href="{{ route('verification.print-ps13', $v) }}" target="_blank" class="inline-flex items-center px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded text-xs font-semibold transition border border-emerald-200">
                                    Sijil KEW.PS-13
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Tiada rekod verifikasi stor dijumpai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($verifications->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $verifications->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Tab 2: Adjustments -->
    <div x-show="activeTab === 'adjustments'" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                            <th class="py-3 px-4">No. Pelarasan</th>
                            <th class="py-3 px-4">Stor Terlibat</th>
                            <th class="py-3 px-4">Sebab / Justifikasi</th>
                            <th class="py-3 px-4">Pemohon</th>
                            <th class="py-3 px-4">Status & Kelulusan</th>
                            <th class="py-3 px-4">Tarikh</th>
                            <th class="py-3 px-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($adjustments as $adj)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-purple-700">
                                {{ $adj->adjustment_number }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $adj->store->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                {{ $adj->reason }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                {{ $adj->requester->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($adj->status === 'SUBMITTED')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Menunggu Kelulusan</span>
                                @elseif($adj->status === 'APPROVED')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        Diluluskan ({{ $adj->perakuan_number ?? 'KEW.PS-16' }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $adj->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-right space-x-1.5">
                                <a href="{{ route('adjustments.print-ps15', $adj) }}" target="_blank" class="inline-flex items-center px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-800 rounded text-xs font-semibold transition border border-blue-200">
                                    KEW.PS-15
                                </a>
                                @if($adj->status === 'APPROVED')
                                <a href="{{ route('adjustments.print-ps16', $adj) }}" target="_blank" class="inline-flex items-center px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded text-xs font-semibold transition border border-emerald-200">
                                    KEW.PS-16
                                </a>
                                @elseif(auth()->user() && auth()->user()->hasRole(['ketua_jabatan', 'admin']))
                                <form action="{{ route('adjustments.approve', $adj) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Luluskan pelarasan stok ini? Baki inventori akan diselaraskan secara automatik.')" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition">
                                        Luluskan (KEW.PS-16)
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Tiada rekod permohonan pelarasan stok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($adjustments->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $adjustments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
