@extends('layouts.app')

@section('title', 'Direktori Meja Bantuan Terarah')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Direktori Meja Bantuan Terarah (Segmented Helpdesk)</h1>
    <p class="text-xs font-semibold text-gray-500 mt-1">Pilih saluran bantuan yang sesuai agar kendala Anda ditangani secara cepat & presisi.</p>
</div>

<!-- Segment 1: Meja Bantuan IT Fakultas -->
<div class="mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center font-bold text-base">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
        <div>
            <h2 class="text-sm font-bold text-gray-900">1. Meja Bantuan IT Fakultas</h2>
            <p class="text-[11px] text-gray-500">Khusus penanganan kendala reset password, gagal upload slide PPT, error e-ticket, dan bug sistem.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($itContacts as $it)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase mb-2 inline-block">
                        Tim IT Fakultas
                    </span>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $it->name }}</h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <div><i class="fa-solid fa-phone text-gray-400 w-4"></i> {{ $it->phone }}</div>
                        @if($it->email)
                            <div><i class="fa-solid fa-envelope text-gray-400 w-4"></i> {{ $it->email }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $it->phone) }}?text=Halo%20Tim%20IT%20FKIK,%20saya%20meminta%20bantuan%20teknis%20web" 
                       target="_blank" 
                       class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Chat WhatsApp Tim IT
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-6 rounded-lg text-center text-xs text-gray-400 border border-gray-200">
                Kontak IT Fakultas belum didaftarkan.
            </div>
        @endforelse
    </div>
</div>

<!-- Segment 2: Meja Bantuan Administrasi Prodi -->
<div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base">
            <i class="fa-solid fa-folder-open"></i>
        </div>
        <div>
            <h2 class="text-sm font-bold text-gray-900">2. Meja Bantuan Administrasi & Panitia Prodi</h2>
            <p class="text-[11px] text-gray-500">Khusus konfirmasi berkas kelulusan, legalisir, ketentuan busana/toga, dan koordinasi rohaniwan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($prodiContacts as $pc)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase">
                            {{ $pc->studyProgram->name }} ({{ $pc->studyProgram->code }})
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $pc->name }}</h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <div><i class="fa-solid fa-phone text-gray-400 w-4"></i> {{ $pc->phone }}</div>
                        @if($pc->email)
                            <div><i class="fa-solid fa-envelope text-gray-400 w-4"></i> {{ $pc->email }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pc->phone) }}?text=Halo%20Panitia%20Sumpah%20{{ urlencode($pc->studyProgram->name) }},%20saya%20meminta%20informasi%20administrasi" 
                       target="_blank" 
                       class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Chat Panitia {{ $pc->studyProgram->code }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-6 rounded-lg text-center text-xs text-gray-400 border border-gray-200">
                Kontak administrasi prodi belum diaktifkan.
            </div>
        @endforelse
    </div>
</div>
@endsection
