@extends('layouts.app')

@section('title', 'Dashboard Pengurusan Stor')
@section('page_title', 'Dashboard Eksekutif & Operasi Stor')
@section('page_description', 'Pemantauan masa nyata inventori, pergerakan stok, dan petunjuk prestasi utama (KPI) TPS AM 6')

@section('page_actions')
    @if(auth()->user()->isPemohon())
        <a href="{{ route('requests.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>➕</span>
            <span>Pesanan Stok Baru (KEW.PS-8)</span>
        </a>
        <a href="{{ route('stock.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>📦</span>
            <span>Katalog Stok ASM</span>
        </a>
    @else
        <a href="{{ route('requests.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>+</span>
            <span>Pesanan Stok Baru (KEW.PS-8)</span>
        </a>
        @if(auth()->user()->hasRole(['admin', 'pegawai_penerima', 'pegawai_stor']))
        <a href="{{ route('receiving.create') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
            <span>📥</span>
            <span>Terima Barang (KEW.PS-1)</span>
        </a>
        @endif
    @endif
@endsection

@section('content')
@if(auth()->user()->isPemohon())
    <!-- Dedicated Staff / Pemohon Dashboard -->
    <div class="space-y-6">
        <!-- Welcome Hero Banner -->
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-8 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-200 border border-blue-400/30">
                    <span>👋</span>
                    <span>Selamat Datang, {{ auth()->user()->name }}</span>
                    <span>•</span>
                    <span>{{ auth()->user()->department ?? 'Akademi Sains Malaysia' }}</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight">Portal Pesanan & Bekalan Stok ASM</h2>
                <p class="text-xs text-blue-200 max-w-2xl leading-relaxed">
                    Kemukakan permohonan stok barangan guna habis dan alat tulis pejabat melalui borang rasmi <strong>KEW.PS-8</strong>, pantau status kelulusan semasa, dan semak ketersediaan item dalam katalog inventori.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
                <a href="{{ route('requests.create') }}" class="px-5 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center space-x-2">
                    <span>➕</span>
                    <span>Mohon Stok (KEW.PS-8)</span>
                </a>
                <a href="{{ route('stock.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold border border-white/20 transition flex items-center justify-center space-x-2">
                    <span>📦</span>
                    <span>Lihat Katalog Stok</span>
                </a>
            </div>
        </div>

        <!-- 4 Stat Summary Cards for Pemohon -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase">Jumlah Permohonan</span>
                    <span class="p-2 rounded-lg bg-blue-50 text-blue-700 text-base">📝</span>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-2">{{ $myTotalRequests }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Sepanjang perkhidmatan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-600 uppercase">Menunggu Kelulusan</span>
                    <span class="p-2 rounded-lg bg-amber-50 text-amber-600 text-base">⏳</span>
                </div>
                <div class="text-2xl font-black text-amber-600 mt-2">{{ $myPendingApproval }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Tindakan Pegawai Pelulus</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600 uppercase">Diluluskan / Sedia</span>
                    <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600 text-base">✅</span>
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-2">{{ $myApproved }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Tindakan Pegawai Stor</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-600 uppercase">Selesai / Diterima</span>
                    <span class="p-2 rounded-lg bg-indigo-50 text-indigo-600 text-base">📦</span>
                </div>
                <div class="text-2xl font-black text-indigo-600 mt-2">{{ $myCompleted }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Stok telah diserahkan</div>
            </div>
        </div>

        <!-- Recent Requests by this Pemohon -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Pesanan Stok Terkini Anda</h3>
                    <p class="text-xs text-slate-500">Status rekod permohonan terkini yang telah dihantar ke stor</p>
                </div>
                <a href="{{ route('requests.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center space-x-1">
                    <span>Lihat Semua Permohonan</span>
                    <span>→</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/75 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                            <th class="py-3 px-4">No. Pesanan</th>
                            <th class="py-3 px-4">Tarikh</th>
                            <th class="py-3 px-4">Stor Pembekal</th>
                            <th class="py-3 px-4">Bil. Item</th>
                            <th class="py-3 px-4">Keutamaan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($myRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-700">
                                <a href="{{ route('requests.show', $req) }}" class="hover:underline">
                                    {{ $req->request_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $req->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-medium">
                                {{ $req->store->name ?? 'Stor Utama' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $req->items->count() }} jenis item
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $req->priority === 'URGENT' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $req->priority }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $badge = match($req->status) {
                                        'SUBMITTED' => 'bg-amber-100 text-amber-800 border-amber-300',
                                        'APPROVED' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'ISSUED' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'COMPLETED' => 'bg-slate-100 text-slate-800 border-slate-300',
                                        'REJECTED' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default => 'bg-slate-100 text-slate-800 border-slate-300',
                                    };
                                    $statusLabel = match($req->status) {
                                        'SUBMITTED' => 'Menunggu Kelulusan',
                                        'APPROVED' => 'Diluluskan (Menunggu Bekalan)',
                                        'ISSUED' => 'Sedia Diambil / Dikeluarkan',
                                        'COMPLETED' => 'Selesai Diterima',
                                        'REJECTED' => 'Ditolak',
                                        default => $req->status,
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('requests.show', $req) }}" class="px-3 py-1 rounded bg-slate-100 hover:bg-blue-50 text-blue-600 font-semibold text-[11px] transition">
                                    Butiran
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <div>Anda belum membuat sebarang permohonan pesanan stok lagi.</div>
                                <a href="{{ route('requests.create') }}" class="inline-block mt-2 text-blue-600 hover:underline font-bold">
                                    + Buat Permohonan Pertama Sekarang (KEW.PS-8)
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    {{-- Full Operations / Executive Dashboard for Stor/Pelulus/Admin --}}
    <div class="space-y-6">

    <!-- Operational Pending Action Alert Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
        <a href="{{ route('receiving.index', ['status' => 'DRAFT']) }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Penerimaan Pending</div>
            <div class="text-xl font-bold {{ $pendingReceivings > 0 ? 'text-amber-600' : 'text-slate-700' }} mt-1">{{ $pendingReceivings }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">KEW.PS-1 BTB</div>
        </a>

        <a href="{{ route('requests.index', ['status' => 'SUBMITTED']) }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Kelulusan Pending</div>
            <div class="text-xl font-bold {{ $pendingRequests > 0 ? 'text-blue-600' : 'text-slate-700' }} mt-1">{{ $pendingRequests }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pegawai Pelulus</div>
        </a>

        <a href="{{ route('requests.index', ['status' => 'APPROVED']) }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Pengeluaran Pending</div>
            <div class="text-xl font-bold {{ $pendingIssues > 0 ? 'text-emerald-600' : 'text-slate-700' }} mt-1">{{ $pendingIssues }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pegawai Stor</div>
        </a>

        <a href="{{ route('transfers.index', ['status' => 'DISPATCHED']) }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Pindahan Pending</div>
            <div class="text-xl font-bold {{ $pendingTransfers > 0 ? 'text-indigo-600' : 'text-slate-700' }} mt-1">{{ $pendingTransfers }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">KEW.PS-17</div>
        </a>

        <a href="{{ route('verification.index') }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Verifikasi Pending</div>
            <div class="text-xl font-bold {{ $pendingVerifications > 0 ? 'text-purple-600' : 'text-slate-700' }} mt-1">{{ $pendingVerifications }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">KEW.PS-11/12</div>
        </a>

        <a href="{{ route('disposal.index') }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Pelupusan Pending</div>
            <div class="text-xl font-bold {{ $pendingDisposals > 0 ? 'text-rose-600' : 'text-slate-700' }} mt-1">{{ $pendingDisposals }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">KEW.PS-20</div>
        </a>

        <a href="{{ route('loss.index') }}" class="p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition">
            <div class="text-[11px] font-medium text-slate-500 uppercase">Hapus Kira Pending</div>
            <div class="text-xl font-bold {{ $pendingLossCases > 0 ? 'text-red-700' : 'text-slate-700' }} mt-1">{{ $pendingLossCases }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">KEW.PS-32..35</div>
        </a>
    </div>

    <!-- Main Stock Valuation and Level KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Stock Value -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Nilai Pegangan Stok</span>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">RM {{ number_format($totalStockValue, 2) }}</h3>
                    <div class="text-xs text-slate-500 mt-1">Daripada {{ $totalStockItems }} jenis item stok</div>
                </div>
                <div class="p-2.5 bg-blue-50 text-blue-700 rounded-xl font-bold text-lg">💰</div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between text-xs">
                <span class="text-slate-600">Kumpulan A (30%): <strong class="text-slate-900">RM {{ number_format($groupAValue, 2) }}</strong></span>
                <span class="text-slate-600">Kumpulan B: <strong class="text-slate-900">RM {{ number_format($groupBValue, 2) }}</strong></span>
            </div>
        </div>

        <!-- Kadar Pusingan Stok (TPS AM 6.6 / KEW.PS-14 Target >= 4.0) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kadar Pusingan Stok (KEW.PS-14)</span>
                    <div class="flex items-baseline space-x-2 mt-1">
                        <h3 class="text-2xl font-black text-slate-900">{{ number_format($turnoverData['turnover_rate'], 2) }}</h3>
                        <span class="text-xs font-medium text-slate-500">kali setahun</span>
                    </div>
                    <div class="text-xs mt-1">
                        @if($turnoverData['target_achieved'])
                            <span class="text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded">✓ Menepati Sasaran TPS (≥ 4.0)</span>
                        @else
                            <span class="text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded">⚠️ Bawah Sasaran TPS (≥ 4.0)</span>
                        @endif
                    </div>
                </div>
                <div class="p-2.5 bg-purple-50 text-purple-700 rounded-xl font-bold text-lg">📈</div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between text-xs text-slate-500">
                <span>Pengeluaran Tahunan: <strong>RM {{ number_format($turnoverData['annual_issue_total'], 2) }}</strong></span>
                <a href="{{ route('reports.kew-ps-14') }}" class="text-blue-600 hover:underline font-medium">Borang PS-14 →</a>
            </div>
        </div>

        <!-- Service Level / Tahap Perkhidmatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tahap Perkhidmatan (Service Level)</span>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($serviceLevel, 1) }}%</h3>
                    <div class="text-xs text-slate-500 mt-1">Peratus pesanan berjaya dipenuhi</div>
                </div>
                <div class="p-2.5 bg-emerald-50 text-emerald-700 rounded-xl font-bold text-lg">🎯</div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between text-xs text-slate-500">
                <span>Stok Aktif: <strong>{{ $activeStockItems }}</strong></span>
                <span>Tidak Aktif: <strong>{{ $inactiveStockItems }}</strong></span>
            </div>
        </div>

        <!-- Expiry & Condition Monitoring (AM 6.3 / KEW.PS-6) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemantauan Tarikh Luput</span>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-xl font-black text-rose-700">{{ $expiredCount }} Telah Luput</span>
                    </div>
                    <div class="text-xs text-amber-700 font-medium mt-1">{{ $nearExpiryCount }} kelompok hampir luput (≤ 60 hari)</div>
                </div>
                <div class="p-2.5 bg-rose-50 text-rose-700 rounded-xl font-bold text-lg">⏳</div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between text-xs text-slate-500">
                <span>Stok Rosak: <strong>{{ number_format($damagedQuantity, 0) }}</strong></span>
                <a href="{{ route('stock.expiry') }}" class="text-blue-600 hover:underline font-medium">Lihat KEW.PS-6 →</a>
            </div>
        </div>
    </div>

    <!-- Stock Level Alerts Row (3-2-1 Month Rule: Min / Reorder / Max) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-wrap justify-between items-center mb-4">
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Status Amaran Paras Stok (Tatacara TPS AM 6.4)</h4>
                <p class="text-xs text-slate-500">Paras Maksimum = 3 Bulan | Paras Menokok = 2 Bulan | Paras Minimum = 1 Bulan</p>
            </div>
            <a href="{{ route('reports.reorder') }}" class="text-xs text-blue-700 hover:underline font-semibold">Laporan Pesanan Semula Lengkap →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <a href="{{ route('stock.index', ['level_alert' => 'no_stock']) }}" class="p-3 bg-red-50 border border-red-200 rounded-xl text-center hover:shadow-md transition">
                <div class="text-2xl font-black text-red-700">{{ $levelSummaries['out_of_stock'] }}</div>
                <div class="text-xs font-bold text-red-800 mt-1">🔴 Tiada Stok</div>
                <div class="text-[10px] text-red-600">Kuantiti = 0</div>
            </a>

            <a href="{{ route('stock.index', ['level_alert' => 'below_min']) }}" class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-center hover:shadow-md transition">
                <div class="text-2xl font-black text-rose-700">{{ $levelSummaries['below_min'] }}</div>
                <div class="text-xs font-bold text-rose-800 mt-1">🔻 Bawah Minimum</div>
                <div class="text-[10px] text-rose-600">&lt; 1 Bulan Penggunaan</div>
            </a>

            <a href="{{ route('stock.index', ['level_alert' => 'reorder']) }}" class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-center hover:shadow-md transition">
                <div class="text-2xl font-black text-amber-700">{{ $levelSummaries['reorder_required'] }}</div>
                <div class="text-xs font-bold text-amber-800 mt-1">⚠️ Perlu Ditokok</div>
                <div class="text-[10px] text-amber-600">≤ 2 Bulan Penggunaan</div>
            </a>

            <a href="{{ route('stock.index') }}" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center hover:shadow-md transition">
                <div class="text-2xl font-black text-emerald-700">{{ $levelSummaries['normal'] }}</div>
                <div class="text-xs font-bold text-emerald-800 mt-1">🟢 Paras Normal</div>
                <div class="text-[10px] text-emerald-600">Paras Mencukupi</div>
            </a>

            <a href="{{ route('stock.index', ['level_alert' => 'above_max']) }}" class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-center hover:shadow-md transition">
                <div class="text-2xl font-black text-purple-700">{{ $levelSummaries['above_max'] }}</div>
                <div class="text-xs font-bold text-purple-800 mt-1">🟣 Lebih Maksimum</div>
                <div class="text-[10px] text-purple-600">&gt; 3 Bulan Penggunaan</div>
            </a>
        </div>
    </div>

    <!-- Recent Transactions & Ledger Activity -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Pergerakan Stok Terkini (Daftar Stok Bahagian B)</h4>
                <p class="text-xs text-slate-500">Transaksi masuk/keluar direkod secara automatik ke dalam Daftar Stok KEW.PS-3</p>
            </div>
            <a href="{{ route('stock-register.index') }}" class="text-xs font-bold text-blue-700 hover:underline">Semua Daftar Stok KEW.PS-3 →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Tarikh</th>
                        <th class="py-3 px-4">No. Rujukan / Borang</th>
                        <th class="py-3 px-4">Jenis Transaksi</th>
                        <th class="py-3 px-4">Stor / Pihak Terlibat</th>
                        <th class="py-3 px-4">Perihal Item Stok</th>
                        <th class="py-3 px-4 text-center">Kuantiti</th>
                        <th class="py-3 px-4 text-right">Nilai (RM)</th>
                        <th class="py-3 px-4">Pegawai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $txn)
                        @foreach($txn->items as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 whitespace-nowrap font-medium text-slate-900">
                                <div>{{ $txn->reference_number }}</div>
                                <span class="text-[10px] font-semibold text-slate-400">{{ $txn->kew_ps_type ?? 'TXN' }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($item->movement_type === 'IN')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        + MASUK ({{ $txn->transaction_type }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        - KELUAR ({{ $txn->transaction_type }})
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $txn->party_name ?? $txn->store->name }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                <a href="{{ route('stock-register.show', $item->stockItem) }}" class="text-blue-600 hover:underline">
                                    {{ $item->stockItem->description }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->stockItem->stock_code }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-bold {{ $item->movement_type === 'IN' ? 'text-emerald-700' : 'text-slate-800' }}">
                                {{ $item->movement_type === 'IN' ? '+' : '-' }}{{ number_format($item->quantity, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-900">
                                RM {{ number_format($item->total_price, 2) }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $txn->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-400">Tiada rekod transaksi ditemui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endif
@endsection
