@extends('layouts.app')

@section('title', 'Butiran Pemulangan ' . $return->return_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.5</span>
                <span class="text-xs text-slate-500">Pemeriksaan Pemulangan Stok</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $return->return_number }}</h1>
                @if($return->status === 'PENDING_INSPECTION')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Pemeriksaan</span>
                @elseif($return->status === 'ACCEPTED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Diterima & Diselaraskan</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('returns.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Kembali ke Senarai
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Maklumat Pemohon -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Maklumat Pemohon</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Nama:</span> <span class="font-bold text-slate-800">{{ $return->user->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Jawatan:</span> <span class="text-slate-700">{{ $return->user->position ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Bahagian/Unit:</span> <span class="text-slate-700">{{ $return->user->department ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Pulang:</span> <span class="text-slate-700">{{ $return->created_at->format('d/m/Y H:i') }}</span></div>
            </div>
        </div>

        <!-- Maklumat Stor & Pesanan Asal -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Stor & Rujukan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Stor Destinasi:</span> <span class="font-bold text-blue-800">{{ $return->store->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Jenis Pemulangan:</span> 
                    <span class="font-semibold text-slate-800">
                        @if($return->return_type === 'FULL') Penuh (Semua)
                        @elseif($return->return_type === 'PARTIAL') Sebahagian
                        @elseif($return->return_type === 'UNUSED') Belum Digunakan
                        @else Rosak Fizikal
                        @endif
                    </span>
                </div>
                <div><span class="text-slate-500 text-xs">Pesanan Asal:</span> 
                    @if($return->stockRequest)
                        <a href="{{ route('requests.show', $return->stockRequest) }}" class="text-blue-600 hover:underline font-mono font-semibold">{{ $return->stockRequest->request_number }}</a>
                    @else
                        <span class="text-slate-400">Tiada rujukan langsung</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pemeriksaan Pegawai Stor -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Pegawai Pemeriksa Stor</h3>
            <div class="text-sm space-y-1.5">
                @if($return->inspector)
                    <div><span class="text-slate-500 text-xs">Pemeriksa:</span> <span class="font-bold text-slate-800">{{ $return->inspector->name }}</span></div>
                    <div><span class="text-slate-500 text-xs">Tarikh Pemeriksaan:</span> <span class="text-slate-700">{{ $return->inspected_at ? \Carbon\Carbon::parse($return->inspected_at)->format('d/m/Y H:i') : '-' }}</span></div>
                    <div><span class="text-slate-500 text-xs">Nota:</span> <span class="italic text-slate-600">{{ $return->inspection_notes ?: 'Tiada nota tambahan' }}</span></div>
                @else
                    <div class="p-3 bg-amber-50 rounded-lg text-amber-800 text-xs">
                        Pemeriksaan fizikal belum dilakukan oleh Pegawai Stor.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Senarai Item & Borang Pemeriksaan -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Senarai Item Pemulangan</h3>
                <p class="text-xs text-slate-500">Pemeriksaan fizikal dan pengesahan kuantiti masuk semula ke daftar stok (KEW.PS-3 Bahagian B).</p>
            </div>
        </div>

        @if($return->status === 'PENDING_INSPECTION')
        <!-- Borang Pemeriksaan untuk Pegawai Stor -->
        <form action="{{ route('returns.inspect', $return) }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod Stok & Perihal</th>
                            <th class="py-3 px-4">Kuantiti Pulang</th>
                            <th class="py-3 px-4">Keadaan Dilaporkan</th>
                            <th class="py-3 px-4 w-44">Kuantiti Diterima Masuk</th>
                            <th class="py-3 px-4 w-44">Keadaan Fizikal Disahkan</th>
                            <th class="py-3 px-4">Catatan Pemohon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($return->items as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ number_format($item->returned_quantity) }} {{ $item->stockItem->uom->code ?? '' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $item->condition === 'GOOD' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $item->condition === 'GOOD' ? 'BAIK' : 'ROSAK' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <input type="number" step="0.01" min="0" max="{{ $item->returned_quantity }}" 
                                       name="items[{{ $item->id }}][accepted_quantity]" 
                                       value="{{ $item->returned_quantity }}" 
                                       required class="w-full text-sm border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 font-bold text-emerald-800">
                            </td>
                            <td class="py-3 px-4">
                                <select name="items[{{ $item->id }}][condition]" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="GOOD" {{ $item->condition === 'GOOD' ? 'selected' : '' }}>Baik (Masuk Inventori Aktif)</option>
                                    <option value="DAMAGED" {{ $item->condition === 'DAMAGED' ? 'selected' : '' }}>Rosak (Pindah ke Kuarantin / Pelupusan)</option>
                                </select>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $item->remarks ?: '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-2 border-t border-slate-100 pt-4">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Pemeriksaan Pegawai Stor</label>
                <textarea name="inspection_notes" rows="2" class="w-full text-sm border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: Barangan disemak dalam keadaan baik dan dipulangkan ke rak A-01-02."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="submit" onclick="return confirm('Sahkan pemeriksaan pemulangan stok ini? Baki daftar stok akan dikemaskini secara automatik.')" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow transition">
                    Sahkan Pemeriksaan & Kemaskini Daftar Stok
                </button>
            </div>
        </form>
        @else
        <!-- Paparan Selesai Diperiksa -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4">Kod Stok & Perihal</th>
                        <th class="py-3 px-4 text-right">Kuantiti Pulang</th>
                        <th class="py-3 px-4 text-right">Kuantiti Diterima</th>
                        <th class="py-3 px-4">Keadaan Disahkan</th>
                        <th class="py-3 px-4">Status Pengemaskinian</th>
                        <th class="py-3 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($return->items as $item)
                    <tr>
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                            <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-slate-600">
                            {{ number_format($item->returned_quantity) }} {{ $item->stockItem->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-700">
                            {{ number_format($item->accepted_quantity) }} {{ $item->stockItem->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $item->condition === 'GOOD' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $item->condition === 'GOOD' ? 'BAIK (AKTIF)' : 'ROSAK (KUARANTIN)' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center text-xs font-semibold text-emerald-700">
                                <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Dikemaskini ke KEW.PS-3
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500">
                            {{ $item->remarks ?: '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
