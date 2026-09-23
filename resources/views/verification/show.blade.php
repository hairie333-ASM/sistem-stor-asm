@extends('layouts.app')

@section('title', 'Verifikasi Stor ' . $verification->verification_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.6</span>
                <span class="text-xs text-slate-500">Pemeriksaan Fizikal & Verifikasi</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $verification->verification_number }}</h1>
                @if($verification->status === 'SCHEDULED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800">Dijadualkan</span>
                @elseif($verification->status === 'IN_PROGRESS')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">Pemeriksaan Berjalan (Dibekukan)</span>
                @elseif($verification->status === 'COMPLETED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Kiraan Selesai (KEW.PS-12)</span>
                @elseif($verification->status === 'APPROVED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Diperakui (KEW.PS-13: {{ $verification->cert_number }})</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('verification.print-ps10', $verification) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition border border-slate-300">
                KEW.PS-10
            </a>
            <a href="{{ route('verification.print-ps11', $verification) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold transition border border-slate-300">
                KEW.PS-11
            </a>
            @if(in_array($verification->status, ['COMPLETED', 'APPROVED']))
            <a href="{{ route('verification.print-ps12', $verification) }}" target="_blank" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 rounded text-xs font-semibold transition border border-purple-200">
                KEW.PS-12
            </a>
            @endif
            @if($verification->status === 'APPROVED')
            <a href="{{ route('verification.print-ps13', $verification) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition shadow-sm">
                Sijil KEW.PS-13
            </a>
            @endif
            <a href="{{ route('verification.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition ml-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Metadata Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Stor & Penjadualan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Stor:</span> <span class="font-bold text-slate-800">{{ $verification->store->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Tahun:</span> <span class="font-bold text-slate-700">{{ $verification->year }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Jadual:</span> <span class="text-slate-700">{{ $verification->scheduled_date }}</span></div>
                <div><span class="text-slate-500 text-xs">Surat Lantikan:</span> <span class="font-mono text-xs text-slate-700">{{ $verification->appointment_letter_ref }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Pemverifikasi Stor (Bebas)</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Pegawai 1:</span> <span class="font-semibold text-slate-800">{{ $verification->verifier1->name ?? '-' }}</span> <span class="text-xs text-slate-400">({{ $verification->verifier1->department ?? '' }})</span></div>
                <div><span class="text-slate-500 text-xs">Pegawai 2:</span> <span class="font-semibold text-slate-800">{{ $verification->verifier2->name ?? '-' }}</span> <span class="text-xs text-slate-400">({{ $verification->verifier2->department ?? '' }})</span></div>
                <div><span class="text-slate-500 text-xs">Status Kiraan:</span> 
                    <span class="font-bold {{ $verification->status === 'COMPLETED' || $verification->status === 'APPROVED' ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $verification->status === 'COMPLETED' || $verification->status === 'APPROVED' ? 'Selesai 100%' : 'Belum Selesai' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Ketua Jabatan / Kelulusan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Pegawai Memperakui:</span> <span class="font-semibold text-slate-800">{{ $verification->approvalOfficer->name ?? 'Belum Diperakui' }}</span></div>
                <div><span class="text-slate-500 text-xs">No. Sijil KEW.PS-13:</span> <span class="font-mono font-bold text-emerald-800">{{ $verification->cert_number ?: 'Belum dikeluarkan' }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Perakuan:</span> <span class="text-slate-700">{{ $verification->approved_at ? \Carbon\Carbon::parse($verification->approved_at)->format('d/m/Y') : '-' }}</span></div>
            </div>
        </div>
    </div>

    <!-- 1. SCHEDULED: Action to Start -->
    @if($verification->status === 'SCHEDULED')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center space-y-4">
        <div class="max-w-xl mx-auto">
            <h3 class="text-lg font-bold text-blue-900">Sedia Untuk Memulakan Verifikasi Stor?</h3>
            <p class="text-sm text-blue-800 mt-1">
                Memulakan pemeriksaan akan membekukan (freeze) transaksi stor berkenaan serta mengambil salinan imbangan baki sistem terkini untuk disemak secara fizikal oleh pegawai pemverifikasi.
            </p>
        </div>
        <form action="{{ route('verification.start', $verification) }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Adakah anda pasti mahu memulakan verifikasi fizikal dan membekukan transaksi stor?')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                Mulakan Verifikasi & Bekukan Stor Sekarang
            </button>
        </form>
    </div>

    <!-- 2. IN_PROGRESS: Form to Record Counts -->
    @elseif($verification->status === 'IN_PROGRESS')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-amber-50 flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <h3 class="text-base font-bold text-amber-900">Borang Kiraan Fizikal Pemverifikasi Stor (KEW.PS-12)</h3>
                </div>
                <p class="text-xs text-amber-800 mt-0.5">Sila masukkan kuantiti fizikal sebenar yang dijumpai di rak stor bagi setiap item.</p>
            </div>
            <span class="px-3 py-1 bg-amber-200 text-amber-900 rounded-full text-xs font-bold">Stor Sedang Dibekukan</span>
        </div>

        <form action="{{ route('verification.record-counts', $verification) }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod Stok & Perihal</th>
                            <th class="py-3 px-4 text-center">Baki Sistem (Buku)</th>
                            <th class="py-3 px-4 w-40 text-center">Kiraan Fizikal</th>
                            <th class="py-3 px-4 w-44">Keadaan Stok</th>
                            <th class="py-3 px-4">Catatan / Sebab Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($verification->items as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                                <div class="text-xs text-slate-400">Harga Seunit: RM {{ number_format($item->unit_price, 2) }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ number_format($item->system_quantity) }} {{ $item->stockItem->uom->code ?? '' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="number" step="0.01" min="0" 
                                       name="items[{{ $item->id }}][physical_quantity]" 
                                       value="{{ $item->physical_quantity }}" required 
                                       class="w-full text-sm font-bold text-center border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-blue-900">
                            </td>
                            <td class="py-3 px-4">
                                <select name="items[{{ $item->id }}][condition_status]" required class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="BAIK" {{ $item->condition_status === 'BAIK' ? 'selected' : '' }}>Baik & Aktif</option>
                                    <option value="ROSAK" {{ $item->condition_status === 'ROSAK' ? 'selected' : '' }}>Rosak</option>
                                    <option value="USANG" {{ $item->condition_status === 'USANG' ? 'selected' : '' }}>Usang / Lapuk</option>
                                    <option value="TIDAK_BERGERAK" {{ $item->condition_status === 'TIDAK_BERGERAK' ? 'selected' : '' }}>Tidak Bergerak (>1 Tahun)</option>
                                </select>
                            </td>
                            <td class="py-3 px-4">
                                <input type="text" placeholder="Catatan fizikal..." name="items[{{ $item->id }}][remarks]" value="{{ $item->remarks }}" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan Dapatan & Tindakan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ringkasan Dapatan Pemeriksaan</label>
                    <textarea name="findings_summary" rows="3" class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Keseluruhan pengurusan stor berada dalam tahap memuaskan, kedudukan kad petak selari.">{{ $verification->findings_summary }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Syor / Tindakan Pembetulan</label>
                    <textarea name="corrective_actions" rows="3" class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Memohon kelulusan pelarasan bagi lebihan 2 unit pen dan pelupusan 1 unit item usang.">{{ $verification->corrective_actions }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="submit" onclick="return confirm('Simpan kiraan fizikal dan lengkapkan Laporan KEW.PS-12?')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                    Simpan Kiraan & Jana Laporan KEW.PS-12
                </button>
            </div>
        </form>
    </div>

    <!-- 3. COMPLETED or APPROVED: Variance & Results Table -->
    @else
    <div class="space-y-6">
        <!-- Results Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Dapatan Kiraan Fizikal & Analisis Selisih (KEW.PS-12)</h3>
                    <p class="text-xs text-slate-500">Perbandingan kuantiti sistem lawan kuantiti fizikal sebenar.</p>
                </div>
                @php
                    $hasDiscrepancy = $verification->items->some(fn($i) => $i->variance_quantity != 0);
                @endphp
                @if($hasDiscrepancy && $verification->status === 'COMPLETED')
                <a href="{{ route('verification.adjustments.create', $verification) }}" class="inline-flex items-center px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-bold shadow transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mohon Pelarasan Stok (KEW.PS-15)
                </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod & Perihal Stok</th>
                            <th class="py-3 px-4 text-center">Baki Sistem</th>
                            <th class="py-3 px-4 text-center">Kiraan Fizikal</th>
                            <th class="py-3 px-4 text-center">Lebihan (+)</th>
                            <th class="py-3 px-4 text-center">Kurangan (-)</th>
                            <th class="py-3 px-4 text-right">Nilai Selisih (RM)</th>
                            <th class="py-3 px-4">Keadaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($verification->items as $item)
                        <tr class="{{ $item->variance_quantity != 0 ? 'bg-amber-50/50' : '' }}">
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center text-slate-600">
                                {{ number_format($item->system_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ number_format($item->physical_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold {{ $item->surplus_quantity > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $item->surplus_quantity > 0 ? '+' . number_format($item->surplus_quantity) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold {{ $item->shortage_quantity > 0 ? 'text-rose-700' : 'text-slate-400' }}">
                                {{ $item->shortage_quantity > 0 ? '-' . number_format($item->shortage_quantity) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold {{ $item->variance_value != 0 ? ($item->variance_value > 0 ? 'text-emerald-700' : 'text-rose-700') : 'text-slate-400' }}">
                                {{ number_format($item->variance_value, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $item->condition_status === 'BAIK' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $item->condition_status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approval Box by Ketua Jabatan -->
        @if($verification->status === 'COMPLETED')
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h4 class="text-base font-bold text-emerald-900">Tindakan Ketua Jabatan: Perakuan & Pengeluaran Sijil</h4>
                <p class="text-xs text-emerald-800 mt-1">Perakui laporan verifikasi fizikal KEW.PS-12 dan keluarkan Sijil Verifikasi Stor (KEW.PS-13).</p>
            </div>
            @if(auth()->user() && auth()->user()->hasRole(['ketua_jabatan', 'admin']))
            <form action="{{ route('verification.approve', $verification) }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Perakui laporan dan keluarkan Sijil Verifikasi Stor KEW.PS-13?')" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow transition">
                    Perakui & Keluarkan KEW.PS-13
                </button>
            </form>
            @else
            <span class="text-xs font-semibold text-emerald-700 bg-white px-3 py-1.5 rounded-lg border border-emerald-300">Menunggu Tindakan Ketua Jabatan</span>
            @endif
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
