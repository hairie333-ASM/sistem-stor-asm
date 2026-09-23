@extends('layouts.app')

@section('title', 'Laporan Kedudukan Stok (KEW.PS-14)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">TPS AM 6.6</span>
                <span class="text-xs text-slate-500">Borang Rasmi KEW.PS-14</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Laporan Kedudukan Stok & Kadar Pusingan (KEW.PS-14)</h1>
            <p class="text-sm text-slate-600">Laporan kedudukan suku tahunan dan formula kadar pusingan stok tahunan mengikut Tatacara Pengurusan Stor AM 6.6.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.kew-ps-14.print', ['year' => $year]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Format Rasmi KEW.PS-14
            </a>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Year Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('reports.kew-ps-14') }}" class="flex items-center space-x-3">
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tahun Kewangan:</label>
            <select name="year" onchange="this.form.submit()" class="text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-bold w-32">
                @for($y = date('Y'); $y >= 2022; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Turnover KPI Highlight -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-1">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Terimaan Tahunan</div>
            <div class="text-2xl font-bold font-mono text-blue-700">RM {{ number_format($turnoverData['annual_receipt_total'], 2) }}</div>
            <div class="text-xs text-slate-400">Termasuk BTB & Pindahan Masuk</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-1">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Pengeluaran Tahunan</div>
            <div class="text-2xl font-bold font-mono text-purple-700">RM {{ number_format($turnoverData['annual_issue_total'], 2) }}</div>
            <div class="text-xs text-slate-400">Pengeluaran KEW.PS-7 & KEW.PS-8</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-1">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Purata Nilai Stok</div>
            <div class="text-2xl font-bold font-mono text-slate-800">RM {{ number_format($turnoverData['average_stock_value'], 2) }}</div>
            <div class="text-xs text-slate-400">(Baki {{ $turnoverData['prev_year'] }} + Baki {{ $turnoverData['year'] }}) / 2</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border {{ $turnoverData['target_achieved'] ? 'border-emerald-300 bg-emerald-50/40' : 'border-amber-300 bg-amber-50/40' }} p-5 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $turnoverData['target_achieved'] ? 'text-emerald-800' : 'text-amber-800' }}">Kadar Pusingan Stok</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $turnoverData['target_achieved'] ? 'bg-emerald-200 text-emerald-800' : 'bg-amber-200 text-amber-800' }}">
                    Sasaran ≥ {{ $turnoverData['target'] }}
                </span>
            </div>
            <div class="text-3xl font-extrabold font-mono {{ $turnoverData['target_achieved'] ? 'text-emerald-700' : 'text-amber-700' }}">
                {{ $turnoverData['turnover_rate'] }}
            </div>
            <div class="text-xs {{ $turnoverData['target_achieved'] ? 'text-emerald-700' : 'text-amber-700' }} font-semibold">
                {{ $turnoverData['target_achieved'] ? '✓ Mencapai Piawaian Perbendaharaan' : '⚠ Di Bawah Sasaran Piawaian' }}
            </div>
        </div>
    </div>

    <!-- Official KEW.PS-14 Quarterly Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Kedudukan Stok Suku Tahun Bagi Tahun {{ $year }}</h3>
                <p class="text-xs text-slate-500">Pecahan nilai pegangan stok mengikut suku tahun kewangan.</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-600 bg-white px-2.5 py-1 rounded border border-slate-200">
                Pekeliling AM 6.6
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4">Suku Tahun</th>
                        <th class="py-3 px-4 text-right">Baki Awal Stok (RM) (a)</th>
                        <th class="py-3 px-4 text-right">Nilai Terimaan (RM) (b)</th>
                        <th class="py-3 px-4 text-right">Nilai Pengeluaran (RM) (c)</th>
                        <th class="py-3 px-4 text-right">Baki Akhir Stok (RM) (a + b - c)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-xs">
                    @foreach($turnoverData['quarters'] as $q)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-sans font-bold text-slate-800">
                            {{ $q['label'] }}
                        </td>
                        <td class="py-3 px-4 text-right text-slate-700">
                            {{ number_format($q['opening_value'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right text-blue-700 font-semibold">
                            {{ number_format($q['receipt_value'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right text-purple-700 font-semibold">
                            {{ number_format($q['issue_value'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-900">
                            {{ number_format($q['closing_value'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300 text-xs font-mono">
                    <tr>
                        <td class="py-3 px-4 font-sans text-slate-900 uppercase">Jumlah Keseluruhan {{ $year }}</td>
                        <td class="py-3 px-4 text-right text-slate-500">-</td>
                        <td class="py-3 px-4 text-right text-blue-800 font-extrabold">{{ number_format($turnoverData['annual_receipt_total'], 2) }}</td>
                        <td class="py-3 px-4 text-right text-purple-800 font-extrabold">{{ number_format($turnoverData['annual_issue_total'], 2) }}</td>
                        <td class="py-3 px-4 text-right text-slate-900 font-extrabold">{{ number_format($turnoverData['current_year_closing'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Statutory Formula Explanatory Box -->
    <div class="bg-slate-900 text-white rounded-xl p-6 space-y-3">
        <h4 class="text-sm font-bold uppercase tracking-wider text-amber-400">Formula Pengiraan Kadar Pusingan Stok (TPS AM 6.6)</h4>
        <div class="p-4 bg-slate-800/80 rounded-lg border border-slate-700 font-mono text-xs leading-relaxed">
            <div class="text-slate-300 font-bold">Kadar Pusingan Stok = Nilai Pengeluaran Stok Tahunan / [ (Baki Stok Akhir Tahun Lepas + Baki Stok Akhir Tahun Semasa) / 2 ]</div>
            <div class="mt-2 text-emerald-400">
                = RM {{ number_format($turnoverData['annual_issue_total'], 2) }} / [ (RM {{ number_format($turnoverData['prev_year_closing'], 2) }} + RM {{ number_format($turnoverData['current_year_closing'], 2) }}) / 2 ]
            </div>
            <div class="mt-1 text-emerald-400">
                = RM {{ number_format($turnoverData['annual_issue_total'], 2) }} / RM {{ number_format($turnoverData['average_stock_value'], 2) }}
            </div>
            <div class="mt-1 text-amber-300 font-bold text-sm">
                = {{ $turnoverData['turnover_rate'] }} kali (Sasaran Piawaian TPS: ≥ 4.0 kali)
            </div>
        </div>
    </div>
</div>
@endsection
