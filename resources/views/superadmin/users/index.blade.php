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
                        <th class="px-3 py-2.5 w-12 text-center">No.</th>
                        <th class="px-4 py-2.5">Nama & Email</th>
                        <th class="px-4 py-2.5">Peran (Spatie Role)</th>
                        <th class="px-4 py-2.5">Program Studi</th>
                        <th class="px-4 py-2.5 text-right">Ubah Peran & Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2.5 text-center font-bold text-gray-500">
                                {{ $users->firstItem() + $index }}
                            </td>
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
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <form action="{{ route('superadmin.users.update-role', $u->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" onchange="this.form.submit()" class="px-2 py-1 bg-gray-50 border border-gray-300 rounded text-[11px] text-gray-800 font-medium focus:ring-1 focus:ring-[#4e73df]" title="Ubah Peran">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $u->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <button type="button" 
                                        onclick="openResetPasswordModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')" 
                                        class="p-1.5 px-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded text-xs font-bold transition-colors inline-flex items-center gap-1 shadow-2xs" 
                                        title="Reset Kata Sandi Pengguna">
                                        <i class="fa-solid fa-key"></i>
                                        <span class="hidden sm:inline">Reset Pass</span>
                                    </button>

                                    @if(auth()->id() !== $u->id)
                                        <form action="{{ route('superadmin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna {{ $u->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 px-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded text-xs font-bold transition-colors" title="Hapus Pengguna">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="p-1.5 px-2 bg-gray-100 text-gray-400 border border-gray-200 rounded text-xs cursor-not-allowed" title="Tidak dapat menghapus akun Anda sendiri">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada akun pengguna.</td>
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

<!-- Modal Reset Password -->
<div id="modal-reset-password" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-amber-600 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-key"></i> Reset Kata Sandi Pengguna
            </h3>
            <button type="button" onclick="closeResetPasswordModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form id="form-reset-password" method="POST" class="p-5 space-y-3.5">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pengguna Terpilih</label>
                <div id="reset-user-info" class="text-xs font-bold text-gray-800 bg-gray-50 p-2.5 rounded border border-gray-200"></div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kata Sandi Baru</label>
                <input type="text" name="password" id="reset-password-input" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-mono font-bold focus:ring-1 focus:ring-amber-500">
                <div class="mt-2 flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('reset-password-input').value = 'password123'" class="text-[11px] font-bold text-amber-700 hover:underline inline-flex items-center gap-1">
                        <i class="fa-solid fa-magic-wand-sparkles"></i> Gunakan Default: password123
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded shadow-sm">Simpan Kata Sandi Baru</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openResetPasswordModal(id, name, email) {
        document.getElementById('form-reset-password').action = "{{ url('superadmin/users') }}/" + id + "/reset-password";
        document.getElementById('reset-user-info').innerText = name + ' (' + email + ')';
        document.getElementById('reset-password-input').value = '';
        document.getElementById('modal-reset-password').classList.remove('hidden');
    }

    function closeResetPasswordModal() {
        document.getElementById('modal-reset-password').classList.add('hidden');
    }
</script>
@endpush
@endsection
