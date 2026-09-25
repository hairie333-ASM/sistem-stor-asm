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
                        asm: {
                            green: '#10754A',
                            darkgreen: '#0b5334',
                            lightgreen: '#ecfdf5',
                            bordergreen: '#a7f3d0',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="min-h-full flex flex-col font-sans text-slate-800 antialiased bg-slate-50" x-data="{ mobileMenuOpen: false }">

    @php
        $u = auth()->user();
        $userName = $u->name ?? 'Pengguna';
        $nameParts = preg_split('/\s+/', trim($userName));
        $initials = '';
        if (count($nameParts) >= 2) {
            $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp

    <!-- 1. Top Government Masthead & Role Switcher Bar (Identical to ASM reference standard) -->
    <header class="bg-[#0B1320] text-white border-b border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5 flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-2.5">
                <img src="{{ asset('images/asm-logo-emblem.png') }}" alt="Logo ASM" class="w-5 h-5 object-contain bg-white rounded-full p-0.5 shadow-sm shrink-0">
                <span class="font-medium text-slate-300">Unit Pengurusan Stor (UPS)</span>
                <span class="text-slate-600">|</span>
                <span class="text-emerald-400 font-semibold">Akademi Sains Malaysia (ASM)</span>
                <span class="text-slate-600 hidden sm:inline">|</span>
                <span class="text-slate-400 hidden sm:inline">Sistem Pengurusan Stor Kerajaan (TPS)</span>
            </div>

            <!-- Role Switcher -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-1.5 text-xs">
                    <span class="text-slate-400">Peranan Semasa:</span>
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#78350F] text-[#FEF3C7] border border-[#B45309]">
                        {{ $u->role->display_name ?? 'Pengguna' }}
                    </span>
                </div>

                <!-- Demo Role Switcher Dropdown (Matching UPF Demo Button exactly) -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" type="button" class="text-xs bg-[#047857] hover:bg-[#059669] text-white px-2.5 py-1 rounded-md font-semibold transition flex items-center space-x-1.5 shadow-sm">
                        <span class="text-amber-300">⚡</span>
                        <span>Tukar Peranan Demo</span>
                        <svg class="w-3.5 h-3.5 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.duration.150ms class="absolute right-0 mt-1.5 w-64 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl z-50 py-1.5 text-slate-200 text-xs">
                        <div class="px-3 py-1.5 text-[10px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-800">Tukar Peranan Pengguna (9 Peranan TPS)</div>
                        <a href="{{ route('auth.switch-role', 'pemohon') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isPemohon() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">1. Pemohon / Staf (KEW.PS-8)</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_pelulus') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isPegawaiPelulus() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">2. Pegawai Pelulus (Kelulusan Pesanan)</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_stor') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isPegawaiStor() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">3. Pegawai Stor (Operasi/Kad Stok)</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_penerima') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isPegawaiPenerima() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">4. Pegawai Penerima (KEW.PS-1/2)</a>
                        <a href="{{ route('auth.switch-role', 'pemverifikasi') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isPemverifikasi() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">5. Pemverifikasi Stor (KEW.PS-10-14)</a>
                        <a href="{{ route('auth.switch-role', 'urus_setia_pelupusan') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isUrusSetiaPelupusan() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">6. Urus Setia Pelupusan (KEW.PS-19-31)</a>
                        <a href="{{ route('auth.switch-role', 'urus_setia_kehilangan') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isUrusSetiaKehilangan() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">7. Urus Setia Kehilangan (KEW.PS-32-36)</a>
                        <a href="{{ route('auth.switch-role', 'ketua_jabatan') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isKetuaJabatan() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">8. Ketua Jabatan (Pelulus Akhir)</a>
                        <a href="{{ route('auth.switch-role', 'admin') }}" class="block px-3 py-1.5 hover:bg-slate-800 hover:text-emerald-400 transition {{ $u->isAdmin() ? 'text-emerald-400 font-bold bg-slate-800/60' : '' }}">9. System Administrator</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Main Horizontal Navigation Bar (Melintang dengan Dropdown — UPF Standard) -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs" x-data="{ activeDropdown: null, profileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Left: Official ASM Logo + Title Stack (Matches UPF Reference) -->
                <div class="flex items-center space-x-3.5 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3.5 focus:outline-none group">
                        <img src="{{ asset('images/asm-logo-horizontal.png') }}" alt="Akademi Sains Malaysia" class="h-9 sm:h-10 w-auto object-contain">
                        <div class="h-8 w-px bg-slate-300 hidden md:block"></div>
                        <div class="hidden sm:block">
                            <div class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight leading-tight group-hover:text-emerald-800 transition">
                                SISTEM PENGURUSAN STOR KERAJAAN
                            </div>
                            <div class="text-[10px] sm:text-[11px] font-bold text-[#10754A] tracking-wider uppercase leading-tight">
                                UNIT PENGURUSAN STOR (TPS AM 6.1 - AM 6.10)
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Center: Horizontal Navigation Links with Dropdowns (Desktop) -->
                <div class="hidden lg:flex items-center space-x-1.5 text-xs sm:text-sm font-medium">

                @if($u->isPemohon())
                    {{-- 1. PEMOHON / STAFF --}}
                    <!-- Dashboard Link -->
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Dashboard</span>
                        <span class="sr-only">Papan Pemuka Pemohon</span>
                    </a>

                    <!-- Katalog & Stok Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'katalog') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'katalog' ? null : 'katalog'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Katalog Stok</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'katalog' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'katalog'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📦</span>
                                <div>
                                    <div class="font-bold">Katalog Stok ASM</div>
                                    <div class="text-[10px] text-slate-400">Semak senarai & baki alat tulis</div>
                                </div>
                            </a>
                            <a href="{{ route('stock.print-catalogue') }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🖨️</span>
                                <div>
                                    <div class="font-bold">Cetak Katalog Rasmi</div>
                                    <div class="text-[10px] text-slate-400">Format cetakan TPS AM 6.1</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Permohonan Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'permohonan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'permohonan' ? null : 'permohonan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('requests.*') || request()->routeIs('returns.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Permohonan Saya</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'permohonan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'permohonan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('requests.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">➕</span>
                                <div>
                                    <div class="font-bold">Borang Permohonan Stok</div>
                                    <div class="text-[10px] text-slate-400">Borang Pesanan KEW.PS-8</div>
                                </div>
                            </a>
                            <a href="{{ route('requests.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📝</span>
                                <div>
                                    <div class="font-bold">Senarai Permohonan Saya</div>
                                    <div class="text-[10px] text-slate-400">Status kelulusan & sejarah</div>
                                </div>
                            </a>
                            <div class="my-1 border-t border-slate-100"></div>
                            <a href="{{ route('returns.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🔄</span>
                                <div>
                                    <div class="font-bold">Pemulangan Stok</div>
                                    <div class="text-[10px] text-slate-400">Pulangkan baki stok tidak diguna</div>
                                </div>
                            </a>
                        </div>
                    </div>

                @elseif($u->isPegawaiPelulus())
                    {{-- 2. PEGAWAI PELULUS --}}
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Dashboard</span>
                        <span class="sr-only">Papan Pemuka Pelulus</span>
                    </a>

                    <div class="relative" @click.outside="if (activeDropdown === 'kelulusan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'kelulusan' ? null : 'kelulusan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('requests.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Tindakan Kelulusan</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'kelulusan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'kelulusan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('requests.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📝</span>
                                <div>
                                    <div class="font-bold">Kelulusan Pesanan (KEW.PS-7/8)</div>
                                    <div class="text-[10px] text-slate-400">Semakan dan kelulusan staf</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="if (activeDropdown === 'katalog') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'katalog' ? null : 'katalog'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Katalog Stok</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'katalog' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'katalog'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📦</span>
                                <div>
                                    <div class="font-bold">Semakan Katalog Stok</div>
                                    <div class="text-[10px] text-slate-400">Pantau paras baki stok semasa</div>
                                </div>
                            </a>
                            <a href="{{ route('stock.print-catalogue') }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🖨️</span>
                                <div>
                                    <div class="font-bold">Cetak Katalog Rasmi</div>
                                    <div class="text-[10px] text-slate-400">Format cetakan TPS AM 6.1</div>
                                </div>
                            </a>
                        </div>
                    </div>

                @elseif($u->isPegawaiPenerima())
                    {{-- 3. PEGAWAI PENERIMA --}}
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Dashboard</span>
                        <span class="sr-only">Papan Pemuka Penerimaan</span>
                    </a>

                    <div class="relative" @click.outside="if (activeDropdown === 'penerimaan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'penerimaan' ? null : 'penerimaan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('receiving.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Penerimaan Stok</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'penerimaan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'penerimaan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('receiving.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📥</span>
                                <div>
                                    <div class="font-bold">Senarai Penerimaan (BTB)</div>
                                    <div class="text-[10px] text-slate-400">Borang Terimaan Barang</div>
                                </div>
                            </a>
                            <a href="{{ route('receiving.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">➕</span>
                                <div>
                                    <div class="font-bold">Terima Barang Baru (KEW.PS-1)</div>
                                    <div class="text-[10px] text-slate-400">Pendaftaran terimaan pembekal</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('stock.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Katalog Stok ASM</span>
                    </a>

                @elseif($u->isPemverifikasi())
                    {{-- 4. PEGAWAI PEMVERIFIKASI --}}
                    <div class="relative" @click.outside="if (activeDropdown === 'dash') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'dash' ? null : 'dash'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Dashboard</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'dash' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'dash'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 text-xs">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📊</span>
                                <div>
                                    <div class="font-bold">Papan Pemuka Verifikasi</div>
                                    <div class="text-[10px] text-slate-400">Ringkasan pemeriksaan stok</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.compliance') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🚥</span>
                                <div>
                                    <div class="font-bold">Pemantauan Pematuhan</div>
                                    <div class="text-[10px] text-slate-400">Status integriti TPS AM 6</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="if (activeDropdown === 'verifikasi') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'verifikasi' ? null : 'verifikasi'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('verification.*') || request()->routeIs('stock-register.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Verifikasi Fizikal</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'verifikasi' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'verifikasi'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('verification.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🔍</span>
                                <div>
                                    <div class="font-bold">Laporan Verifikasi (KEW.PS-10..13)</div>
                                    <div class="text-[10px] text-slate-400">Pengiraan fizikal tahunan</div>
                                </div>
                            </a>
                            <a href="{{ route('stock-register.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📋</span>
                                <div>
                                    <div class="font-bold">Rujukan Kad Stok KEW.PS-3</div>
                                    <div class="text-[10px] text-slate-400">Semak lejar pergerakan stok</div>
                                </div>
                            </a>
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📦</span>
                                <div>
                                    <div class="font-bold">Katalog Stok ASM</div>
                                    <div class="text-[10px] text-slate-400">Senarai item inventori</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="if (activeDropdown === 'laporan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'laporan' ? null : 'laporan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('reports.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Laporan KEW.PS</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'laporan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'laporan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('reports.kew-ps-14') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📈</span>
                                <div>
                                    <div class="font-bold">Kedudukan Stok (KEW.PS-14)</div>
                                    <div class="text-[10px] text-slate-400">Kadar pusingan & nilai tahunan</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📑</span>
                                <div>
                                    <div class="font-bold">Hub Borang KEW.PS</div>
                                    <div class="text-[10px] text-slate-400">Janaan & cetakan 36 borang</div>
                                </div>
                            </a>
                        </div>
                    </div>

                @elseif($u->isUrusSetiaPelupusan())
                    {{-- 5. URUS SETIA PELUPUSAN --}}
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Dashboard</span>
                        <span class="sr-only">Papan Pemuka Pelupusan</span>
                    </a>

                    <div class="relative" @click.outside="if (activeDropdown === 'pelupusan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'pelupusan' ? null : 'pelupusan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('disposal.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Pelupusan Stok</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'pelupusan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'pelupusan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('disposal.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🗑️</span>
                                <div>
                                    <div class="font-bold">Senarai Pelupusan (KEW.PS-19..31)</div>
                                    <div class="text-[10px] text-slate-400">Permohonan & keputusan pelupusan</div>
                                </div>
                            </a>
                            <a href="{{ route('disposal.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">➕</span>
                                <div>
                                    <div class="font-bold">Permohonan Pelupusan Baru</div>
                                    <div class="text-[10px] text-slate-400">Daftar perakuan pelupusan</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📑</span>
                                <div>
                                    <div class="font-bold">Laporan Pelupusan (KEW.PS-23)</div>
                                    <div class="text-[10px] text-slate-400">Laporan tahunan pelupusan</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('stock.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Katalog Stok ASM</span>
                    </a>

                @elseif($u->isUrusSetiaKehilangan())
                    {{-- 6. URUS SETIA KEHILANGAN --}}
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Dashboard</span>
                        <span class="sr-only">Papan Pemuka Kehilangan</span>
                    </a>

                    <div class="relative" @click.outside="if (activeDropdown === 'kehilangan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'kehilangan' ? null : 'kehilangan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('loss.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Kehilangan & Hapus Kira</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'kehilangan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'kehilangan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('loss.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">⚖️</span>
                                <div>
                                    <div class="font-bold">Senarai Kes (KEW.PS-32..36)</div>
                                    <div class="text-[10px] text-slate-400">Siasatan & kelulusan hapus kira</div>
                                </div>
                            </a>
                            <a href="{{ route('loss.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">➕</span>
                                <div>
                                    <div class="font-bold">Daftar Kes Hilang (KEW.PS-32)</div>
                                    <div class="text-[10px] text-slate-400">Laporan awal kehilangan stok</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📑</span>
                                <div>
                                    <div class="font-bold">Laporan Hapus Kira (KEW.PS-36)</div>
                                    <div class="text-[10px] text-slate-400">Laporan tahunan kehilangan</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('stock.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}">
                        <span>Katalog Stok ASM</span>
                    </a>

                @elseif($u->isKetuaJabatan())
                    {{-- 7. KETUA JABATAN --}}
                    <div class="relative" @click.outside="if (activeDropdown === 'dash') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'dash' ? null : 'dash'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Dashboard</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'dash' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'dash'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📊</span>
                                <div>
                                    <div class="font-bold">Dashboard Eksekutif</div>
                                    <div class="text-[10px] text-slate-400">KPI & analisa stor pengurusan</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.compliance') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🚥</span>
                                <div>
                                    <div class="font-bold">Pemantauan Pematuhan TPS</div>
                                    <div class="text-[10px] text-slate-400">Pematuhan pekeliling AM 6</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="if (activeDropdown === 'kelulusan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'kelulusan' ? null : 'kelulusan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('requests.*') || request()->routeIs('verification.*') || request()->routeIs('disposal.*') || request()->routeIs('loss.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Tindakan Kelulusan</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'kelulusan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'kelulusan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('requests.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📝</span>
                                <div>
                                    <div class="font-bold">Pesanan Stok (KEW.PS-7/8)</div>
                                    <div class="text-[10px] text-slate-400">Kelulusan permohonan staf</div>
                                </div>
                            </a>
                            <a href="{{ route('verification.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🔍</span>
                                <div>
                                    <div class="font-bold">Pelarasan Stok (KEW.PS-16)</div>
                                    <div class="text-[10px] text-slate-400">Perakuan lebihan/kurangan</div>
                                </div>
                            </a>
                            <a href="{{ route('disposal.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🗑️</span>
                                <div>
                                    <div class="font-bold">Kelulusan Pelupusan (KEW.PS-21)</div>
                                    <div class="text-[10px] text-slate-400">Kelulusan kaedah pelupusan</div>
                                </div>
                            </a>
                            <a href="{{ route('loss.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">⚖️</span>
                                <div>
                                    <div class="font-bold">Kelulusan Hapus Kira (KEW.PS-35)</div>
                                    <div class="text-[10px] text-slate-400">Syor tindakan hapus kira</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="if (activeDropdown === 'katalog') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'katalog' ? null : 'katalog'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') || request()->routeIs('stock-register.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Katalog & Rekod</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'katalog' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'katalog'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📦</span>
                                <div>
                                    <div class="font-bold">Katalog Stok ASM</div>
                                    <div class="text-[10px] text-slate-400">Inventori rasmi stor</div>
                                </div>
                            </a>
                            <a href="{{ route('stock-register.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📋</span>
                                <div>
                                    <div class="font-bold">Daftar Stok (KEW.PS-3)</div>
                                    <div class="text-[10px] text-slate-400">Rekod kawalan & lejar stok</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="if (activeDropdown === 'laporan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'laporan' ? null : 'laporan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('reports.*') || request()->routeIs('admin.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Laporan & Tadbir Urus</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'laporan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'laporan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📑</span>
                                <div>
                                    <div class="font-bold">Hub Borang KEW.PS-1..36</div>
                                    <div class="text-[10px] text-slate-400">Janaan & cetakan 36 borang</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.kew-ps-14') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📈</span>
                                <div>
                                    <div class="font-bold">Kedudukan Stok (KEW.PS-14)</div>
                                    <div class="text-[10px] text-slate-400">Laporan tahunan stor</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.stock-valuation') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">💰</span>
                                <div>
                                    <div class="font-bold">Penilaian Baki Stok</div>
                                    <div class="text-[10px] text-slate-400">Nilai aset semasa inventori</div>
                                </div>
                            </a>
                            <div class="my-1 border-t border-slate-100"></div>
                            <a href="{{ route('admin.users') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">👥</span>
                                <div>
                                    <div class="font-bold">Pengurusan Pengguna</div>
                                    <div class="text-[10px] text-slate-400">Akaun staf & penetapan peranan</div>
                                </div>
                            </a>
                            <a href="{{ route('admin.audit') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📜</span>
                                <div>
                                    <div class="font-bold">Jejak Audit Sistem</div>
                                    <div class="text-[10px] text-slate-400">Log transaksi keselamatan</div>
                                </div>
                            </a>
                        </div>
                    </div>

                @else
                    {{-- 8 & 9. PEGAWAI STOR & ADMIN (Universal Store Operations) --}}
                    <!-- Dashboard Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'dash') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'dash' ? null : 'dash'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('dashboard*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Dashboard</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'dash' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'dash'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📊</span>
                                <div>
                                    <div class="font-bold">Dashboard Pengurusan</div>
                                    <div class="text-[10px] text-slate-400">KPI utama & statistik inventori</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.compliance') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🚥</span>
                                <div>
                                    <div class="font-bold">Pemantauan Pematuhan</div>
                                    <div class="text-[10px] text-slate-400">Status integriti TPS AM 6</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Pengurusan Stok Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'stok') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'stok' ? null : 'stok'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('stock.*') || request()->routeIs('stock-register.*') || request()->routeIs('locations.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Stok & Katalog</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'stok' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'stok'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('stock.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📦</span>
                                <div>
                                    <div class="font-bold">Katalog Stok Induk</div>
                                    <div class="text-[10px] text-slate-400">981 item inventori & foto produk</div>
                                </div>
                            </a>
                            <a href="{{ route('stock-register.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📋</span>
                                <div>
                                    <div class="font-bold">Daftar Stok (KEW.PS-3)</div>
                                    <div class="text-[10px] text-slate-400">Kad kawalan digital rasmi</div>
                                </div>
                            </a>
                            <a href="{{ route('locations.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📍</span>
                                <div>
                                    <div class="font-bold">Lokasi Penyimpanan</div>
                                    <div class="text-[10px] text-slate-400">Rak, baris, tingkat & petak</div>
                                </div>
                            </a>
                            <a href="{{ route('stock.expiry') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">⏳</span>
                                <div>
                                    <div class="font-bold">Tarikh Luput (KEW.PS-6)</div>
                                    <div class="text-[10px] text-slate-400">Kawalan stok mudah rosak</div>
                                </div>
                            </a>
                            <a href="{{ route('stock.group-ab') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🏷️</span>
                                <div>
                                    <div class="font-bold">Kumpulan A & B (KEW.PS-5)</div>
                                    <div class="text-[10px] text-slate-400">Analisis nilai pusingan tahunan</div>
                                </div>
                            </a>
                            <div class="my-1 border-t border-slate-100"></div>
                            <a href="{{ route('stock.print-catalogue') }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🖨️</span>
                                <div>
                                    <div class="font-bold">Cetak Katalog Rasmi</div>
                                    <div class="text-[10px] text-slate-400">Format cetakan TPS AM 6.1</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Operasi & Agihan Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'operasi') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'operasi' ? null : 'operasi'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('receiving.*') || request()->routeIs('requests.*') || request()->routeIs('returns.*') || request()->routeIs('transfers.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Operasi & Agihan</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'operasi' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'operasi'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('receiving.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📥</span>
                                <div>
                                    <div class="font-bold">Penerimaan (KEW.PS-1/2)</div>
                                    <div class="text-[10px] text-slate-400">Terimaan pembekal & BTB</div>
                                </div>
                            </a>
                            <a href="{{ route('requests.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📝</span>
                                <div>
                                    <div class="font-bold">Pesanan Stok (KEW.PS-7/8)</div>
                                    <div class="text-[10px] text-slate-400">Permohonan & pengeluaran stok</div>
                                </div>
                            </a>
                            <a href="{{ route('returns.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🔄</span>
                                <div>
                                    <div class="font-bold">Pemulangan Stok</div>
                                    <div class="text-[10px] text-slate-400">Penerimaan balik stok berlebihan</div>
                                </div>
                            </a>
                            <a href="{{ route('transfers.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🚚</span>
                                <div>
                                    <div class="font-bold">Pindahan Stor (KEW.PS-17)</div>
                                    <div class="text-[10px] text-slate-400">Pindahan antara stor/cawangan</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Pemeriksaan & Kawalan Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'kawalan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'kawalan' ? null : 'kawalan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('verification.*') || request()->routeIs('safety.*') || request()->routeIs('disposal.*') || request()->routeIs('loss.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Kawalan & Pematuhan</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'kawalan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'kawalan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('verification.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🔍</span>
                                <div>
                                    <div class="font-bold">Verifikasi & Pelarasan</div>
                                    <div class="text-[10px] text-slate-400">KEW.PS-10 hingga KEW.PS-16</div>
                                </div>
                            </a>
                            <a href="{{ route('safety.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🛡️</span>
                                <div>
                                    <div class="font-bold">Keselamatan & Kebersihan</div>
                                    <div class="text-[10px] text-slate-400">Pemeriksaan fizikal berkala</div>
                                </div>
                            </a>
                            <a href="{{ route('disposal.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🗑️</span>
                                <div>
                                    <div class="font-bold">Pelupusan (KEW.PS-19..31)</div>
                                    <div class="text-[10px] text-slate-400">Pengurusan pelupusan stok usang</div>
                                </div>
                            </a>
                            <a href="{{ route('loss.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">⚖️</span>
                                <div>
                                    <div class="font-bold">Kehilangan & Hapus Kira</div>
                                    <div class="text-[10px] text-slate-400">KEW.PS-32 hingga KEW.PS-36</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Laporan Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'laporan') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'laporan' ? null : 'laporan'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('reports.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Laporan KEW.PS</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'laporan' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'laporan'" x-cloak x-transition.origin.top.duration.150ms class="absolute left-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📑</span>
                                <div>
                                    <div class="font-bold">Hub Borang KEW.PS-1..36</div>
                                    <div class="text-[10px] text-slate-400">Janaan & cetakan 36 borang rasmi</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.kew-ps-14') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📈</span>
                                <div>
                                    <div class="font-bold">Kedudukan Stok (KEW.PS-14)</div>
                                    <div class="text-[10px] text-slate-400">Laporan tahunan & kadar pusingan</div>
                                </div>
                            </a>
                            <a href="{{ route('reports.stock-valuation') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">💰</span>
                                <div>
                                    <div class="font-bold">Penilaian Baki Stok</div>
                                    <div class="text-[10px] text-slate-400">Nilai kewangan stok semasa</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    @if($u->isAdmin())
                    <!-- Administration Dropdown -->
                    <div class="relative" @click.outside="if (activeDropdown === 'admin') activeDropdown = null">
                        <button 
                            @click="activeDropdown = activeDropdown === 'admin' ? null : 'admin'"
                            type="button" 
                            class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('admin.*') ? 'bg-[#ECFDF5] text-[#065F46] font-bold border border-[#A7F3D0]' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-100' }}"
                        >
                            <span>Pentadbiran</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="activeDropdown === 'admin' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="activeDropdown === 'admin'" x-cloak x-transition.origin.top.duration.150ms class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <a href="{{ route('admin.users') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">👥</span>
                                <div>
                                    <div class="font-bold">Pengurusan Pengguna</div>
                                    <div class="text-[10px] text-slate-400">Senarai & peranan pengguna</div>
                                </div>
                            </a>
                            <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">⚙️</span>
                                <div>
                                    <div class="font-bold">Tetapan Stor & Sistem</div>
                                    <div class="text-[10px] text-slate-400">Konfigurasi & parameter TPS</div>
                                </div>
                            </a>
                            <a href="{{ route('admin.audit') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">📜</span>
                                <div>
                                    <div class="font-bold">Jejak Audit (Audit Trail)</div>
                                    <div class="text-[10px] text-slate-400">Log transaksi & keselamatan</div>
                                </div>
                            </a>
                            <a href="{{ route('admin.year-end') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-emerald-50 hover:text-[#065F46] transition font-medium">
                                <span class="text-base">🏁</span>
                                <div>
                                    <div class="font-bold">Proses Akhir Tahun</div>
                                    <div class="text-[10px] text-slate-400">Tutup buku & bawa baki ke hadapan</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endif
                @endif

                </div>

                <!-- Right Side: Action CTA Button, Notification Bell, User Profile Pill -->
                <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                    
                    <!-- Action CTA Button (Matching '+ Tempah Bilik' in reference image) -->
                    @if($u->isPemohon())
                        <a href="{{ route('requests.create') }}" class="px-3 sm:px-4 py-2 bg-[#10754A] hover:bg-[#0b5334] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-sm transition flex items-center space-x-1.5 shrink-0">
                            <span class="text-base font-bold leading-none">+</span>
                            <span>Mohon Stok</span>
                        </a>
                    @elseif($u->isPegawaiPenerima())
                        <a href="{{ route('receiving.create') }}" class="px-3 sm:px-4 py-2 bg-[#10754A] hover:bg-[#0b5334] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-sm transition flex items-center space-x-1.5 shrink-0">
                            <span class="text-base font-bold leading-none">+</span>
                            <span>Terima Stok</span>
                        </a>
                    @elseif($u->isUrusSetiaPelupusan())
                        <a href="{{ route('disposal.create') }}" class="px-3 sm:px-4 py-2 bg-[#10754A] hover:bg-[#0b5334] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-sm transition flex items-center space-x-1.5 shrink-0">
                            <span class="text-base font-bold leading-none">+</span>
                            <span>Pelupusan Baru</span>
                        </a>
                    @elseif($u->isUrusSetiaKehilangan())
                        <a href="{{ route('loss.create') }}" class="px-3 sm:px-4 py-2 bg-[#10754A] hover:bg-[#0b5334] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-sm transition flex items-center space-x-1.5 shrink-0">
                            <span class="text-base font-bold leading-none">+</span>
                            <span>Lapor Hilang</span>
                        </a>
                    @else
                        <a href="{{ route('requests.create') }}" class="px-3 sm:px-4 py-2 bg-[#10754A] hover:bg-[#0b5334] text-white rounded-lg text-xs sm:text-sm font-semibold shadow-sm transition flex items-center space-x-1.5 shrink-0">
                            <span class="text-base font-bold leading-none">+</span>
                            <span>Pesanan Stok</span>
                        </a>
                    @endif

                    <!-- Notification Bell Icon (Matching Reference) -->
                    <div class="relative">
                        <button type="button" class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 transition relative focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#10754A] rounded-full ring-2 ring-white"></span>
                        </button>
                    </div>

                    <!-- User Profile Pill with Avatar & Dropdown (Matching Reference Image exactly) -->
                    <div class="relative" @click.outside="profileOpen = false">
                        <button 
                            @click="profileOpen = !profileOpen" 
                            type="button" 
                            class="flex items-center space-x-2 sm:space-x-2.5 p-1 sm:px-2.5 sm:py-1 rounded-xl hover:bg-slate-100 transition focus:outline-none group"
                        >
                            <!-- Circle with User Initials (Emerald Green circle matching reference 'WA') -->
                            <div class="w-8 h-8 rounded-full bg-[#10754A] text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                {{ $initials }}
                            </div>
                            <div class="text-left hidden md:block">
                                <div class="text-xs font-bold text-slate-900 leading-tight group-hover:text-emerald-800 transition truncate max-w-[130px]">
                                    {{ $userName }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium leading-tight truncate max-w-[130px]">
                                    {{ $u->department ? $u->department . ' • ' : '' }}{{ $u->role->display_name ?? 'Pengguna' }}
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Profile Dropdown Menu -->
                        <div x-show="profileOpen" x-cloak x-transition.origin.top.duration.150ms class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <div class="font-bold text-slate-900 text-sm truncate">{{ $userName }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $u->email }}</div>
                                <div class="mt-1.5 inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-[#065F46] border border-emerald-200">
                                    {{ $u->role->display_name ?? 'Pengguna' }}
                                </div>
                            </div>
                            <div class="px-2 py-1">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-700 hover:bg-slate-50 hover:text-emerald-700 transition">
                                    <span>📊</span>
                                    <span>Papan Pemuka Utama</span>
                                </a>
                                @if($u->isAdmin())
                                <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-700 hover:bg-slate-50 hover:text-emerald-700 transition">
                                    <span>⚙️</span>
                                    <span>Tetapan Sistem</span>
                                </a>
                                @endif
                            </div>
                            <div class="border-t border-slate-100 pt-1 px-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-rose-600 hover:bg-rose-50 font-semibold transition text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        <span>Log Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Hamburger Button -->
                    <button 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        type="button" 
                        class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 lg:hidden focus:outline-none"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                </div>

            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileMenuOpen" x-cloak x-transition.origin.top.duration.200ms class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3">
            @if($u->isPemohon())
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-2">Menu Pemohon</div>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">Dashboard (Papan Pemuka Pemohon)</a>
                <a href="{{ route('stock.index') }}" class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('stock.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">Katalog Stok ASM</a>
                <a href="{{ route('requests.create') }}" class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('requests.create') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">Borang Permohonan Stok</a>
                <a href="{{ route('requests.index') }}" class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('requests.index') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">Senarai Permohonan Saya</a>
                <a href="{{ route('returns.index') }}" class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('returns.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">Pemulangan Stok</a>
            @else
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-2">Pautan Pantas</div>
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Dashboard Utama</a>
                <a href="{{ route('stock.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Katalog Stok</a>
                <a href="{{ route('requests.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Pesanan Stok</a>
                @if($u->hasRole(['admin', 'pegawai_stor']))
                <a href="{{ route('stock-register.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Daftar Stok (KEW.PS-3)</a>
                <a href="{{ route('receiving.index') }}" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Penerimaan (KEW.PS-1/2)</a>
                @endif
            @endif
        </div>
    </nav>

    <!-- 3. Main Content Container (Full Width / Max 7xl Standard) -->
    <main class="flex-1 bg-slate-50 min-h-screen">
        
        <!-- Breadcrumb / Page Header (Matching UPF Standard) -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        @hasSection('page_category')
                        <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-1.5">
                            @yield('page_category')
                        </div>
                        @endif
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">@yield('page_title', 'Ringkasan Sistem')</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl">@yield('page_description', 'Sistem Pengurusan Stor Kerajaan (TPS) – AM 6.1 hingga AM 6.10')</p>
                    </div>
                    <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                        @yield('page_actions')
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs">
                    <div class="flex items-center space-x-2">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs">
                    <div class="flex items-center space-x-2">
                        <span>❌</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl text-sm mb-4 shadow-xs">
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </div>

    </main>

    <!-- 4. Global Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>© 2026 Akademi Sains Malaysia (ASM) — Hak Cipta Terpelihara</span>
            <span>Pekeliling Perbendaharaan Malaysia AM 6.1 – AM 6.10</span>
        </div>
    </footer>

</body>
</html>
