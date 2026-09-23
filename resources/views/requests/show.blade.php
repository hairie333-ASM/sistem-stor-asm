@extends('layouts.app')

@section('title', 'Pesanan Stok: ' . $stockRequest->request_number)
@section('page_title', $stockRequest->form_type . ' — ' . $stockRequest->request_number)
@section('page_description', 'Aliran kerja pesanan, kelulusan, pengeluaran stor dan perakuan penerima (Pekeliling AM 6.5)')

@section('page_actions')
    @if($stockRequest->form_type === 'KEW.PS-8')
        <a href="{{ route('requests.print-ps8', $stockRequest) }}" target="_blank" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>🖨️</span>
            <span>Cetak Borang KEW.PS-8</span>
        </a>
    @else
        <a href="{{ route('requests.print-ps7', $stockRequest) }}" target="_blank" class="px-3.5 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>🖨️</span>
            <span>Cetak Borang KEW.PS-7</span>
        </a>
    @endif
    @if(in_array($stockRequest->status, ['APPROVED', 'PARTIALLY_APPROVED', 'ISSUED', 'COMPLETED']))
        <a href="{{ route('requests.packing.create', $stockRequest) }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>📦</span>
            <span>Borang Pembungkusan (KEW.PS-9)</span>
        </a>
    @endif
@endsection

