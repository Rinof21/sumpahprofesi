@extends('layouts.app')

@section('title', 'Narahubung Admin Prodi')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Kelola Narahubung Meja Bantuan Admin Prodi</h1>
    <p class="text-xs font-semibold text-gray-500">Kontak person yang ditampilkan pada Meja Bantuan Administrasi Prodi.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Form Add Contact -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-[#4e73df]"></i> Tambah Narahubung
        </h2>

        <form action="{{ route('admin.contacts.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Personil Panitia</label>
                <input type="text" name="name" required placeholder="Contoh: Ibu Susi (Sekretariat Sumpah)" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor WhatsApp (62...)</label>
                <input type="text" name="phone" required placeholder="6281122334455" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Resmi (Opsional)</label>
                <input type="email" name="email" placeholder="admin@prodi.ac.id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked class="w-4 h-4 rounded border-gray-300 text-[#4e73df]">
                <label for="is_active" class="text-xs font-bold text-gray-700">Tayangkan di Portal & Dasbor</label>
            </div>

            <button type="submit" class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">
                Simpan Narahubung
            </button>
        </form>
    </div>

    <!-- Contacts Table -->
    <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Daftar Kontak Person Administrasi</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2.5">Nama Personil</th>
                        <th class="px-4 py-2.5">WhatsApp</th>
                        <th class="px-4 py-2.5">Email</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($contacts as $cont)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-bold text-gray-900">{{ $cont->name }}</td>
                            <td class="px-4 py-2.5 font-mono text-[#4e73df]">{{ $cont->phone }}</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $cont->email ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black border
                                    @if($cont->is_active) bg-emerald-100 text-emerald-700 border-emerald-200
                                    @else bg-gray-100 text-gray-600 border-gray-200 @endif">
                                    {{ $cont->is_active ? 'Aktif Tayang' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <form action="{{ route('admin.contacts.toggle', $cont->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 rounded border border-gray-300">
                                        Toggle Status
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada narahubung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
