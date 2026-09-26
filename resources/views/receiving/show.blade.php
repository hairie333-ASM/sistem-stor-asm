@extends('layouts.app')

@section('title', 'Pemeriksaan BTB: ' . $receiving->btb_number)
@section('page_title', 'Borang Terimaan Barang-Barang (KEW.PS-1)')
@section('page_description', 'Pemeriksaan kualiti dan kuantiti barangan fizikal sebelum kemasukan ke dalam Daftar Stok (AM 6.2)')

@section('page_actions')
    <a href="{{ route('receiving.print-btb', $receiving) }}" target="_blank" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>🖨️</span>
        <span>Cetak Borang KEW.PS-1 (BTB)</span>
    </a>
    @if($receiving->rejections->isNotEmpty())
        @foreach($receiving->rejections as $rej)
            <a href="{{ route('rejections.print-bpb', $rej) }}" target="_blank" class="px-3.5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
                <span>⚠️</span>
                <span>Cetak Borang KEW.PS-2 (BPB)</span>
            </a>
        @endforeach
    @endif
@endsection

@section('content')
<div class="space-y-6">

    <!-- Document Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 border-b border-slate-200 pb-4 mb-4">
            <div>
                <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded border border-blue-200">
                    {{ $receiving->btb_number }}
                </span>
                <h3 class="text-base font-bold text-slate-900 mt-2">{{ $receiving->supplier_name }}</h3>
                <div class="text-xs text-slate-500 mt-0.5">{{ $receiving->supplier_address ?? 'Alamat tidak dinyatakan' }}</div>
            </div>
            <div class="text-left sm:text-right shrink-0">
                <span class="text-[11px] font-bold px-3 py-1 rounded-full border {{ $receiving->status === 'ACCEPTED' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($receiving->status === 'PARTIALLY_REJECTED' ? 'bg-rose-50 text-rose-800 border-rose-300' : 'bg-amber-50 text-amber-800 border-amber-300') }}">
                    {{ $receiving->status === 'ACCEPTED' ? '✓ DITERIMA (KEW.PS-1)' : ($receiving->status === 'PARTIALLY_REJECTED' ? '⚠️ PENOLAKAN SEBAHAGIAN (KEW.PS-2)' : '⏳ MENUNGGU PEMERIKSAAN') }}
                </span>
                <div class="text-xs text-slate-400 mt-1">Stor: <strong>{{ $receiving->store->name }}</strong></div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-700">
            <div>
                <span class="text-slate-400 text-[10px] block uppercase font-bold">No. Nota Hantaran (DO):</span>
                <span class="font-mono font-bold text-slate-900">{{ $receiving->delivery_order_number }}</span>
                <div class="text-[10px] text-slate-500">Tarikh DO: {{ $receiving->delivery_order_date?->format('d/m/Y') }}</div>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block uppercase font-bold">Pesanan Rasmi Kerajaan:</span>
                <span class="font-mono font-bold text-slate-900">{{ $receiving->po_contract_number ?? '-' }}</span>
                <div class="text-[10px] text-slate-500">Tarikh PO: {{ $receiving->po_contract_date?->format('d/m/Y') ?? '-' }}</div>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block uppercase font-bold">Jenis Terimaan:</span>
                <span class="font-bold text-slate-900">{{ $receiving->receipt_type }}</span>
                <div class="text-[10px] text-slate-500">{{ $receiving->carrier_info ?? '-' }}</div>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block uppercase font-bold">Pegawai Penerima:</span>
                <span class="font-bold text-slate-900">{{ $receiving->receivingOfficer->name ?? '-' }}</span>
                <div class="text-[10px] text-slate-500">{{ $receiving->receivingOfficer->position ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Physical Inspection Form / Results -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-900 text-white px-6 py-3 flex justify-between items-center">
            <h3 class="text-sm font-bold tracking-wide">PENGESAHAN KUANTITI & KUALITI FIZIKAL (AM 6.2)</h3>
            <span class="text-xs text-slate-400">Pegawai Penerima bertanggungjawab mengira dan memeriksa barang</span>
        </div>

        @if($receiving->status === 'DRAFT')
        <!-- Inspection Form in Action -->
        <form method="POST" action="{{ route('receiving.inspect', $receiving) }}" class="p-6 space-y-4">
            @csrf
            <div class="flex justify-between items-center text-xs mb-2">
                <div>
                    <label class="font-semibold text-slate-700 mr-2">Tarikh Pemeriksaan Fizikal *</label>
                    <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}" required class="px-3 py-1.5 border border-slate-300 rounded-lg">
                </div>
                <div class="text-xs text-amber-700 bg-amber-50 px-3 py-1 rounded border border-amber-200">
                    Kuantiti ditolak akan dimasukkan secara automatik ke dalam Borang Penolakan KEW.PS-2
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Item Stok</th>
                            <th class="py-2.5 px-2 text-center">Dipesan</th>
                            <th class="py-2.5 px-2 text-center">DO</th>
                            <th class="py-2.5 px-2 text-center">Fizikal Sampai</th>
                            <th class="py-2.5 px-2 text-center w-28 bg-emerald-50 text-emerald-900">Kuantiti Diterima *</th>
                            <th class="py-2.5 px-2 text-center w-28 bg-rose-50 text-rose-900">Kuantiti Ditolak *</th>
                            <th class="py-2.5 px-2 w-48">Sebab Penolakan (Jika Ada)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($receiving->items as $item)
                        <tr class="hover:bg-slate-50" x-data="{
                            received: {{ $item->received_quantity }},
                            accepted: {{ $item->received_quantity }},
                            rejected: 0,
                            updateAccepted() {
                                this.accepted = Math.max(0, this.received - this.rejected);
                            }
                        }">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-900">{{ $item->stockItem->description }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->stockItem->stock_code }} ({{ $item->stockItem->uom->code }})</div>
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono">{{ number_format($item->ordered_quantity, 0) }}</td>
                            <td class="py-2.5 px-2 text-center font-mono">{{ number_format($item->do_quantity, 0) }}</td>
                            <td class="py-2.5 px-2 text-center font-mono font-bold text-slate-900">{{ number_format($item->received_quantity, 0) }}</td>
                            <td class="py-2.5 px-2 text-center bg-emerald-50/50">
                                <input type="number" step="1" min="0" :max="received" :name="'items[{{ $item->id }}][accepted_quantity]'" x-model="accepted" required class="w-full text-center px-2 py-1 border border-emerald-300 rounded font-bold text-emerald-900">
                            </td>
                            <td class="py-2.5 px-2 text-center bg-rose-50/50">
                                <input type="number" step="1" min="0" :max="received" :name="'items[{{ $item->id }}][rejected_quantity]'" x-model="rejected" @input="updateAccepted()" required class="w-full text-center px-2 py-1 border border-rose-300 rounded font-bold text-rose-900">
                            </td>
                            <td class="py-2.5 px-2">
                                <select :name="'items[{{ $item->id }}][rejection_reason]'" x-show="rejected > 0" class="w-full px-2 py-1 border border-slate-300 rounded text-xs">
                                    <option value="DAMAGED">Rosak (Damaged)</option>
                                    <option value="QUANTITY_LESS">Kuantiti Kurang</option>
                                    <option value="QUANTITY_MORE">Kuantiti Lebih</option>
                                    <option value="WRONG_ITEM">Barang Tidak Sama</option>
                                    <option value="SPEC_MISMATCH">Tidak Ikut Spesifikasi</option>
                                    <option value="QUALITY_ISSUE">Isu Kualiti / Cacat</option>
                                    <option value="OTHER">Lain-lain</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1 text-xs">Catatan Pemeriksaan</label>
                <textarea name="remarks" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs" placeholder="Catatan pemerhatian kualiti barangan..."></textarea>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-200">
                <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-xs shadow transition">
                    Sahkan Pemeriksaan & Kemaskini Baki Daftar Stok
                </button>
            </div>
        </form>
        @else
        <!-- Completed Inspection Readonly Table -->
        <div class="p-6">
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Item Stok</th>
                            <th class="py-2.5 px-2 text-center">Dipesan</th>
                            <th class="py-2.5 px-2 text-center">DO</th>
                            <th class="py-2.5 px-2 text-center">Diterima Fizikal</th>
                            <th class="py-2.5 px-2 text-center text-emerald-800">Diterima (KEW.PS-1)</th>
                            <th class="py-2.5 px-2 text-center text-rose-800">Ditolak (KEW.PS-2)</th>
                            <th class="py-2.5 px-3 text-right">Harga Seunit</th>
                            <th class="py-2.5 px-3 text-right">Jumlah Nilai Diterima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($receiving->items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3">
                                <a href="{{ route('stock-register.show', $item->stockItem) }}" class="font-bold text-blue-700 hover:underline">
                                    {{ $item->stockItem->description }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->stockItem->stock_code }}</div>
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono">{{ number_format($item->ordered_quantity, 0) }}</td>
                            <td class="py-2.5 px-2 text-center font-mono">{{ number_format($item->do_quantity, 0) }}</td>
                            <td class="py-2.5 px-2 text-center font-mono">{{ number_format($item->received_quantity, 0) }}</td>
                            <td class="py-2.5 px-2 text-center font-mono font-bold text-emerald-800 bg-emerald-50/50">
                                {{ number_format($item->accepted_quantity, 0) }} {{ $item->stockItem->uom->code }}
                            </td>
                            <td class="py-2.5 px-2 text-center font-mono font-bold text-rose-800 bg-rose-50/50">
                                {{ number_format($item->rejected_quantity, 0) }} {{ $item->stockItem->uom->code }}
                            </td>
                            <td class="py-2.5 px-3 text-right">RM {{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2.5 px-3 text-right font-black text-slate-900">
                                RM {{ number_format($item->total_price, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex justify-between items-center">
                <span>Diperiksa oleh: <strong>{{ $receiving->receivingOfficer->name ?? '-' }}</strong> pada {{ $receiving->inspection_date?->format('d/m/Y') }}</span>
                <span>Jumlah Nilai Terimaan Diluluskan: <strong class="text-base text-slate-900">RM {{ number_format($receiving->total_amount, 2) }}</strong></span>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