@section('content')
<div class="space-y-6">

    <!-- Requisition Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex justify-between items-start border-b border-slate-200 pb-4 mb-4">
            <div>
                <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded border border-blue-200">
                    {{ $stockRequest->request_number }}
                </span>
                <span class="ml-2 text-xs font-bold px-2 py-0.5 rounded {{ $stockRequest->form_type === 'KEW.PS-8' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ $stockRequest->form_type }}
                </span>
                <h3 class="text-base font-bold text-slate-900 mt-2">{{ $stockRequest->purpose }}</h3>
                <div class="text-xs text-slate-500">Bahagian / Unit: <strong>{{ $stockRequest->department }}</strong> | Stor: <strong>{{ $stockRequest->store->name }}</strong></div>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-bold px-3 py-1 rounded-full border {{ $stockRequest->status === 'COMPLETED' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($stockRequest->status === 'REJECTED' ? 'bg-rose-50 text-rose-800 border-rose-300' : 'bg-blue-50 text-blue-800 border-blue-300') }}">
                    {{ $stockRequest->status }}
                </span>
                <div class="text-[11px] text-slate-400 mt-1">Keutamaan: <strong class="{{ $stockRequest->priority === 'URGENT' ? 'text-rose-600' : 'text-slate-700' }}">{{ $stockRequest->priority }}</strong></div>
            </div>
        </div>

        <!-- Workflow Progress Steps -->
        <div class="grid grid-cols-4 gap-2 pt-2 text-center text-xs">
            <div class="p-2.5 rounded-xl border {{ $stockRequest->created_at ? 'bg-blue-50 border-blue-300 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                <div class="font-bold">1. Permohonan</div>
                <div class="text-[10px] text-slate-500">{{ $stockRequest->requester->name }}</div>
                <div class="text-[9px] text-slate-400">{{ $stockRequest->created_at->format('d/m/Y') }}</div>
            </div>

            <div class="p-2.5 rounded-xl border {{ $stockRequest->approved_at ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : ($stockRequest->status === 'SUBMITTED' ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                <div class="font-bold">2. Kelulusan</div>
                <div class="text-[10px] text-slate-500">{{ $stockRequest->approver->name ?? 'Pegawai Pelulus' }}</div>
                <div class="text-[9px] text-slate-400">{{ $stockRequest->approved_at?->format('d/m/Y') ?? 'Menunggu' }}</div>
            </div>

            <div class="p-2.5 rounded-xl border {{ $stockRequest->issued_at ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : ($stockRequest->status === 'APPROVED' ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                <div class="font-bold">3. Pengeluaran</div>
                <div class="text-[10px] text-slate-500">{{ $stockRequest->issuer->name ?? 'Pegawai Stor' }}</div>
                <div class="text-[9px] text-slate-400">{{ $stockRequest->issued_at?->format('d/m/Y') ?? 'Menunggu' }}</div>
            </div>

            <div class="p-2.5 rounded-xl border {{ $stockRequest->received_at ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : ($stockRequest->status === 'ISSUED' ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                <div class="font-bold">4. Perakuan Terima</div>
                <div class="text-[10px] text-slate-500">{{ $stockRequest->recipient->name ?? $stockRequest->requester->name }}</div>
                <div class="text-[9px] text-slate-400">{{ $stockRequest->received_at?->format('d/m/Y') ?? 'Menunggu' }}</div>
            </div>
        </div>
    </div>

    <!-- STAGE 1: ACTION BY PEGAWAI PELULUS -->
    @if($stockRequest->status === 'SUBMITTED')
    <div class="bg-white rounded-2xl border-2 border-blue-400 shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Tindakan Kelulusan (Pegawai Pelulus)</h3>
                <p class="text-xs text-slate-500">Semak permohonan stok dan tetapkan kuantiti diluluskan. Stok akan diperuntukkan secara automatik.</p>
            </div>
            <span class="px-2.5 py-1 rounded bg-blue-100 text-blue-900 text-xs font-bold">Pegawai Pelulus Bertauliah</span>
        </div>

        @if(auth()->user()->id === $stockRequest->requester_id && !auth()->user()->hasRole('admin'))
            <div class="p-3 bg-amber-50 border border-amber-300 text-amber-800 rounded-xl text-xs mb-4">
                ⚠️ <strong>Pemisahan Tugas (Separation of Duties):</strong> Anda adalah pemohon bagi pesanan ini dan tidak boleh meluluskan permohonan sendiri. Sila log masuk sebagai Pegawai Pelulus yang ditetapkan.
            </div>
        @else
        <form method="POST" action="{{ route('requests.approve', $stockRequest) }}" class="space-y-4 text-xs">
            @csrf
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Item Stok</th>
                            <th class="py-2.5 px-3 text-center">Baki Tersedia Stor</th>
                            <th class="py-2.5 px-3 text-center">Kuantiti Dimohon</th>
                            <th class="py-2.5 px-3 text-center w-36 bg-blue-50 text-blue-900">Kuantiti Diluluskan *</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stockRequest->items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-900">{{ $item->stockItem->description }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->stockItem->stock_code }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-slate-700">
                                {{ number_format($item->stockItem->current_quantity, 0) }} {{ $item->stockItem->uom->code }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-blue-900">
                                {{ number_format($item->requested_quantity, 0) }} {{ $item->stockItem->uom->code }}
                            </td>
                            <td class="py-2.5 px-3 text-center bg-blue-50/50">
                                <input type="number" step="1" min="0" max="{{ $item->stockItem->current_quantity }}" name="items[{{ $item->id }}][approved_quantity]" value="{{ $item->requested_quantity }}" required class="w-full text-center px-2 py-1.5 border border-blue-300 rounded font-black text-blue-900">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Ulasan / Catatan Kelulusan</label>
                <input type="text" name="approval_remarks" placeholder="cth: Diluluskan mengikut peruntukan semasa..." class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="submit" name="action" value="REJECT" onclick="return confirm('Tolak permohonan pesanan stok ini?')" class="px-5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg font-bold">
                    Tolak Permohonan
                </button>
                <button type="submit" name="action" value="APPROVE" class="px-6 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold shadow">
                    Luluskan & Peruntukkan Stok
                </button>
            </div>
        </form>
        @endif
    </div>
    @endif

    <!-- STAGE 2: ACTION BY PEGAWAI STOR (ISSUE) -->
    @if(in_array($stockRequest->status, ['APPROVED', 'PARTIALLY_APPROVED']))
    <div class="bg-white rounded-2xl border-2 border-emerald-400 shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Tindakan Pengeluaran (Pegawai Stor)</h3>
                <p class="text-xs text-slate-500">Stok telah diluluskan. Pegawai stor mengeluarkan fizikal stok dan mengemaskini lejar KEW.PS-3 secara automatik.</p>
            </div>
            <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-900 text-xs font-bold">Pegawai Stor Bertauliah</span>
        </div>

        <form method="POST" action="{{ route('requests.issue', $stockRequest) }}" class="space-y-4 text-xs">
            @csrf
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Item Stok</th>
                            <th class="py-2.5 px-3 text-center">Diluluskan</th>
                            <th class="py-2.5 px-3 text-center">Lokasi Simpanan</th>
                            <th class="py-2.5 px-3 text-center w-36 bg-emerald-50 text-emerald-900">Kuantiti Dikeluarkan *</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stockRequest->items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-900">{{ $item->stockItem->description }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->stockItem->stock_code }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-blue-900">
                                {{ number_format($item->approved_quantity, 0) }} {{ $item->stockItem->uom->code }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono text-slate-700">
                                {{ $item->stockItem->defaultLocation->full_code ?? 'Lokasi Am' }}
                            </td>
                            <td class="py-2.5 px-3 text-center bg-emerald-50/50">
                                <input type="number" step="1" min="0" max="{{ $item->approved_quantity }}" name="items[{{ $item->id }}][issued_quantity]" value="{{ $item->approved_quantity }}" required class="w-full text-center px-2 py-1.5 border border-emerald-300 rounded font-black text-emerald-900">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-xs shadow transition">
                    Keluarkan Stok & Kemaskini Daftar Stok (KEW.PS-3)
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- STAGE 3: ACTION BY RECIPIENT (CONFIRM RECEIPT) -->
    @if($stockRequest->status === 'ISSUED')
    <div class="bg-white rounded-2xl border-2 border-indigo-400 shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Perakuan Penerimaan Stok (Pemohon / Penerima)</h3>
                <p class="text-xs text-slate-500">Stok telah dikeluarkan oleh stor. Sila sahkan penerimaan fizikal barangan.</p>
            </div>
            <span class="px-2.5 py-1 rounded bg-indigo-100 text-indigo-900 text-xs font-bold">Penerima Stok</span>
        </div>

        <form method="POST" action="{{ route('requests.confirm', $stockRequest) }}" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Pengesahan Penerimaan</label>
                <input type="text" name="recipient_notes" value="Telah disemak dan diterima dalam keadaan lengkap dan sempurna." required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white rounded-lg font-bold text-xs shadow transition">
                    Sahkan Penerimaan Stok (Tutup Pesanan)
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Items Listing Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-900 text-white px-6 py-3 flex justify-between items-center">
            <h3 class="text-sm font-bold tracking-wide">SENARAI ITEM PESANAN & STATUS PENGELUARAN</h3>
            <span class="text-xs text-slate-400">{{ $stockRequest->items->count() }} Item Didaftarkan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-2.5 px-3">Item Stok</th>
                        <th class="py-2.5 px-3 text-center">Kuantiti Dimohon</th>
                        <th class="py-2.5 px-3 text-center">Kuantiti Diluluskan</th>
                        <th class="py-2.5 px-3 text-center">Kuantiti Dikeluarkan</th>
                        <th class="py-2.5 px-3">Lokasi Simpanan</th>
                        <th class="py-2.5 px-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stockRequest->items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-3">
                            <a href="{{ route('stock-register.show', $item->stockItem) }}" class="font-bold text-blue-700 hover:underline">
                                {{ $item->stockItem->description }}
                            </a>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->stockItem->stock_code }}</div>
                        </td>
                        <td class="py-2.5 px-3 text-center font-mono font-bold text-slate-800">
                            {{ number_format($item->requested_quantity, 0) }} {{ $item->stockItem->uom->code }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-mono font-bold text-blue-900">
                            {{ number_format($item->approved_quantity, 0) }} {{ $item->stockItem->uom->code }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-900">
                            {{ number_format($item->issued_quantity, 0) }} {{ $item->stockItem->uom->code }}
                        </td>
                        <td class="py-2.5 px-3 font-mono text-slate-600">
                            {{ $item->stockItem->defaultLocation->full_code ?? 'Lokasi Am' }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-500">
                            {{ $item->remarks ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
