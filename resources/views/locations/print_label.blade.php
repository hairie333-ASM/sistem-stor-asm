<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Lokasi Rak: {{ $location->full_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 print:bg-white p-6 flex flex-col items-center justify-center min-h-screen">

    <div class="mb-4 no-print flex space-x-3">
        <button onclick="window.history.back()" class="px-4 py-2 bg-slate-300 rounded font-medium text-xs">← Kembali</button>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-700 text-white rounded font-bold text-xs shadow">🖨️ Cetak Label Rak</button>
    </div>

    <!-- Official Shelf Label Badge -->
    <div class="w-96 bg-white border-4 border-slate-900 rounded-2xl p-6 shadow-xl print:shadow-none text-center">
        <div class="border-b-2 border-slate-900 pb-2 mb-4 flex justify-between items-center">
            <span class="text-xs font-black uppercase text-slate-800">AKADEMI SAINS MALAYSIA</span>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-900 text-white rounded">STOR KERAJAAN</span>
        </div>

        <div class="text-xs font-semibold text-slate-500 uppercase">{{ $location->store->name }}</div>
        <div class="text-[11px] text-slate-600 mb-3">{{ $location->section->name ?? 'Gudang Utama' }}</div>

        <!-- Simulated Barcode / QR Visual Representation -->
        <div class="my-4 p-4 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center">
            <!-- SVG QR Code Icon Generator -->
            <svg class="w-28 h-28 text-slate-900" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm8-2h2v2h-2v-2zm4 0h2v2h-2v-2zm2 2h2v2h-2v-2zm-6 2h2v2h-2v-2zm4 2h2v2h-2v-2zm2-2h2v2h-2v-2zm-2 4h4v2h-4v-2zm-6-2h2v2h-2v-2zm0-4h2v2h-2v-2zm4-2h2v2h-2v-2z"/>
            </svg>
            <div class="font-mono text-[10px] text-slate-500 mt-2 tracking-widest">{{ $location->barcode ?? $location->full_code }}</div>
        </div>

        <!-- Full Location Breakdown -->
        <div class="mt-2 pt-3 border-t-2 border-slate-900">
            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Kod Lokasi Rak Penuh:</div>
            <div class="font-mono text-xl font-black text-slate-950 tracking-wider my-1">
                {{ $location->full_code }}
            </div>
            <div class="grid grid-cols-4 gap-1 text-[10px] text-slate-700 font-bold mt-2 pt-2 border-t border-slate-200">
                <div>Baris: {{ $location->row }}</div>
                <div>Rak: {{ $location->rack }}</div>
                <div>Tingkat: {{ $location->level }}</div>
                <div>Petak: {{ $location->bin }}</div>
            </div>
        </div>
    </div>

</body>
</html>
