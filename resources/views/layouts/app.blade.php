<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengurusan Stor Kerajaan') - Akademi Sains Malaysia</title>
    <link rel="icon" type="image/png" href="{{ asset('images/asm-logo-emblem.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        gold: {
                            500: '#d97706',
                            600: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <!-- Top Government Masthead & Role Switcher Bar -->
    <header class="bg-navy-950 text-white border-b border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5 flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-2.5">
                <img src="{{ asset('images/asm-logo-emblem.png') }}" alt="Logo ASM" class="w-5 h-5 object-contain bg-white rounded-full p-0.5 shadow-sm">
                <span class="font-semibold text-slate-300">Sistem Pengurusan Stor Kerajaan (TPS)</span>
                <span class="text-slate-500">|</span>
                <span class="text-amber-400 font-medium">Akademi Sains Malaysia (ASM)</span>
                <span class="text-slate-500">|</span>
                <span class="text-slate-400">AM 6.1 – AM 6.10</span>
            </div>

            <!-- Quick Testing Role Switcher -->
            <div class="flex items-center space-x-2">
                <span class="text-slate-400">Peranan Semasa:</span>
                <span class="px-2 py-0.5 rounded bg-blue-900 text-blue-200 font-bold border border-blue-700">
                    {{ auth()->user()->role->display_name ?? 'Pengguna' }}
                </span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-xs bg-amber-600 hover:bg-amber-500 text-white px-2 py-0.5 rounded font-medium transition flex items-center space-x-1">
                        <span>⚡ Tukar Peranan Demo</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-64 bg-slate-900 border border-slate-700 rounded-lg shadow-xl z-50 py-1 text-slate-200">
                        <div class="px-3 py-1 text-[10px] uppercase tracking-wider text-slate-400 font-semibold border-b border-slate-800">Tukar Peranan Pengguna (9 Peranan TPS)</div>
                        <a href="{{ route('auth.switch-role', 'admin') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">1. System Administrator</a>
                        <a href="{{ route('auth.switch-role', 'ketua_jabatan') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">2. Ketua Jabatan (Pelulus Akhir)</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_stor') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">3. Pegawai Stor (Operasi/Kad Stok)</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_penerima') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">4. Pegawai Penerima (KEW.PS-1/2)</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_pelulus') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">5. Pegawai Pelulus (Kelulusan Pesanan)</a>
                        <a href="{{ route('auth.switch-role', 'pemverifikasi') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">6. Pemverifikasi Stor (KEW.PS-10-14)</a>
                        <a href="{{ route('auth.switch-role', 'urus_setia_pelupusan') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">7. Urus Setia Pelupusan (KEW.PS-19-31)</a>
                        <a href="{{ route('auth.switch-role', 'urus_setia_kehilangan') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">8. Urus Setia Kehilangan (KEW.PS-32-36)</a>
                        <a href="{{ route('auth.switch-role', 'pemohon') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-white text-xs">9. Pemohon / Staff (KEW.PS-8)</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Navigation (Conforms strictly to Section 34 of User Specification) -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between overflow-y-auto">
            <div class="py-4">
                <!-- Branding -->
                <div class="px-5 pb-4 mb-4 border-b border-slate-800 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shrink-0">
                        <img src="{{ asset('images/asm-logo-emblem.png') }}" alt="Logo ASM" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white tracking-wide">STOR ASM</div>
                        <div class="text-[11px] text-slate-400">Akademi Sains Malaysia</div>
                    </div>
                </div>

                <nav class="px-3 space-y-6 text-sm">
                @php
                    $u = auth()->user();
                @endphp

                @if($u->isPemohon())
                    {{-- 1. PEMOHON / STAFF --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Papan Pemuka Pemohon</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Pesanan Stok (KEW.PS-8)</div>
                        <a href="{{ route('requests.create') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('requests.create') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>➕</span>
                            <span>Borang Permohonan Stok</span>
                        </a>
                        <a href="{{ route('requests.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('requests.index') || request()->routeIs('requests.show') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📝</span>
                            <span>Senarai Permohonan Saya</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Katalog & Pemulangan</div>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok ASM</span>
                        </a>
                        <a href="{{ route('returns.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('returns.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🔄</span>
                            <span>Pemulangan Stok</span>
                        </a>
                    </div>

                @elseif($u->isPegawaiPelulus())
                    {{-- 2. PEGAWAI PELULUS --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Papan Pemuka Pelulus</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Tindakan Kelulusan</div>
                        <a href="{{ route('requests.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('requests.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📝</span>
                            <span>Kelulusan Pesanan (KEW.PS-7/8)</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Semakan Katalog Stok</span>
                        </a>
                    </div>

                @elseif($u->isPegawaiPenerima())
                    {{-- 3. PEGAWAI PENERIMA --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Papan Pemuka Penerimaan</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Penerimaan Stok (AM 6.2)</div>
                        <a href="{{ route('receiving.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('receiving.index') || request()->routeIs('receiving.show') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📥</span>
                            <span>Senarai Penerimaan (BTB)</span>
                        </a>
                        <a href="{{ route('receiving.create') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('receiving.create') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>➕</span>
                            <span>Terima Barang Baru (KEW.PS-1)</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok ASM</span>
                        </a>
                    </div>

                @elseif($u->isPemverifikasi())
                    {{-- 4. PEGAWAI PEMVERIFIKASI --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Papan Pemuka Verifikasi</span>
                        </a>
                        <a href="{{ route('dashboard.compliance') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard.compliance') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🚥</span>
                            <span>Pemantauan Pematuhan</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Verifikasi Fizikal (AM 6.6)</div>
                        <a href="{{ route('verification.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('verification.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🔍</span>
                            <span>Laporan Verifikasi (KEW.PS-10..13)</span>
                        </a>
                        <a href="{{ route('stock-register.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock-register.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📋</span>
                            <span>Rujukan Kad Stok KEW.PS-3</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok ASM</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Laporan Verifikasi</div>
                        <a href="{{ route('reports.kew-ps-14') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.kew-ps-14*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📈</span>
                            <span>Kedudukan Stok (KEW.PS-14)</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📑</span>
                            <span>Hub Borang KEW.PS</span>
                        </a>
                    </div>

                @elseif($u->isUrusSetiaPelupusan())
                    {{-- 5. URUS SETIA PELUPUSAN --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Papan Pemuka Pelupusan</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Pelupusan Stok (AM 6.9)</div>
                        <a href="{{ route('disposal.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('disposal.index') || request()->routeIs('disposal.show') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🗑️</span>
                            <span>Senarai Pelupusan (KEW.PS-19..31)</span>
                        </a>
                        <a href="{{ route('disposal.create') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('disposal.create') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>➕</span>
                            <span>Permohonan Pelupusan Baru</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok ASM</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Laporan Pelupusan</div>
                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📑</span>
                            <span>Laporan Pelupusan (KEW.PS-23)</span>
                        </a>
                    </div>

                @elseif($u->isUrusSetiaKehilangan())
                    {{-- 6. URUS SETIA KEHILANGAN --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Papan Pemuka Kehilangan</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Kehilangan & Hapus Kira (AM 6.10)</div>
                        <a href="{{ route('loss.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('loss.index') || request()->routeIs('loss.show') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>⚖️</span>
                            <span>Senarai Kes (KEW.PS-32..36)</span>
                        </a>
                        <a href="{{ route('loss.create') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('loss.create') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>➕</span>
                            <span>Daftar Kes Hilang (KEW.PS-32)</span>
                        </a>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok ASM</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Laporan Kehilangan</div>
                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📑</span>
                            <span>Laporan Hapus Kira (KEW.PS-36)</span>
                        </a>
                    </div>

                @elseif($u->isKetuaJabatan())
                    {{-- 7. KETUA JABATAN --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Dashboard Eksekutif</span>
                        </a>
                        <a href="{{ route('dashboard.compliance') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard.compliance') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🚥</span>
                            <span>Pemantauan Pematuhan TPS</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Tindakan Kelulusan</div>
                        <a href="{{ route('requests.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('requests.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📝</span>
                            <span>Pesanan Stok (KEW.PS-7/8)</span>
                        </a>
                        <a href="{{ route('verification.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('verification.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🔍</span>
                            <span>Pelarasan Stok (KEW.PS-16)</span>
                        </a>
                        <a href="{{ route('disposal.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('disposal.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🗑️</span>
                            <span>Kelulusan Pelupusan (KEW.PS-21)</span>
                        </a>
                        <a href="{{ route('loss.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('loss.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>⚖️</span>
                            <span>Kelulusan Hapus Kira (KEW.PS-35)</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Katalog & Rekod Stor</div>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok ASM</span>
                        </a>
                        <a href="{{ route('stock-register.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock-register.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📋</span>
                            <span>Daftar Stok (KEW.PS-3)</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Pusat Laporan & Tadbir Urus</div>
                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📑</span>
                            <span>Hub Borang KEW.PS-1..36</span>
                        </a>
                        <a href="{{ route('reports.kew-ps-14') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.kew-ps-14*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📈</span>
                            <span>Kedudukan Stok (KEW.PS-14)</span>
                        </a>
                        <a href="{{ route('reports.stock-valuation') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.stock-valuation') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>💰</span>
                            <span>Penilaian Baki Stok</span>
                        </a>
                        <a href="{{ route('admin.users') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>👥</span>
                            <span>Pengurusan Pengguna</span>
                        </a>
                        <a href="{{ route('admin.audit') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.audit*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📜</span>
                            <span>Jejak Audit Sistem</span>
                        </a>
                    </div>

                @else
                    {{-- 8. PEGAWAI STOR & ADMIN (Universal Store Operations) --}}
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Utama</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span>
                            <span>Dashboard Pengurusan</span>
                        </a>
                        <a href="{{ route('dashboard.compliance') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard.compliance') ? 'bg-blue-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🚥</span>
                            <span>Pemantauan Pematuhan</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">STOK</div>
                        <a href="{{ route('stock.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.index') || request()->routeIs('stock.create') || request()->routeIs('stock.edit') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📦</span>
                            <span>Katalog Stok Induk</span>
                        </a>
                        <a href="{{ route('stock-register.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock-register.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📋</span>
                            <span>Daftar Stok (KEW.PS-3)</span>
                        </a>
                        <a href="{{ route('locations.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('locations.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📍</span>
                            <span>Lokasi Penyimpanan</span>
                        </a>
                        <a href="{{ route('stock.expiry') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.expiry') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>⏳</span>
                            <span>Tarikh Luput (KEW.PS-6)</span>
                        </a>
                        <a href="{{ route('stock.group-ab') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('stock.group-ab') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🏷️</span>
                            <span>Kumpulan A & B (KEW.PS-5)</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">TRANSAKSI</div>
                        <a href="{{ route('receiving.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('receiving.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📥</span>
                            <span>Penerimaan (KEW.PS-1/2)</span>
                        </a>
                        <a href="{{ route('requests.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('requests.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📝</span>
                            <span>Pesanan Stok (KEW.PS-7/8)</span>
                        </a>
                        <a href="{{ route('returns.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('returns.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🔄</span>
                            <span>Pemulangan Stok</span>
                        </a>
                        <a href="{{ route('transfers.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('transfers.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🚚</span>
                            <span>Pindahan Stor (KEW.PS-17)</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">KAWALAN & PEMATUHAN</div>
                        <a href="{{ route('verification.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('verification.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🔍</span>
                            <span>Verifikasi & Pelarasan</span>
                        </a>
                        <a href="{{ route('safety.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('safety.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🛡️</span>
                            <span>Keselamatan & Kebersihan</span>
                        </a>
                        <a href="{{ route('disposal.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('disposal.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🗑️</span>
                            <span>Pelupusan (KEW.PS-19..31)</span>
                        </a>
                        <a href="{{ route('loss.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('loss.*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>⚖️</span>
                            <span>Kehilangan & Hapus Kira</span>
                        </a>
                    </div>

                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">REPORTS</div>
                        <a href="{{ route('reports.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.index') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📑</span>
                            <span>Hub Borang KEW.PS-1..36</span>
                        </a>
                        <a href="{{ route('reports.kew-ps-14') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.kew-ps-14*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📈</span>
                            <span>Kedudukan Stok (KEW.PS-14)</span>
                        </a>
                        <a href="{{ route('reports.stock-valuation') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.stock-valuation') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>💰</span>
                            <span>Penilaian Baki Stok</span>
                        </a>
                    </div>

                    @if($u->isAdmin())
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">ADMINISTRATION</div>
                        <a href="{{ route('admin.users') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>👥</span>
                            <span>Pengurusan Pengguna</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.settings*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>⚙️</span>
                            <span>Tetapan Stor & Sistem</span>
                        </a>
                        <a href="{{ route('admin.audit') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.audit*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📜</span>
                            <span>Jejak Audit (Audit Trail)</span>
                        </a>
                        <a href="{{ route('admin.year-end') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.year-end*') ? 'bg-blue-800 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🏁</span>
                            <span>Proses Akhir Tahun</span>
                        </a>
                    </div>
                    @endif
                @endif
                </nav>
            </div>

            <!-- User Footer Profile Box -->
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <div class="flex items-center justify-between">
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->position ?? auth()->user()->department }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Keluar" class="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-rose-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 flex flex-col">
            <!-- Breadcrumbs and Action Header -->
            <div class="bg-white border-b border-slate-200 px-6 py-4 flex flex-wrap justify-between items-center gap-4 shadow-sm">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">@yield('page_title', 'Ringkasan Sistem')</h1>
                    <p class="text-xs text-slate-500 mt-0.5">@yield('page_description', 'Sistem Pengurusan Stor Kerajaan (TPS) – AM 6.1 hingga AM 6.10')</p>
                </div>
                <div class="flex items-center space-x-3">
                    @yield('page_actions')
                </div>
            </div>

            <!-- Flash Messages -->
            <div class="px-6 pt-4">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between mb-4 shadow-sm">
                        <div class="flex items-center space-x-2">
                            <span>✅</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between mb-4 shadow-sm">
                        <div class="flex items-center space-x-2">
                            <span>❌</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg text-sm mb-4 shadow-sm">
                        <div class="font-bold mb-1 flex items-center space-x-1">
                            <span>⚠️</span>
                            <span>Sila semak ralat berikut:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Body Content -->
            <div class="flex-1 px-6 py-4">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 px-6 py-3 text-xs text-slate-500 flex justify-between items-center">
                <span>© 2026 Akademi Sains Malaysia (ASM) — Hak Cipta Terpelihara</span>
                <span>Pekeliling Perbendaharaan Malaysia AM 6.1 – AM 6.10</span>
            </footer>
        </main>
    </div>
</body>
</html>
