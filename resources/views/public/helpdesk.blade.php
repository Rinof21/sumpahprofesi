@extends('layouts.app')

@section('title', 'Direktori Meja Bantuan Terarah')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Direktori Meja Bantuan Terarah (Segmented Helpdesk)</h1>
    <p class="text-xs font-semibold text-gray-500 mt-1">Pilih saluran bantuan yang sesuai agar kendala Anda ditangani secara cepat & presisi.</p>
</div>

<!-- Segment 1: Meja Bantuan IT Fakultas -->
<div class="mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">1. Meja Bantuan IT Fakultas</h2>
                <p class="text-[11px] text-gray-500">Khusus penanganan kendala reset password, gagal upload slide PPT, error e-ticket, dan bug sistem.</p>
            </div>
        </div>
        <span class="hidden sm:inline-flex px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-[#4e73df] border border-blue-200">
            {{ $itContacts->count() }} Kontak IT
        </span>
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
                        <div><i class="fa-solid fa-phone text-gray-400 w-4"></i> <span class="font-mono">{{ $it->phone }}</span></div>
                        @if($it->email)
                            <div><i class="fa-solid fa-envelope text-gray-400 w-4"></i> {{ $it->email }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $it->phone) }}?text=Halo%20Tim%20IT%20FKIK,%20saya%20meminta%20bantuan%20teknis%20web" 
                       target="_blank" 
                       class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2 transition-colors">
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
<div class="mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">2. Meja Bantuan Administrasi & Panitia Prodi</h2>
                <p class="text-[11px] text-gray-500">Khusus konfirmasi berkas kelulusan, legalisir, ketentuan busana/toga, dan koordinasi rohaniwan.</p>
            </div>
        </div>
        <span class="hidden sm:inline-flex px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-[#4e73df] border border-blue-200">
            {{ $prodiContacts->count() }} Panitia Prodi
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($prodiContacts as $pc)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                            {{ $pc->studyProgram->name }} ({{ $pc->studyProgram->code }})
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $pc->name }}</h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <div><i class="fa-solid fa-phone text-gray-400 w-4"></i> <span class="font-mono">{{ $pc->phone }}</span></div>
                        @if($pc->email)
                            <div><i class="fa-solid fa-envelope text-gray-400 w-4"></i> {{ $pc->email }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pc->phone) }}?text=Halo%20Panitia%20Sumpah%20{{ urlencode($pc->studyProgram->name) }},%20saya%20meminta%20informasi%20administrasi" 
                       target="_blank" 
                       class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2 transition-colors">
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

<!-- Segment 3: Rekomendasi Vendor Fotografer Ruangan -->
<div class="mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-camera"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">3. Rekomendasi Vendor Fotografer Ruangan (Dokumentasi & Studio)</h2>
                <p class="text-[11px] text-gray-500">Khusus rekomendasi kontak WhatsApp fotografer resmi & studio foto wisuda yang biasa disewa peserta sumpah profesi.</p>
            </div>
        </div>
        <span class="hidden sm:inline-flex px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-[#4e73df] border border-blue-200">
            {{ $photographerVendors->count() }} Rekomendasi
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($photographerVendors as $photo)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase flex items-center gap-1">
                            <i class="fa-solid fa-camera text-xs"></i> Fotografer
                        </span>
                        @if($photo->studyProgram)
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[9px] font-bold uppercase">
                                {{ $photo->studyProgram->code }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[9px] font-bold uppercase">
                                Semua Prodi
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $photo->name }}</h3>

                    <div class="text-xs text-gray-600 space-y-1 mb-2">
                        @if($photo->contact_person)
                            <div><i class="fa-solid fa-user-tie text-gray-400 w-4"></i> PIC: {{ $photo->contact_person }}</div>
                        @endif
                        <div><i class="fa-solid fa-phone text-gray-400 w-4"></i> <span class="font-mono">{{ $photo->whatsapp_number }}</span></div>
                        @if($photo->price_estimate)
                            <div><i class="fa-solid fa-tags text-gray-400 w-4"></i> <span class="font-bold text-gray-800">{{ $photo->price_estimate }}</span></div>
                        @endif
                        @if($photo->instagram_handle)
                            <div>
                                <i class="fa-brands fa-instagram text-gray-400 w-4"></i>
                                <a href="https://instagram.com/{{ ltrim($photo->instagram_handle, '@') }}" target="_blank" class="text-[#4e73df] hover:underline font-semibold">
                                    {{ $photo->instagram_handle }}
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($photo->description)
                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed pt-1 border-t border-gray-100">
                            {{ $photo->description }}
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="{{ $photo->whatsapp_url }}" 
                       target="_blank" 
                       class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Chat WhatsApp Fotografer
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-6 rounded-lg text-center text-xs text-gray-400 border border-gray-200">
                Belum ada rekomendasi kontak fotografer yang didaftarkan.
            </div>
        @endforelse
    </div>
</div>

<!-- Segment 4: Rekomendasi Jasa Streaming Live YouTube -->
<div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center font-bold text-base">
                <i class="fa-brands fa-youtube"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">4. Rekomendasi Jasa Streaming Live YouTube (Siaran Langsung)</h2>
                <p class="text-[11px] text-gray-500">Khusus rekomendasi kontak WhatsApp penyedia jasa siaran langsung YouTube multi-kamera untuk keluarga dan kerabat peserta.</p>
            </div>
        </div>
        <span class="hidden sm:inline-flex px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-[#4e73df] border border-blue-200">
            {{ $streamingVendors->count() }} Rekomendasi
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($streamingVendors as $stream)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase flex items-center gap-1">
                            <i class="fa-solid fa-video text-xs"></i> Live Streaming
                        </span>
                        @if($stream->studyProgram)
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[9px] font-bold uppercase">
                                {{ $stream->studyProgram->code }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[9px] font-bold uppercase">
                                Semua Prodi
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $stream->name }}</h3>

                    <div class="text-xs text-gray-600 space-y-1 mb-2">
                        @if($stream->contact_person)
                            <div><i class="fa-solid fa-user-tie text-gray-400 w-4"></i> PIC: {{ $stream->contact_person }}</div>
                        @endif
                        <div><i class="fa-solid fa-phone text-gray-400 w-4"></i> <span class="font-mono">{{ $stream->whatsapp_number }}</span></div>
                        @if($stream->price_estimate)
                            <div><i class="fa-solid fa-tags text-gray-400 w-4"></i> <span class="font-bold text-gray-800">{{ $stream->price_estimate }}</span></div>
                        @endif
                        @if($stream->youtube_channel)
                            <div>
                                <i class="fa-brands fa-youtube text-gray-400 w-4"></i>
                                <a href="{{ $stream->youtube_channel }}" target="_blank" class="text-[#4e73df] hover:underline font-semibold">
                                    Channel YouTube &rarr;
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($stream->description)
                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed pt-1 border-t border-gray-100">
                            {{ $stream->description }}
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100">
                    <a href="{{ $stream->whatsapp_url }}" 
                       target="_blank" 
                       class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Chat WhatsApp Streamer
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-6 rounded-lg text-center text-xs text-gray-400 border border-gray-200">
                Belum ada rekomendasi kontak jasa live streaming YouTube yang didaftarkan.
            </div>
        @endforelse
    </div>
</div>
@endsection
