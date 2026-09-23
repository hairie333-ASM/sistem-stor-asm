<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk - Sistem Pengurusan Stor Kerajaan ASM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/asm-logo-emblem.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
        
        <!-- Left Branding Hero Banner -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-8 text-white flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-800">
            <div>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-white p-2 flex items-center justify-center shadow-lg border border-slate-100 shrink-0">
                        <img src="{{ asset('images/asm-logo-emblem.png') }}" alt="Logo Rasmi Akademi Sains Malaysia" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="text-lg font-bold tracking-wider text-white">AKADEMI SAINS MALAYSIA</div>
                        <div class="text-xs text-amber-400 font-semibold tracking-wide uppercase">Agensi Di Bawah MOSTI</div>
                    </div>
                </div>

                <h2 class="text-2xl font-black text-white leading-tight mb-3">
                    Sistem Pengurusan Stor Kerajaan
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Mematuhi sepenuhnya tatacara dan garis panduan dalam <strong>Pekeliling Perbendaharaan Malaysia AM 6.1 hingga AM 6.10</strong> bagi penerimaan, perekodan, pengeluaran, verifikasi, pelupusan, dan hapus kira stok.
                </p>

                <div class="space-y-2 mt-6">
                    <div class="flex items-center space-x-2 text-xs text-slate-300">
                        <span class="text-emerald-400">✓</span>
                        <span>Daftar Stok Digital <strong>KEW.PS-3</strong> & Kad Petak <strong>KEW.PS-4</strong></span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs text-slate-300">
                        <span class="text-emerald-400">✓</span>
                        <span>Enjin Transaksi & Baki Automatik (Imutabiliti)</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs text-slate-300">
                        <span class="text-emerald-400">✓</span>
                        <span>Semua 36 Borang Rasmi <strong>KEW.PS-1 hingga KEW.PS-36</strong></span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs text-slate-300">
                        <span class="text-emerald-400">✓</span>
                        <span>Kadar Pusingan Stok <strong>KEW.PS-14</strong> (Sasaran ≥ 4.0)</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 text-[11px] text-slate-400">
                Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur
            </div>
        </div>

        <!-- Right Login & Quick Access Panel -->
        <div class="p-8 flex flex-col justify-between bg-slate-50">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Log Masuk Pengguna</h3>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-700">Akses Rasmi</span>
                </div>

                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-300 text-rose-700 px-3 py-2 rounded text-xs mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Emel</label>
                        <input type="email" name="email" value="{{ old('email', 'stor@asm.gov.my') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Laluan</label>
                        <input type="password" name="password" value="password123" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold text-sm shadow transition">
                        Log Masuk Sistem
                    </button>
                </form>

                <!-- 1-Click Quick Demo Role Switcher -->
                <div class="mt-6 pt-5 border-t border-slate-200">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                        ⚡ Akses Pantas Mengikut Peranan (1-Klik):
                    </div>
                    <div class="grid grid-cols-2 gap-1.5 text-xs">
                        <a href="{{ route('auth.switch-role', 'admin') }}" class="px-2 py-1.5 rounded bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium text-center truncate">1. System Admin</a>
                        <a href="{{ route('auth.switch-role', 'ketua_jabatan') }}" class="px-2 py-1.5 rounded bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium text-center truncate">2. Ketua Jabatan</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_stor') }}" class="px-2 py-1.5 rounded bg-blue-100 hover:bg-blue-200 text-blue-900 font-semibold text-center truncate">3. Pegawai Stor</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_penerima') }}" class="px-2 py-1.5 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-medium text-center truncate">4. Pegawai Penerima</a>
                        <a href="{{ route('auth.switch-role', 'pegawai_pelulus') }}" class="px-2 py-1.5 rounded bg-amber-100 hover:bg-amber-200 text-amber-900 font-medium text-center truncate">5. Pegawai Pelulus</a>
                        <a href="{{ route('auth.switch-role', 'pemverifikasi') }}" class="px-2 py-1.5 rounded bg-purple-100 hover:bg-purple-200 text-purple-900 font-medium text-center truncate">6. Pemverifikasi</a>
                        <a href="{{ route('auth.switch-role', 'urus_setia_pelupusan') }}" class="px-2 py-1.5 rounded bg-red-100 hover:bg-red-200 text-red-900 font-medium text-center truncate">7. Urus Setia Pelupusan</a>
                        <a href="{{ route('auth.switch-role', 'urus_setia_kehilangan') }}" class="px-2 py-1.5 rounded bg-rose-100 hover:bg-rose-200 text-rose-900 font-medium text-center truncate">8. Urus Setia Kehilangan</a>
                        <a href="{{ route('auth.switch-role', 'pemohon') }}" class="col-span-2 px-2 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white font-medium text-center truncate">9. Pemohon / Staff (Buat Pesanan)</a>
                    </div>
                </div>
            </div>

            <div class="text-[11px] text-slate-400 text-center mt-4">
                Kata laluan lalai untuk semua akaun demo: <code class="text-blue-600 font-bold">password123</code>
            </div>
        </div>
    </div>
</body>
</html>
