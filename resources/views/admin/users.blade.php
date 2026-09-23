@extends('layouts.app')

@section('title', 'Pengurusan Pengguna & Peranan (RBAC)')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 text-xs font-bold rounded bg-slate-100 text-slate-800 border border-slate-300">Pentadbiran Sistem</span>
                <span class="text-xs text-slate-500">Kawalan Akses 9 Peranan TPS</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-1">Pengurusan Pengguna & Peranan Stor</h1>
            <p class="text-sm text-slate-600">Pengurusan akaun pengguna dan penetapan had kuasa mengikut peranan di bawah Tatacara Pengurusan Stor Kerajaan.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="showModal = true" class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Daftar Pengguna Baru
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, emel, no. staf..." class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-56">
                <select name="role_id" class="w-full text-xs border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Peranan --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                Tapis
            </button>
            @if(request()->filled('search') || request()->filled('role_id'))
            <a href="{{ route('admin.users') }}" class="text-xs text-rose-600 hover:underline">
                Kosongkan Tapisan
            </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <th class="py-3 px-4">Nama Pegawai & ID</th>
                        <th class="py-3 px-4">Emel Rasmi</th>
                        <th class="py-3 px-4">Jawatan & Bahagian</th>
                        <th class="py-3 px-4">Peranan TPS</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-800">{{ $user->name }}</div>
                            <div class="text-xs font-mono text-slate-400">{{ $user->staff_id }}</div>
                        </td>
                        <td class="py-3 px-4 text-xs font-mono text-slate-600">
                            {{ $user->email }}
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <div class="font-semibold text-slate-800">{{ $user->position }}</div>
                            <div class="text-slate-400">{{ $user->department }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-800 border border-blue-200">
                                {{ $user->role->name ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($user->is_active)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-rose-100 text-rose-800">Nyahaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" onclick="return confirm('Tukar status akaun pengguna ini?')" class="text-xs font-semibold {{ $user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                    {{ $user->is_active ? 'Nyahaktif' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            Tiada rekod pengguna dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Daftar Pengguna Baru -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full p-6 space-y-4" @click.away="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Daftar Pengguna Baru</h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Penuh <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Staf <span class="text-rose-500">*</span></label>
                        <input type="text" name="staff_id" required class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Peranan TPS <span class="text-rose-500">*</span></label>
                        <select name="role_id" required class="w-full text-sm border-slate-300 rounded-lg font-medium">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Emel Rasmi ASM <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="nama@asm.gov.my" class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jawatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="position" required class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bahagian / Unit <span class="text-rose-500">*</span></label>
                        <input type="text" name="department" required class="w-full text-sm border-slate-300 rounded-lg">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Laluan Awal <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required value="password123" class="w-full text-sm border-slate-300 rounded-lg font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow transition">
                        Daftar Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
