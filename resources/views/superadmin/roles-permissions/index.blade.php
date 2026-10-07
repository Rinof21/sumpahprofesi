@extends('layouts.app')

@section('title', 'Manajemen Peran & Izin Akses (Spatie Permission)')

@section('content')
<!-- Header Page -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <a href="{{ route('superadmin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Superadmin
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Manajemen Peran & Izin Akses (Spatie Permission)</h1>
        <p class="text-xs font-semibold text-gray-500">Kelola daftar Peran (Roles), Hak Izin Akses (Permissions), dan pemetaan hak akses pengguna secara terpusat.</p>
    </div>
    
    <div class="flex items-center gap-2">
        <button type="button" onclick="openModal('modal-tambah-role')" class="px-3.5 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white text-xs font-bold rounded-lg shadow-sm flex items-center gap-2 transition-all">
            <i class="fa-solid fa-plus-circle"></i> Tambah Peran Baru
        </button>
        <button type="button" onclick="openModal('modal-tambah-permission')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center gap-2 transition-all">
            <i class="fa-solid fa-key"></i> Tambah Izin Akses Baru
        </button>
    </div>
</div>

<!-- Summary Cards (SB Admin 2 Style) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg border-l-4 border-l-[#4e73df] border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[11px] font-extrabold text-[#4e73df] uppercase tracking-wider">TOTAL PERAN (ROLES)</div>
            <div class="text-2xl font-black text-gray-800 mt-0.5">{{ $roles->count() }}</div>
            <div class="text-[10px] text-gray-500 font-semibold">Tersimpan dalam database</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 text-[#4e73df] flex items-center justify-center text-lg">
            <i class="fa-solid fa-user-shield"></i>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg border-l-4 border-l-emerald-500 border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[11px] font-extrabold text-emerald-600 uppercase tracking-wider">TOTAL IZIN AKSES (PERMISSIONS)</div>
            <div class="text-2xl font-black text-gray-800 mt-0.5">{{ $permissions->count() }}</div>
            <div class="text-[10px] text-gray-500 font-semibold">Fitur & kontrol aplikasi</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-key"></i>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg border-l-4 border-l-purple-500 border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-[11px] font-extrabold text-purple-600 uppercase tracking-wider">TOTAL PENGGUNA TERHUBUNG</div>
            <div class="text-2xl font-black text-gray-800 mt-0.5">{{ $roles->sum('users_count') }}</div>
            <div class="text-[10px] text-gray-500 font-semibold">Memiliki peran khusus</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-users font-extrabold"></i>
        </div>
    </div>
</div>

<!-- Main Section: Roles & Permissions Grid -->
<div class="space-y-8">
    
    <!-- Table 1: Daftar Peran (Roles) & Modifikasi Permission -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-[#4e73df]"></i> Daftar Peran Hak Akses (Roles)
            </h2>
            <span class="text-xs text-gray-500 font-medium">Klik <strong>Edit Peran</strong> untuk mengatur ulang kombinasi izin akses (permissions).</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 text-gray-600 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-12">No</th>
                        <th class="px-4 py-3">Nama Peran (Role Name)</th>
                        <th class="px-4 py-3">Jumlah Pengguna</th>
                        <th class="px-4 py-3">Daftar Izin Akses (Granted Permissions)</th>
                        <th class="px-4 py-3 text-right">Aksi & Pengaturan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($roles as $index => $r)
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 font-extrabold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded text-xs uppercase font-black tracking-wider border
                                        @if($r->name === 'Superadmin') bg-red-100 text-red-700 border-red-200
                                        @elseif($r->name === 'Admin Prodi') bg-emerald-100 text-emerald-700 border-emerald-200
                                        @elseif($r->name === 'Peserta') bg-blue-100 text-blue-700 border-blue-200
                                        @else bg-gray-100 text-gray-700 border-gray-300 @endif">
                                        {{ $r->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-800">
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 border border-gray-200">
                                    <i class="fa-solid fa-user text-[10px] mr-1 text-gray-400"></i>{{ $r->users_count }} Akun
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1 max-w-xl">
                                    @forelse($r->permissions as $perm)
                                        <span class="px-2 py-0.5 bg-blue-50 text-[#4e73df] border border-blue-200 rounded text-[10px] font-bold">
                                            <i class="fa-solid fa-check text-[9px] mr-0.5"></i>{{ $perm->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 italic text-[11px]">Belum ada izin akses yang disematkan</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openEditRoleModal('{{ $r->id }}', '{{ $r->name }}', {{ json_encode($r->permissions->pluck('name')) }})" class="px-2.5 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold rounded text-xs transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit Peran
                                    </button>
                                    @if(!in_array($r->name, ['Superadmin', 'Admin Prodi', 'Peserta']))
                                        <form action="{{ route('superadmin.roles.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran {{ $r->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold rounded text-xs transition-colors" title="Hapus Peran">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400 font-medium">Belum ada peran terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table 2: Daftar Izin Akses (Permissions) Master -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-key text-emerald-600"></i> Master Hak Izin Akses (Permissions)
            </h2>
            <span class="text-xs text-gray-500 font-medium">Koleksi seluruh izin granular yang tersedia di dalam sistem Spatie.</span>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($permissions as $perm)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between hover:bg-white hover:shadow-sm transition-all group">
                        <div class="min-w-0 pr-2">
                            <div class="font-extrabold text-xs text-gray-900 truncate flex items-center gap-1.5">
                                <i class="fa-solid fa-unlock-keyhole text-emerald-600 text-[11px]"></i>
                                <span>{{ $perm->name }}</span>
                            </div>
                            <div class="text-[10px] text-gray-500 mt-1 flex flex-wrap gap-1">
                                <span class="font-bold text-gray-400">Dipakai oleh:</span>
                                @forelse($perm->roles as $rRole)
                                    <span class="px-1.5 py-0.2 bg-gray-200 text-gray-700 rounded text-[9px] font-semibold">{{ $rRole->name }}</span>
                                @empty
                                    <span class="text-gray-400 italic text-[9px]">Belum dipakai</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0 opacity-80 group-hover:opacity-100 transition-opacity">
                            <button type="button" onclick="openEditPermissionModal('{{ $perm->id }}', '{{ $perm->name }}')" class="p-1.5 text-gray-500 hover:text-yellow-700 hover:bg-yellow-50 rounded" title="Edit Permission">
                                <i class="fa-solid fa-pencil text-xs"></i>
                            </button>
                            <form action="{{ route('superadmin.permissions.destroy', $perm->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus izin akses {{ $perm->name }}?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Hapus Permission">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-4 text-center text-gray-400 text-xs">Belum ada izin akses yang terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Table 3: Penugasan Hak Akses Khusus Pengguna (User Level Custom Permissions) -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-user-gear text-purple-600"></i> Penugasan Hak Akses Pengguna
            </h2>
            <span class="text-xs text-gray-500 font-medium">Ubah Peran (Role) dan Sematkan Izin Akses Khusus (Direct Permissions) per Akun.</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 text-gray-600 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">Nama Pengguna & Email</th>
                        <th class="px-4 py-3">Program Studi</th>
                        <th class="px-4 py-3">Peran (Role Utama)</th>
                        <th class="px-4 py-3">Izin Akses Khusus (Direct Permissions)</th>
                        <th class="px-4 py-3 text-right">Kelola Akses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-900">
                                <div>{{ $u->name }}</div>
                                <div class="text-[10px] text-gray-400 font-normal">{{ $u->email }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-gray-700">
                                {{ $u->studyProgram ? $u->studyProgram->name : '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border
                                    @if($u->hasRole('Superadmin')) bg-red-100 text-red-700 border-red-200
                                    @elseif($u->hasRole('Admin Prodi')) bg-emerald-100 text-emerald-700 border-emerald-200
                                    @else bg-blue-100 text-blue-700 border-blue-200 @endif">
                                    {{ $u->roles->first()?->name ?? 'Tanpa Role' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($u->permissions as $dPerm)
                                        <span class="px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 rounded text-[10px] font-bold">
                                            <i class="fa-solid fa-star text-[9px] mr-0.5"></i>{{ $dPerm->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 italic text-[11px]">Tidak ada izin khusus</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <button type="button" onclick="openEditUserPermModal('{{ $u->id }}', '{{ $u->name }}', '{{ $u->roles->first()?->name }}', {{ json_encode($u->permissions->pluck('name')) }})" class="px-2.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-300 font-bold rounded text-xs transition-colors flex items-center gap-1 justify-end ml-auto">
                                    <i class="fa-solid fa-user-shield"></i> Kelola Hak Akses
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400 font-medium">Belum ada akun pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-5 py-3 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- ==================== MODAL DIALOGS (BAHASA INDONESIA) ==================== -->

<!-- Modal 1: Tambah Peran Baru -->
<div id="modal-tambah-role" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Tambah Peran Baru (Role)
            </h3>
            <button type="button" onclick="closeModal('modal-tambah-role')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form action="{{ route('superadmin.roles.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Peran Baru <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Panitia Penguji, Verifikator Keuangan" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pilih Izin Akses (Permissions)</label>
                <div class="max-h-48 overflow-y-auto p-3 bg-gray-50 border border-gray-200 rounded space-y-2">
                    @foreach($permissions as $perm)
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-800 cursor-pointer hover:bg-gray-100 p-1 rounded">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded text-[#4e73df] focus:ring-[#4e73df]">
                            <span>{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-tambah-role')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">Simpan Peran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Peran & Sync Permissions -->
<div id="modal-edit-role" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden">
        <div class="bg-yellow-600 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Peran & Hak Akses
            </h3>
            <button type="button" onclick="closeModal('modal-edit-role')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="form-edit-role" method="POST" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Peran <span class="text-red-500">*</span></label>
                <input type="text" id="edit-role-name" name="name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pengaturan Izin Akses (Permissions)</label>
                <div class="max-h-56 overflow-y-auto p-3 bg-gray-50 border border-gray-200 rounded space-y-2">
                    @foreach($permissions as $perm)
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-800 cursor-pointer hover:bg-gray-100 p-1 rounded">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="edit-role-perm-checkbox rounded text-[#4e73df] focus:ring-[#4e73df]">
                            <span>{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-role')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs rounded shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Tambah Permission Baru -->
<div id="modal-tambah-permission" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden">
        <div class="bg-emerald-600 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-key"></i> Tambah Master Izin Akses (Permission)
            </h3>
            <button type="button" onclick="closeModal('modal-tambah-permission')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form action="{{ route('superadmin.permissions.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Permission Baru <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: export-pdf-recap, view-audit-logs" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                <p class="text-[10px] text-gray-400 mt-1">Gunakan format lowercase dengan tanda hubung (kebab-case).</p>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-tambah-permission')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm">Tambah Permission</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4: Edit Permission -->
<div id="modal-edit-permission" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden">
        <div class="bg-gray-800 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen"></i> Edit Master Izin Akses
            </h3>
            <button type="button" onclick="closeModal('modal-edit-permission')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="form-edit-permission" method="POST" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Permission <span class="text-red-500">*</span></label>
                <input type="text" id="edit-permission-name" name="name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-permission')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs rounded shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 5: Edit User Direct Permissions & Role -->
<div id="modal-edit-user-perm" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden">
        <div class="bg-purple-700 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user-shield"></i> Kelola Hak Akses Pengguna
            </h3>
            <button type="button" onclick="closeModal('modal-edit-user-perm')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="form-edit-user-perm" method="POST" class="p-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Pengguna</label>
                <input type="text" id="edit-user-name" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded text-xs font-bold text-gray-700">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Peran Utama (Role) <span class="text-red-500">*</span></label>
                <select id="edit-user-role" name="role" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs font-bold text-gray-900">
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Izin Akses Khusus Langsung (Direct Permissions)</label>
                <div class="max-h-48 overflow-y-auto p-3 bg-gray-50 border border-gray-200 rounded space-y-2">
                    @foreach($permissions as $perm)
                        <label class="flex items-center gap-2 text-xs font-medium text-gray-800 cursor-pointer hover:bg-gray-100 p-1 rounded">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="edit-user-perm-checkbox rounded text-purple-600 focus:ring-purple-600">
                            <span>{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-user-perm')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs rounded shadow-sm">Simpan Hak Akses</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditRoleModal(id, name, permissions) {
        document.getElementById('form-edit-role').action = "{{ url('superadmin/roles') }}/" + id;
        document.getElementById('edit-role-name').value = name;
        
        const checkboxes = document.querySelectorAll('.edit-role-perm-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = permissions.includes(cb.value);
        });

        openModal('modal-edit-role');
    }

    function openEditPermissionModal(id, name) {
        document.getElementById('form-edit-permission').action = "{{ url('superadmin/permissions') }}/" + id;
        document.getElementById('edit-permission-name').value = name;

        openModal('modal-edit-permission');
    }

    function openEditUserPermModal(userId, userName, userRole, userPermissions) {
        document.getElementById('form-edit-user-perm').action = "{{ url('superadmin/users') }}/" + userId + "/permissions";
        document.getElementById('edit-user-name').value = userName;
        document.getElementById('edit-user-role').value = userRole;

        const checkboxes = document.querySelectorAll('.edit-user-perm-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = userPermissions.includes(cb.value);
        });

        openModal('modal-edit-user-perm');
    }
</script>
@endpush
@endsection
