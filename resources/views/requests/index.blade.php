@extends('layouts.app')

@section('title', 'Permohonan & Pengeluaran Stok')
@section('page_title', 'Permohonan & Pengeluaran Stok (AM 6.5)')
@section('page_description', 'Pengurusan pesanan stok kakitangan (KEW.PS-8) dan pesanan antara stor (KEW.PS-7)')

@section('page_actions')
    <a href="{{ route('requests.create', ['type' => 'KEW.PS-8']) }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>+</span>
        <span>Pesanan Kakitangan (KEW.PS-8)</span>
    </a>
    <a href="{{ route('requests.create', ['type' => 'KEW.PS-7']) }}" class="px-3.5 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-semibold shadow transition flex items-center space-x-1.5">
        <span>🔄</span>
        <span>Pesanan Antara Stor (KEW.PS-7)</span>
    </a>
@endsection

@section('content')
<div class="space-y-4">

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-3 text-xs">
        <form method="GET" action="{{ route('requests.index') }}" class="flex items-center space-x-2 w-full sm:w-auto">
            <select name="form_type" class="px-3 py-1.5 border border-slate-300 rounded-lg">
                <option value="">Semua Jenis Borang</option>
                <option value="KEW.PS-8" {{ request('form_type') === 'KEW.PS-8' ? 'selected' : '' }}>KEW.PS-8 (Pesanan Kakitangan)</option>
                <option value="KEW.PS-7" {{ request('form_type') === 'KEW.PS-7' ? 'selected' : '' }}>KEW.PS-7 (Antara Stor)</option>
            </select>
            <select name="status" class="px-3 py-1.5 border border-slate-300 rounded-lg">
                <option value="">Semua Status</option>
                <option value="SUBMITTED" {{ request('status') === 'SUBMITTED' ? 'selected' : '' }}>Menunggu Kelulusan (Pelulus)</option>
                <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Diluluskan / Sedia Keluar (Stor)</option>
                <option value="ISSUED" {{ request('status') === 'ISSUED' ? 'selected' : '' }}>Telah Dikeluarkan</option>
                <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai / Diperakui</option>
                <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-blue-700 text-white rounded-lg font-semibold">Tapis</button>
            @if(request('form_type') || request('status'))
                <a href="{{ route('requests.index') }}" class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded-lg">Reset</a>
            @endif
        </form>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">No. Permohonan</th>
                        <th class="py-3 px-4">Jenis Borang</th>
                        <th class="py-3 px-4">Pemohon / Cawangan</th>
                        <th class="py-3 px-4">Tujuan / Kegunaan</th>
                        <th class="py-3 px-4 text-center">Keutamaan</th>
                        <th class="py-3 px-4 text-center">Bil. Item</th>
                        <th class="py-3 px-4 text-center">Status Aliran Kerja</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 whitespace-nowrap font-mono font-bold text-blue-700">
                            <a href="{{ route('requests.show', $req) }}" class="hover:underline">
                                {{ $req->request_number }}
                            </a>
                            <div class="text-[10px] text-slate-400 font-sans">{{ $req->created_at->format('d/m/Y h:i A') }}</div>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $req->form_type === 'KEW.PS-8' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $req->form_type }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $req->requester->name }}
                            <div class="text-[10px] text-slate-400">{{ $req->department }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-700 max-w-xs truncate">
                            {{ $req->purpose }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $req->priority === 'URGENT' ? 'bg-rose-100 text-rose-800 animate-pulse' : 'bg-slate-100 text-slate-600' }}">
                                {{ $req->priority }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">
                                {{ $req->items->count() }} item
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($req->status === 'SUBMITTED')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    ⏳ Menunggu Kelulusan
                                </span>
                            @elseif($req->status === 'APPROVED' || $req->status === 'PARTIALLY_APPROVED')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                    ✓ Diluluskan (Sedia Keluar)
                                </span>
                            @elseif($req->status === 'ISSUED')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                    📦 Dikeluarkan (Menunggu Perakuan)
                                </span>
                            @elseif($req->status === 'COMPLETED')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    ✓ Selesai & Diperakui
                                </span>
                            @elseif($req->status === 'REJECTED')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    ✗ Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-1.5">
                                <a href="{{ route('requests.show', $req) }}" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-medium text-[11px] shadow-sm">
                                    Lihat / Tindakan
                                </a>
                                @if($req->form_type === 'KEW.PS-8')
                                    <a href="{{ route('requests.print-ps8', $req) }}" target="_blank" title="Cetak KEW.PS-8" class="p-1 text-blue-700 hover:bg-slate-100 rounded">
                                        🖨️
                                    </a>
                                @else
                                    <a href="{{ route('requests.print-ps7', $req) }}" target="_blank" title="Cetak KEW.PS-7" class="p-1 text-purple-700 hover:bg-slate-100 rounded">
                                        🖨️
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">
                            Tiada permohonan stok ditemui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
