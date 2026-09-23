@extends('layouts.app')

@section('title', 'Borang Pelarasan Stok (KEW.PS-15)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.6</span>
                <span class="text-xs text-slate-500">Pelarasan Stok (KEW.PS-15)</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Permohonan Pelarasan Stok (KEW.PS-15)</h1>
            <p class="text-sm text-slate-600">Pelarasan bagi selisih kuantiti fizikal dan rekod stok berpunca daripada Verifikasi {{ $verification->verification_number }}.</p>
        </div>
        <a href="{{ route('verification.show', $verification) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
        <div class="font-bold mb-1">Terdapat ralat:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('verification.adjustments.store', $verification) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600 mr-2"></span>
                Maklumat Asas Pelarasan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stor Terlibat</label>
                    <input type="text" disabled value="{{ $verification->store->name ?? '-' }}" class="w-full text-sm bg-slate-50 border-slate-200 rounded-lg text-slate-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Punca / Sebab Pelarasan <span class="text-rose-500">*</span></label>
                    <select name="reason" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="APPROVED_VERIFICATION">Hasil Dapatan Verifikasi Stor Tahunan Diluluskan</option>
                        <option value="RECORDING_ERROR">Kesilapan Pengeluaran / Kemasukan Terimaan Rekod</option>
                        <option value="PHYSICAL_DISCREPANCY">Selisih Kiraan Fizikal Semasa Pemeriksaan</option>
                        <option value="DAMAGE">Stok Rosak / Cacat Semasa Penyimpanan</option>
                        <option value="OTHER">Lain-lain Sebab Yang Diluluskan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Discrepancy Items -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                Senarai Item Selisih Untuk Diselaraskan
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kod & Perihal Stok</th>
                            <th class="py-3 px-4 text-center">Baki Semasa</th>
                            <th class="py-3 px-4 text-center">Kiraan Fizikal</th>
                            <th class="py-3 px-4 w-44 text-center">Kuantiti Pelarasan (+/-)</th>
                            <th class="py-3 px-4">Catatan Justifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($discrepancies as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                                <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center text-slate-600 font-medium">
                                {{ number_format($item->system_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ number_format($item->physical_quantity) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="number" step="0.01" 
                                       name="items[{{ $item->stock_item_id }}][adjustment_quantity]" 
                                       value="{{ $item->variance_quantity }}" required 
                                       class="w-full text-sm font-bold text-center border-slate-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 {{ $item->variance_quantity >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            </td>
                            <td class="py-3 px-4">
                                <input type="text" placeholder="Contoh: Kesilapan rekod kemasukan..." 
                                       name="items[{{ $item->stock_item_id }}][reason_detail]" 
                                       value="{{ $item->remarks }}" 
                                       class="w-full text-xs border-slate-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">
                                Tiada item selisih ditemui dalam verifikasi ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('verification.show', $verification) }}" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" onclick="return confirm('Hantar permohonan pelarasan stok ini untuk kelulusan Ketua Jabatan (KEW.PS-16)?')" class="px-6 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-bold shadow transition">
                Hantar Laporan Pelarasan (KEW.PS-15)
            </button>
        </div>
    </form>
</div>
@endsection
