@extends('layouts.app')

@section('title', 'Kes Kehilangan ' . $lossCase->case_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-rose-100 text-rose-800 border border-rose-300">TPS AM 6.10</span>
                <span class="text-xs text-slate-500">Kehilangan & Hapus Kira Stok</span>
            </div>
            <div class="flex items-center space-x-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $lossCase->case_number }}</h1>
                @if($lossCase->status === 'REPORTED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Laporan Awal (KEW.PS-32)</span>
                @elseif($lossCase->status === 'INVESTIGATING')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">Siasatan (KEW.PS-33)</span>
                @elseif($lossCase->status === 'SUBMITTED_FOR_WRITE_OFF')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Laporan Akhir (KEW.PS-34)</span>
                @elseif($lossCase->status === 'APPROVED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Hapus Kira Diluluskan (KEW.PS-35)</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Syor Surcaj Dikenakan</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('loss.print-ps32', $lossCase) }}" target="_blank" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 rounded text-xs font-semibold transition border border-rose-200">
                KEW.PS-32
            </a>
            @if(in_array($lossCase->status, ['INVESTIGATING', 'SUBMITTED_FOR_WRITE_OFF', 'APPROVED', 'SURCHARGE_RECOMMENDED']))
            <a href="{{ route('loss.print-ps33', $lossCase) }}" target="_blank" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded text-xs font-semibold transition border border-amber-200">
                KEW.PS-33
            </a>
            @endif
            @if(in_array($lossCase->status, ['SUBMITTED_FOR_WRITE_OFF', 'APPROVED', 'SURCHARGE_RECOMMENDED']))
            <a href="{{ route('loss.print-ps34', $lossCase) }}" target="_blank" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-800 rounded text-xs font-semibold transition border border-blue-200">
                KEW.PS-34
            </a>
            @endif
            @if(in_array($lossCase->status, ['APPROVED', 'SURCHARGE_RECOMMENDED']))
            <a href="{{ route('loss.print-ps35', $lossCase) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition shadow-sm">
                Sijil KEW.PS-35
            </a>
            <a href="{{ route('loss.print-ps36', $lossCase) }}" target="_blank" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 rounded text-xs font-semibold transition border border-purple-200">
                KEW.PS-36
            </a>
            @endif
            <a href="{{ route('loss.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition ml-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Metadata Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Maklumat Kes Kehilangan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Stor:</span> <span class="font-bold text-slate-800">{{ $lossCase->store->name ?? '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Kejadian:</span> <span class="text-slate-700">{{ $lossCase->incident_date }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Dikesan:</span> <span class="text-slate-700">{{ $lossCase->discovery_date }}</span></div>
                <div><span class="text-slate-500 text-xs">Jumlah Nilai Kehilangan:</span> <span class="font-bold font-mono text-rose-700">RM {{ number_format($lossCase->total_loss_value, 2) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Laporan Polis & Pencerobohan</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">No. Polis:</span> <span class="font-mono font-bold text-slate-800">{{ $lossCase->police_report_no ?: 'Tiada laporan polis' }}</span></div>
                <div><span class="text-slate-500 text-xs">Tarikh Polis:</span> <span class="text-slate-700">{{ $lossCase->police_report_date ?: '-' }}</span></div>
                <div><span class="text-slate-500 text-xs">Status Tindakan Polis:</span> <span class="text-slate-700">{{ $lossCase->police_action_status ?: '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Siasatan & Hapus Kira</h3>
            <div class="text-sm space-y-1.5">
                <div><span class="text-slate-500 text-xs">Jawatankuasa (KEW.PS-33):</span> <span class="font-mono text-xs text-slate-800">{{ $lossCase->investigation_committee_ref ?: 'Belum Dilantik' }}</span></div>
                <div><span class="text-slate-500 text-xs">Laporan Akhir (KEW.PS-34):</span> <span class="font-mono text-xs text-slate-800">{{ $lossCase->final_report_ref ?: 'Belum Dihantar' }}</span></div>
                <div><span class="text-slate-500 text-xs">Sijil Hapus Kira:</span> <span class="font-mono font-bold text-emerald-800">{{ $lossCase->write_off_cert_number ?: 'Menunggu Kelulusan' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Description & Items -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Perihal Ringkas Kejadian</h3>
        <p class="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100 italic">
            "{{ $lossCase->description }}"
        </p>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-800">Senarai Stok Terlibat Dalam Kehilangan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4">Kod & Perihal Stok</th>
                        <th class="py-3 px-4 text-center">Kuantiti Hilang</th>
                        <th class="py-3 px-4 text-right">Harga Seunit (RM)</th>
                        <th class="py-3 px-4 text-right">Nilai Kehilangan (RM)</th>
                        <th class="py-3 px-4">Keadaan Semasa Kehilangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($lossCase->items as $item)
                    <tr>
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $item->stockItem->item_code ?? '-' }}</span>
                            <div class="font-medium text-slate-800">{{ $item->stockItem->description ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-rose-700">
                            {{ number_format($item->quantity) }} {{ $item->stockItem->uom->code ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-700">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-rose-700">
                            {{ number_format($item->total_value, 2) }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            {{ $item->circumstances ?: '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- LIFECYCLE ACTIONS -->

    <!-- STAGE 1: APPOINT INVESTIGATION COMMITTEE (KEW.PS-33) -->
    @if($lossCase->status === 'REPORTED')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4" x-data="{
        committees: [
            { officer_name: 'Dr. Faizal Rahman', position: 'Ketua Unit Audit', department: 'ASM Cawangan', role: 'PENGERUSI' },
            { officer_name: 'Nurul Huda', position: 'Pegawai Tadbir', department: 'ASM Sumber Manusia', role: 'AHLI' }
        ],
        addMember() {
            this.committees.push({ officer_name: '', position: '', department: '', role: 'AHLI' });
        }
    }">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">Tindakan: Pelantikan Jawatankuasa Penyiasat (KEW.PS-33)</h3>
                <p class="text-xs text-slate-500">Lantik pegawai bebas untuk menyiasat punca kehilangan stok dan mengesyorkan surcaj/tatatertib jika berlaku kecuaian.</p>
            </div>
            <button type="button" @click="addMember()" class="text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg border border-amber-200">
                + Tambah Ahli Jawatankuasa
            </button>
        </div>

        <form action="{{ route('loss.appoint-committee', $lossCase) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Rujukan Surat Lantikan (KEW.PS-33) <span class="text-rose-500">*</span></label>
                <input type="text" name="investigation_committee_ref" value="ASM/KEHILANGAN/LANTIKAN/{{ date('Y') }}/{{ sprintf('%03d', $lossCase->id) }}" required class="w-full text-sm border-slate-300 rounded-lg">
            </div>

            <div class="space-y-3">
                <template x-for="(comm, index) in committees" :key="index">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nama Pegawai</label>
                            <input type="text" :name="`committees[${index}][officer_name]`" x-model="comm.officer_name" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Jawatan</label>
                            <input type="text" :name="`committees[${index}][position]`" x-model="comm.position" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Bahagian / Agensi</label>
                            <input type="text" :name="`committees[${index}][department]`" x-model="comm.department" required class="w-full text-xs border-slate-300 rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Peranan</label>
                            <select :name="`committees[${index}][role]`" x-model="comm.role" class="w-full text-xs border-slate-300 rounded-lg bg-white">
                                <option value="PENGERUSI">Pengerusi</option>
                                <option value="AHLI">Ahli</option>
                            </select>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" onclick="return confirm('Keluarkan Surat Pelantikan Jawatankuasa Penyiasat KEW.PS-33?')" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-bold shadow transition">
                    Lantik Jawatankuasa Penyiasat (KEW.PS-33)
                </button>
            </div>
        </form>
    </div>

    <!-- STAGE 2: SUBMIT FINAL REPORT (KEW.PS-34) -->
    @elseif($lossCase->status === 'INVESTIGATING')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Tindakan Jawatankuasa Penyiasat: Laporan Akhir (KEW.PS-34)</h3>
            <p class="text-xs text-slate-500">Kemukakan dapatan siasatan, punca kehilangan, syor tatatertib/surcaj dan langkah kawalan dalaman.</p>
        </div>

        <form action="{{ route('loss.submit-final-report', $lossCase) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Dapatan Siasatan Jawatankuasa <span class="text-rose-500">*</span></label>
                <textarea name="findings" rows="3" required placeholder="Contoh: Siasatan mendapati stor dimasuki melalui tingkap belakang yang rosak selaknya..." class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Syor & Langkah Penambahbaikan <span class="text-rose-500">*</span></label>
                <textarea name="recommendations" rows="3" required placeholder="Contoh: Disyorkan stok dihapus kira dan kunci jeriji keselamatan dipasang segera..." class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="surcharge_recommended" value="1" id="surcharge" class="rounded text-rose-600 focus:ring-rose-500">
                    <label for="surcharge" class="text-sm font-bold text-slate-800 cursor-pointer">Syor Tindakan Surcaj / Tatatertib Terhadap Pegawai Yang Cuai</label>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Perincian Syor Surcaj (Jika Berkenaan)</label>
                    <input type="text" name="surcharge_details" placeholder="Nama pegawai, justifikasi kecuaian dan peratusan surcaj disyorkan" class="w-full text-sm border-slate-300 rounded-lg bg-white">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" onclick="return confirm('Hantar Laporan Akhir Siasatan (KEW.PS-34) untuk kelulusan Hapus Kira?')" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow transition">
                    Kemukakan Laporan Akhir (KEW.PS-34)
                </button>
            </div>
        </form>
    </div>

    <!-- STAGE 3: APPROVE WRITE-OFF (KEW.PS-35) -->
    @elseif($lossCase->status === 'SUBMITTED_FOR_WRITE_OFF')
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h4 class="text-base font-bold text-emerald-900">Tindakan Kuasa Melulus: Kelulusan Hapus Kira (KEW.PS-35)</h4>
            <p class="text-xs text-emerald-800 mt-1">Luluskan hapus kira nilai kehilangan stok ini dan keluarkan Sijil Hapus Kira Stok (KEW.PS-35). Baki stok akan ditolak dari rekod rasmi.</p>
        </div>
        @if(auth()->user() && auth()->user()->hasRole(['ketua_jabatan', 'admin', 'pegawai_pelulus']))
        <form action="{{ route('loss.approve-write-off', $lossCase) }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Luluskan Hapus Kira ini secara muktamad? Baki stok akan ditolak secara automatik dan kekal.')" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow transition">
                Luluskan Hapus Kira & Keluarkan Sijil KEW.PS-35
            </button>
        </form>
        @else
        <span class="text-xs font-semibold text-emerald-800 bg-white px-3 py-1.5 rounded-lg border border-emerald-300">Menunggu Kelulusan Kuasa Melulus</span>
        @endif
    </div>
    @endif
</div>
@endsection
