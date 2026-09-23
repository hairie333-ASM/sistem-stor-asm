@extends('layouts.app')

@section('title', 'Penerimaan Stok (KEW.PS-1 & KEW.PS-2)')
@section('page_title', 'Penerimaan Barang-Barang Stor (AM 6.2)')
@section('page_description', 'Pemeriksaan dokumen, pengesahan fizikal, Borang Terimaan (KEW.PS-1) dan Borang Penolakan (KEW.PS-2)')

@section('page_actions')
    <a href="{{ route('receiving.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>+</span>
        <span>Daftar Penerimaan Baru (BTB)</span>
    </a>
@endsection

@section('content')
<div class="space-y-4">

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-3 text-xs">
        <form method="GET" action="{{ route('receiving.index') }}" class="flex items-center space-x-2 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. BTB, pembekal, DO..." class="px-3 py-1.5 border border-slate-300 rounded-lg w-64 focus:outline-none focus:ring-1 focus:ring-blue-600">
            <select name="status" class="px-3 py-1.5 border border-slate-300 rounded-lg">
                <option value="">Semua Status</option>
                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Menunggu Pemeriksaan Fizikal</option>
                <option value="ACCEPTED" {{ request('status') === 'ACCEPTED' ? 'selected' : '' }}>Diterima Sepenuhnya</option>
                <option value="PARTIALLY_REJECTED" {{ request('status') === 'PARTIALLY_REJECTED' ? 'selected' : '' }}>Diterima Sebahagian / Ditolak (BPB)</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-blue-700 text-white rounded-lg font-semibold">Tapis</button>
            @if(request('search') || request('status'))
                <a href="{{ route('receiving.index') }}" class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded-lg">Reset</a>
            @endif
        </form>
    </div>

    <!-- Receivings Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">No. BTB (KEW.PS-1)</th>
                        <th class="py-3 px-4">Nama Pembekal</th>
                        <th class="py-3 px-4">No. DO / Nota Hantaran</th>
                        <th class="py-3 px-4">No. Pesanan Rasmi</th>
                        <th class="py-3 px-4 text-center">Tarikh Terima</th>
                        <th class="py-3 px-4 text-center">Bilangan Item</th>
                        <th class="py-3 px-4 text-right">Jumlah Nilai (RM)</th>
                        <th class="py-3 px-4 text-center">Status Pemeriksaan</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receivings as $rec)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 whitespace-nowrap font-mono font-bold text-blue-700">
                            <a href="{{ route('receiving.show', $rec) }}" class="hover:underline">
                                {{ $rec->btb_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $rec->supplier_name }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 font-mono">
                            {{ $rec->delivery_order_number }}
                            <div class="text-[10px] text-slate-400 font-sans">{{ $rec->delivery_order_date?->format('d/m/Y') }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-600 font-mono">
                            {{ $rec->po_contract_number ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center text-slate-600 whitespace-nowrap">
                            {{ $rec->delivery_order_date ? $rec->delivery_order_date->format('d/m/Y') : $rec->created_at->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-800">
                                {{ $rec->items->count() }} item
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-black text-slate-900 whitespace-nowrap">
                            RM {{ number_format($rec->total_amount, 2) }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($rec->status === 'DRAFT')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    ⏳ Menunggu Pemeriksaan
                                </span>
                            @elseif($rec->status === 'ACCEPTED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    ✓ Diterima (KEW.PS-1)
                                </span>
                            @elseif($rec->status === 'PARTIALLY_REJECTED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    ⚠️ Penolakan (KEW.PS-2)
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-1.5">
                                <a href="{{ route('receiving.show', $rec) }}" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-medium text-[11px] shadow-sm">
                                    {{ $rec->status === 'DRAFT' ? 'Periksa' : 'Lihat' }}
                                </a>
                                <a href="{{ route('receiving.print-btb', $rec) }}" target="_blank" title="Cetak KEW.PS-1" class="p-1 rounded hover:bg-slate-100 text-blue-700 font-bold">
                                    🖨️
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">
                            Tiada rekod penerimaan ditemui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($receivings->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $receivings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
