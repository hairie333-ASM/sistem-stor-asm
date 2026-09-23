<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Borang Rasmi KEW.PS') - Akademi Sains Malaysia</title>
    <link rel="icon" type="image/png" href="{{ asset('images/asm-logo-emblem.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; font-size: 11pt; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        @page { size: auto; margin: 15mm 15mm 15mm 15mm; }
        .gov-table { border-collapse: collapse; width: 100%; }
        .gov-table th, .gov-table td { border: 1px solid #1f2937; padding: 6px 8px; font-size: 10pt; }
        .gov-table th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
    </style>
</head>
<body class="bg-gray-100 print:bg-white text-gray-900 font-sans p-4 sm:p-8">
    <div class="max-w-5xl mx-auto mb-4 no-print flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                Dokumen Rasmi Tatacara Pengurusan Stor (TPS)
            </span>
            <span class="text-sm text-gray-500">Sedia untuk cetakan atau simpanan fail PDF</span>
        </div>
        <div class="space-x-2">
            <button onclick="window.history.back()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded font-medium text-sm transition">
                ← Kembali
            </button>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded font-medium text-sm shadow transition flex-inline items-center">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-12 shadow-md print:shadow-none print:p-0 border print:border-none border-gray-300">
        <!-- Government Official Header -->
        <div class="flex justify-between items-start border-b-2 border-gray-900 pb-4 mb-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/asm-logo-emblem.png') }}" alt="Logo Akademi Sains Malaysia" class="w-16 h-16 object-contain shrink-0">
                <div>
                    <h1 class="text-lg font-bold uppercase tracking-wider text-gray-900">AKADEMI SAINS MALAYSIA</h1>
                    <p class="text-xs text-gray-600 font-medium">Sistem Pengurusan Stor Kerajaan (TPS) – Pekeliling Perbendaharaan AM 6</p>
                    <p class="text-xs text-gray-500">Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-block border-2 border-gray-900 px-3 py-1 text-sm font-black tracking-widest uppercase bg-gray-50">
                    @yield('kew_ps_code', 'KEW.PS')
                </div>
            </div>
        </div>

        <!-- Form Content -->
        @yield('content')

        <!-- Footer Notice -->
        <div class="mt-8 pt-4 border-t border-gray-300 text-xs text-gray-500 flex justify-between">
            <span>Dicetak secara digital melalui Sistem Pengurusan Stor ASM pada: {{ now()->translatedFormat('d F Y, h:i A') }}</span>
            <span>AM 6.1 – AM 6.10</span>
        </div>
    </div>
</body>
</html>
