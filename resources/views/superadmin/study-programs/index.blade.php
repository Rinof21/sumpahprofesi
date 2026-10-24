@extends('layouts.app')

@section('title', 'Master Program Studi')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Master Data Program Studi (Multi-Tenancy Engine)</h1>
    <p class="text-xs font-semibold text-gray-500">Penambahan program studi baru tanpa merombak skema database (FR-A1).</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Form Add Study Program -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-[#4e73df]"></i> Tambah Program Studi
        </h2>

        <form action="{{ route('superadmin.study-programs.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Prodi (Unik)</label>
                <input type="text" name="code" required placeholder="Contoh: KED, APT, NRS, GZ" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 uppercase font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Program Studi</label>
                <input type="text" name="name" required placeholder="Contoh: Profesi Dokter" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Gelar Lulusan</label>
                <input type="text" name="degree_title" required placeholder="Contoh: dr. / Apt. / Ns." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Organisasi Profesi</label>
                <input type="text" name="organization" required placeholder="Contoh: IDI / IAI / PPNI" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <button type="submit" class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">
                Simpan Program Studi
            </button>
        </form>
    </div>

    <!-- Study Programs Table -->
    <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Daftar Master Program Studi Aktif</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2.5 w-12 text-center">No.</th>
                        <th class="px-4 py-2.5">Kode & Nama Prodi</th>
                        <th class="px-4 py-2.5">Gelar</th>
                        <th class="px-4 py-2.5">Organisasi Profesi</th>
                        <th class="px-4 py-2.5">Total Periode</th>
                        <th class="px-4 py-2.5">Admin Prodi</th>
                        <th class="px-4 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($studyPrograms as $sp)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2.5 text-center font-bold text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-2.5 font-bold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded bg-blue-100 text-[#4e73df] font-black flex items-center justify-center text-[10px]">
                                        {{ $sp->code }}
                                    </span>
                                    <span>{{ $sp->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 font-bold text-[#4e73df]">{{ $sp->degree_title }}</td>
                            <td class="px-4 py-2.5 text-gray-800">{{ $sp->organization }}</td>
                            <td class="px-4 py-2.5 font-semibold">{{ $sp->oath_periods_count }} Periode</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $sp->users_count }} Pengguna</td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <button type="button" 
                                    onclick="openEditProdiModal('{{ $sp->id }}', '{{ addslashes($sp->code) }}', '{{ addslashes($sp->name) }}', '{{ addslashes($sp->degree_title) }}', '{{ addslashes($sp->organization) }}')" 
                                    class="p-1.5 px-2.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 rounded text-xs font-bold transition-colors inline-flex items-center gap-1 shadow-2xs" 
                                    title="Edit Program Studi">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-400">Belum ada program studi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Edit Program Studi -->
<div id="modal-edit-prodi" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data Program Studi
            </h3>
            <button type="button" onclick="closeEditProdiModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form id="form-edit-prodi" method="POST" class="p-5 space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Prodi (Unik)</label>
                <input type="text" name="code" id="edit-prodi-code" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 uppercase font-bold focus:ring-1 focus:ring-[#4e73df]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Program Studi</label>
                <input type="text" name="name" id="edit-prodi-name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-1 focus:ring-[#4e73df]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Gelar Lulusan</label>
                <input type="text" name="degree_title" id="edit-prodi-degree" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-1 focus:ring-[#4e73df]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Organisasi Profesi</label>
                <input type="text" name="organization" id="edit-prodi-org" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-1 focus:ring-[#4e73df]">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditProdiModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white text-xs font-bold rounded shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditProdiModal(id, code, name, degree, org) {
        document.getElementById('form-edit-prodi').action = "{{ url('superadmin/study-programs') }}/" + id;
        document.getElementById('edit-prodi-code').value = code;
        document.getElementById('edit-prodi-name').value = name;
        document.getElementById('edit-prodi-degree').value = degree;
        document.getElementById('edit-prodi-org').value = org;
        document.getElementById('modal-edit-prodi').classList.remove('hidden');
    }

    function closeEditProdiModal() {
        document.getElementById('modal-edit-prodi').classList.add('hidden');
    }
</script>
@endpush
@endsection
