@extends('layouts.app')

@section('title', 'Daftar Stok Kerajaan (KEW.PS-3)')
@section('page_title', 'Daftar Stok Kerajaan (KEW.PS-3)')
@section('page_description', 'Pendaftaran rasmi dan rekod kawalan semua item stok yang diterima di stor (Pekeliling AM 6.3)')

@section('content')
<div class="space-y-4">
    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
        <form method="GET" action="{{ route('stock-register.index') }}" class="flex items-center space-x-2 text-xs w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kod stok atau perihal..." class="px-3 py-2 border border-slate-300 rounded-lg w-72 focus:outline-none focus:ring-1 focus:ring-blue-600">
            <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('stock-register.index') }}" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg">Reset</a>
            @endif
        </form>

        <div class="flex items-center space-x-2">
            <a href="{{ route('stock.group-ab') }}" class="px-3 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-semibold shadow transition">
                🏷️ Penentuan Kumpulan A & B (KEW.PS-5)
            </a>
            <a href="{{ route('stock.expiry') }}" class="px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-semibold shadow transition">
                ⏳ Pemantauan Luput (KEW.PS-6)
            </a>
        </div>
    </div>

    <!-- Register Grid / List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-400 hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        {{ $item->stock_code }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $item->stock_group === 'A' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                        Kumpulan {{ $item->stock_group }}
                    </span>
                </div>

                <h3 class="font-bold text-slate-900 text-sm mb-1 leading-snug">
                    <a href="{{ route('stock-register.show', $item) }}" class="hover:text-blue-700 hover:underline">
                        {{ $item->description }}
                    </a>
                </h3>
                <div class="text-[11px] text-slate-500 mb-3">{{ $item->category->name ?? 'Kategori Am' }}</div>

                <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-100 mb-3">
                    <div>
                        <span class="text-slate-400 text-[10px] block">Baki Kuantiti:</span>
                        <span class="font-black text-slate-900 text-base">{{ number_format($item->current_quantity, 0) }} {{ $item->uom->code }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Nilai Semasa:</span>
                        <span class="font-bold text-blue-900 text-xs">RM {{ number_format($item->total_value, 2) }}</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-600 flex justify-between items-center">
                    <span>Lokasi: <strong class="font-mono">{{ $item->defaultLocation->full_code ?? '-' }}</strong></span>
                    <span class="font-semibold {{ $item->stock_level_status['badge'] }} px-2 py-0.5 rounded-full text-[10px]">
                        {{ $item->stock_level_status['label'] }}
                    </span>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex justify-between items-center text-xs">
                <a href="{{ route('stock-register.show', $item) }}" class="text-blue-700 font-bold hover:underline flex items-center space-x-1">
                    <span>Buka Kad KEW.PS-3</span>
                    <span>→</span>
                </a>
                <a href="{{ route('stock-register.print', $item) }}" target="_blank" class="text-slate-500 hover:text-slate-800" title="Cetak Borang KEW.PS-3">
                    🖨️ Cetak
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white p-8 text-center text-slate-400 rounded-xl border border-slate-200">
            Tiada item daftar stok ditemui.
        </div>
        @endforelse
    </div>

    @if($items->hasPages())
    <div class="p-4 bg-white rounded-xl border border-slate-200">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
