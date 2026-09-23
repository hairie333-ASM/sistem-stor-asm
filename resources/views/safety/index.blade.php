@extends('layouts.app')

@section('title', 'Keselamatan & Kebersihan Stor (TPS AM 6.7)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.7</span>
                <span class="text-xs text-slate-500">Kawalan Keselamatan & Kebersihan Stor</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Pemeriksaan Keselamatan & Kebersihan Stor</h1>
            <p class="text-sm text-slate-600">Pemantauan berjadual keselamatan fizikal, pencegahan kebakaran, kebersihan, dan kawalan makhluk perosak mengikut AM 6.7.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('safety.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Pemeriksaan Baru
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('safety.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-56">
                <select name="category" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Kategori --</option>
                    <option value="SECURITY" {{ request('category') === 'SECURITY' ? 'selected' : '' }}>Keselamatan Fizikal & Kunci</option>
                    <option value="FIRE_SAFETY" {{ request('category') === 'FIRE_SAFETY' ? 'selected' : '' }}>Pencegahan Kebakaran</option>
                    <option value="CLEANLINESS" {{ request('category') === 'CLEANLINESS' ? 'selected' : '' }}>Kebersihan Premis & Rak</option>
                    <option value="PEST_CONTROL" {{ request('category') === 'PEST_CONTROL' ? 'selected' : '' }}>Kawalan Makhluk Perosak</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                Tapis Rekod
            </button>
            @if(request()->filled('category'))
            <a href="{{ route('safety.index') }}" class="text-xs text-rose-600 hover:underline">
                Kosongkan Tapisan
            </a>
            @endif
        </form>
    </div>

    <!-- Inspections List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">Tarikh Periksa</th>
                        <th class="py-3 px-4">Stor Diperiksa</th>
                        <th class="py-3 px-4">Kategori Pemeriksaan</th>
                        <th class="py-3 px-4">Pegawai Pemeriksa</th>
                        <th class="py-3 px-4 text-center">Skor Pematuhan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($inspections as $ins)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-700">
                            {{ $ins->inspection_date }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-800">
                            {{ $ins->store->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            @if($ins->category === 'SECURITY')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">Keselamatan Fizikal</span>
                            @elseif($ins->category === 'FIRE_SAFETY')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded bg-rose-100 text-rose-800">Pencegahan Kebakaran</span>
                            @elseif($ins->category === 'CLEANLINESS')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">Kebersihan</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800">Kawalan Makhluk Perosak</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <span class="font-semibold text-slate-800">{{ $ins->inspector->name ?? '-' }}</span>
                            <div class="text-slate-400">{{ $ins->inspector->position ?? '' }}</div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold {{ $ins->score >= 80 ? 'bg-emerald-100 text-emerald-800' : ($ins->score >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ $ins->score }}%
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($ins->status === 'PASS')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Memuaskan</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 animate-pulse">Tindakan Diperlukan</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('safety.show', $ins) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition">
                                Butiran / Semakan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            Tiada rekod pemeriksaan keselamatan dan kebersihan dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inspections->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $inspections->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
