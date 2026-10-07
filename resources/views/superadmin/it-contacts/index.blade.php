@extends('layouts.app')

@section('title', 'Narahubung IT Fakultas')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Narahubung IT Fakultas (Meja Bantuan Terarah)</h1>
    <p class="text-xs font-semibold text-gray-500">Kontak person IT Fakultas untuk penanganan kendala reset password, upload PPT, & bug sistem.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Form Add IT Contact -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-headset text-cyan-600"></i> Tambah Personil IT
        </h2>

        <form action="{{ route('superadmin.it-contacts.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Personil / Tim IT</label>
                <input type="text" name="name" required placeholder="Contoh: Tim IT Fakultas" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor WhatsApp (62...)</label>
                <input type="text" name="phone" required placeholder="6281234567890" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Support IT</label>
                <input type="email" name="email" placeholder="helpdesk-it@fkik.ac.id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <button type="submit" class="w-full py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded shadow-sm">
                Simpan Personil IT
            </button>
        </form>
    </div>

    <!-- IT Contacts Table -->
    <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Daftar Personil Meja Bantuan IT</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2.5">Nama Personil</th>
                        <th class="px-4 py-2.5">WhatsApp</th>
                        <th class="px-4 py-2.5">Email Support</th>
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
                                    {{ $cont->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <form action="{{ route('superadmin.it-contacts.toggle', $cont->id) }}" method="POST">
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
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada personil IT.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
