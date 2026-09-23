@extends('layouts.app')

@section('title', 'Pindahan Stok ' . $transfer->transfer_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.8</span>
                <span class="text-xs text-slate-500">Borang Pindahan Stok (KEW.PS-17)</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $transfer->transfer_number }}</h1>
                @if($transfer->status === 'SUBMITTED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Kelulusan</span>
                @elseif($transfer->status === 'APPROVED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Diluluskan (Sedia Dihantar)</span>
                @elseif($transfer->status === 'DISPATCHED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Dihantar (Dalam Perjalanan)</span>
                @elseif($transfer->status === 'RECEIVED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai Diterima</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('transfers.print-ps17', $transfer) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak KEW.PS-17
            </a>
            <a href="{{ route('transfers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- 4-Stage Progress Tracker -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Stage 1 -->
            <div class="flex items-center space-x-3 p-3 rounded-lg {{ $transfer->status !== 'REJECTED' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-emerald-600 text-white">1</div>
                <div>
                    <div class="text-xs font-bold text-slate-800">1. Permohonan</div>
                    <div class="text-xs text-slate-500">{{ $transfer->requester->name ?? '-' }}</div>
                </div>
            </div>

            <!-- Stage 2 -->
            <div class="flex items-center space-x-3 p-3 rounded-lg {{ in_array($transfer->status, ['APPROVED', 'DISPATCHED', 'RECEIVED']) ? 'bg-emerald-50 border border-emerald-200' : ($transfer->status === 'SUBMITTED' ? 'bg-amber-50 border border-amber-200 animate-pulse' : 'bg-slate-50') }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ in_array($transfer->status, ['APPROVED', 'DISPATCHED', 'RECEIVED']) ? 'bg-emerald-600 text-white' : ($transfer->status === 'SUBMITTED' ? 'bg-amber-500 text-white' : 'bg-slate-300 text-slate-700') }}">2</div>
                <div>
                    <div class="text-xs font-bold text-slate-800">2. Kelulusan</div>
                    <div class="text-xs text-slate-500">{{ $transfer->approver ? $transfer->approver->name : 'Menunggu Kelulusan' }}</div>
                </div>
            </div>

            <!-- Stage 3 -->
            <div class="flex items-center space-x-3 p-3 rounded-lg {{ in_array($transfer->status, ['DISPATCHED', 'RECEIVED']) ? 'bg-emerald-50 border border-emerald-200' : ($transfer->status === 'APPROVED' ? 'bg-amber-50 border border-amber-200 animate-pulse' : 'bg-slate-50') }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ in_array($transfer->status, ['DISPATCHED', 'RECEIVED']) ? 'bg-emerald-600 text-white' : ($transfer->status === 'APPROVED' ? 'bg-amber-500 text-white' : 'bg-slate-300 text-slate-700') }}">3</div>
                <div>
                    <div class="text-xs font-bold text-slate-800">3. Penghantaran Keluar</div>
                    <div class="text-xs text-slate-500">{{ $transfer->sender ? $transfer->sender->name : 'Pegawai Stor Asal' }}</div>
                </div>
            </div>

            <!-- Stage 4 -->
            <div class="flex items-center space-x-3 p-3 rounded-lg {{ $transfer->status === 'RECEIVED' ? 'bg-emerald-50 border border-emerald-200' : ($transfer->status === 'DISPATCHED' ? 'bg-purple-50 border border-purple-200 animate-pulse' : 'bg-slate-50') }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $transfer->status === 'RECEIVED' ? 'bg-emerald-600 text-white' : ($transfer->status === 'DISPATCHED' ? 'bg-purple-600 text-white' : 'bg-slate-300 text-slate-700') }}">4</div>
                <div>
                    <div class="text-xs font-bold text-slate-800">4. Penerimaan Masuk</div>
                    <div class="text-xs text-slate-500">{{ $transfer->receiver ? $transfer->receiver->name : 'Pegawai Stor Penerima' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Metadata -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Maklumat Stor & Tujuan</h3>
            <div class="text-sm space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">Stor Asal (Pembekal):</span> <span class="font-bold text-slate-800">{{ $transfer->sourceStore->name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Stor Destinasi (Pemohon):</span> <span class="font-bold text-emerald-700">{{ $transfer->destinationStore->name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Tujuan Pindahan:</span> <span class="font-medium text-slate-800 text-right">{{ $transfer->purpose }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Tarikh Mohon:</span> <span class="text-slate-700">{{ $transfer->created_at->format('d/m/Y H:i') }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Status Integriti Transaksi</h3>
            <div class="text-sm space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">Pegawai Pemohon:</span> <span class="font-semibold text-slate-800">{{ $transfer->requester->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Pegawai Pelulus:</span> <span class="font-semibold text-slate-800">{{ $transfer->approver->name ?? 'Belum diluluskan' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Pegawai Penghantar:</span> <span class="font-semibold text-slate-800">{{ $transfer->sender->name ?? 'Belum dihantar' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Pegawai Penerima:</span> <span class="font-semibold text-slate-800">{{ $transfer->receiver->name ?? 'Belum diterima' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Items & Action Forms -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-800">Senarai Stok Pindahan (KEW.PS-17)</h3>
        </div>

        <!-- 1. Form for Approval (Ketua Jabatan / Pelulus) -->
        @if($transfer->status === 'SUBMITTED')
        <form action="{{ route('transfers.approve', $transfer) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod & Perihal Stok</th>
                            <th class="py-3 px-4 text-center">Baki Stor Asal</th>
                            <th class="py-3 px-4 text-center">Kuantiti Dimohon</th>
                            <th class="py-3 px-4 w-48 text-center">Kuantiti Diluluskan</th>
                            <th class="py-3 px-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transfer->items as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ number_format($item->stockItem->current_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-800">
                                {{ number_format($item->requested_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="number" step="0.01" min="0" max="{{ $item->requested_quantity }}" 
                                       name="items[{{ $item->id }}][approved_quantity]" 
                                       value="{{ $item->requested_quantity }}" required 
                                       class="w-full text-sm font-bold text-center border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $item->remarks ?: '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="submit" onclick="return confirm('Luluskan permohonan pindahan stok ini?')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                    Luluskan Pindahan Stok
                </button>
            </div>
        </form>

        <!-- 2. Form for Dispatch (Pegawai Stor Asal) -->
        @elseif($transfer->status === 'APPROVED')
        <div class="p-6 space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod & Perihal Stok</th>
                            <th class="py-3 px-4 text-center">Kuantiti Dimohon</th>
                            <th class="py-3 px-4 text-center">Kuantiti Diluluskan</th>
                            <th class="py-3 px-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transfer->items as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center text-slate-600">
                                {{ number_format($item->requested_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-700">
                                {{ number_format($item->approved_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $item->remarks ?: '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-900 text-sm flex items-center justify-between">
                <div>
                    <span class="font-bold">Tindakan Pegawai Stor Asal:</span>
                    <p class="text-xs text-blue-700 mt-0.5">Sahkan barangan telah dibungkus dan dikeluarkan dari premis stor pembekal.</p>
                </div>
                <form action="{{ route('transfers.dispatch', $transfer) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Keluarkan dan hantar stok ini sekarang? Baki stor pembekal akan ditolak secara automatik.')" class="px-5 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-bold shadow transition">
                        Sahkan Penghantaran Keluar
                    </button>
                </form>
            </div>
        </div>

        <!-- 3. Form for Receiving (Pegawai Stor Penerima) -->
        @elseif($transfer->status === 'DISPATCHED')
        <form action="{{ route('transfers.receive', $transfer) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod & Perihal Stok</th>
                            <th class="py-3 px-4 text-center">Kuantiti Diluluskan</th>
                            <th class="py-3 px-4 w-48 text-center">Kuantiti Diterima Fizikal</th>
                            <th class="py-3 px-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transfer->items as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ number_format($item->approved_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="number" step="0.01" min="0" max="{{ $item->approved_quantity }}" 
                                       name="items[{{ $item->id }}][received_quantity]" 
                                       value="{{ $item->approved_quantity }}" required 
                                       class="w-full text-sm font-bold text-center border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-emerald-800">
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
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Penerimaan Stor Destinasi</label>
                <input type="text" name="remarks" placeholder="Contoh: Diterima dalam keadaan baik dan disimpan pada rak B-02-01." class="w-full text-sm border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="submit" onclick="return confirm('Sahkan penerimaan fizikal stok ini? Baki stor destinasi akan ditambah secara automatik ke KEW.PS-3.')" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow transition">
                    Sahkan Penerimaan & Kemaskini KEW.PS-3 Destinasi
                </button>
            </div>
        </form>

        <!-- 4. Completed View (RECEIVED) -->
        @else
        <div class="overflow-x-auto p-4">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4">Kod & Perihal Stok</th>
                        <th class="py-3 px-4 text-center">Kuantiti Dimohon</th>
                        <th class="py-3 px-4 text-center">Kuantiti Diluluskan</th>
                        <th class="py-3 px-4 text-center">Kuantiti Diterima</th>
                        <th class="py-3 px-4">Status Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($transfer->items as $item)
                    <tr>
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                            <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-center text-slate-600">
                            {{ number_format($item->requested_quantity) }}
                        </td>
                        <td class="py-3 px-4 text-center text-slate-700">
                            {{ number_format($item->approved_quantity) }}
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-emerald-700">
                            {{ number_format($item->received_quantity) }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center text-xs font-semibold text-emerald-700">
                                <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Rekod Bersepadu Selesai
                            </span>
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
