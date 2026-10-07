@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Peran')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Manajemen Akun Pengguna & Spatie Permission Roles</h1>
    <p class="text-xs font-semibold text-gray-500">Pengelolaan peran hak akses: Superadmin, Admin Prodi, dan Peserta.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Form Add User -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-[#4e73df]"></i> Buat Akun Pengguna Baru
        </h2>

        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama Pengguna" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alamat Email</label>
                <input type="email" name="email" required placeholder="user@domain.ac.id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kata Sandi</label>
                <input type="password" name="password" required minlength="6" placeholder="••••••••" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Peran (Spatie Role)</label>
                <select name="role" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Program Studi (Admin / Peserta)</label>
                <select name="study_program_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    <option value="">-- Tanpa Prodi (Global Superadmin) --</option>
                    @foreach($studyPrograms as $sp)
                        <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">
                Buat Akun Pengguna
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Daftar Akun Pengguna Sistem</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2.5">Nama & Email</th>
                        <th class="px-4 py-2.5">Peran (Spatie Role)</th>
                        <th class="px-4 py-2.5">Program Studi</th>
                        <th class="px-4 py-2.5 text-right">Ubah Peran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-bold text-gray-900">
                                <div>{{ $u->name }}</div>
                                <div class="text-[10px] text-gray-400 font-normal">{{ $u->email }}</div>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border
                                    @if($u->hasRole('Superadmin')) bg-red-100 text-red-700 border-red-200
                                    @elseif($u->hasRole('Admin Prodi')) bg-emerald-100 text-emerald-700 border-emerald-200
                                    @else bg-blue-100 text-blue-700 border-blue-200 @endif">
                                    {{ $u->roles->first()?->name ?? 'Tanpa Role' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 font-semibold text-gray-800">
                                {{ $u->studyProgram ? $u->studyProgram->name : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <form action="{{ route('superadmin.users.update-role', $u->id) }}" method="POST" class="inline-flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="px-2 py-1 bg-gray-50 border border-gray-300 rounded text-[11px] text-gray-800">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $u->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-400">Belum ada akun pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
