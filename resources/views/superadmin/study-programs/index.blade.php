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
                        <th class="px-4 py-2.5">Kode & Nama Prodi</th>
                        <th class="px-4 py-2.5">Gelar</th>
                        <th class="px-4 py-2.5">Organisasi Profesi</th>
                        <th class="px-4 py-2.5">Total Periode</th>
                        <th class="px-4 py-2.5">Admin Prodi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($studyPrograms as $sp)
                        <tr class="hover:bg-gray-50">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada program studi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
