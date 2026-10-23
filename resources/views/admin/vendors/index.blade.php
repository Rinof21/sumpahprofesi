@extends('layouts.app')

@php
    $isSuperadmin = auth()->user()->hasRole('Superadmin');
    $storeRoute = $isSuperadmin ? route('superadmin.vendors.store') : route('admin.vendors.store');
    $backRoute = $isSuperadmin ? route('superadmin.dashboard') : route('admin.dashboard');
    $indexRoute = $isSuperadmin ? route('superadmin.vendors.index') : route('admin.vendors.index');
@endphp

@section('title', 'Direktori Vendor Fotografer & Streaming Live YouTube')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <a href="{{ $backRoute }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-2xs">
                <i class="fa-solid fa-video"></i>
            </span>
            <span>Rekomendasi Vendor Fotografer & Streaming Live YouTube</span>
        </h1>
        <p class="text-xs font-semibold text-gray-500 mt-1 max-w-3xl leading-relaxed">
            Kontak WhatsApp fotografer dan penyedia jasa live streaming YouTube terpercaya yang biasa disewa peserta. Disimpan permanen sebagai referensi rekomendasi berkelanjutan lintas periode (tidak dibatasi per periode).
        </p>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <button type="button" onclick="openModal('modal-create-vendor')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-sm flex items-center gap-2 transition-all">
            <i class="fa-solid fa-plus-circle text-sm"></i>
            <span>Tambah Vendor Rekomendasi</span>
        </button>
    </div>
</div>

<!-- Alert Notifications -->
@if(session('success'))
    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-check-circle text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-black text-lg leading-none">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl border border-red-200 text-red-900 text-xs font-bold flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-red-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 font-black text-lg leading-none">&times;</button>
    </div>
@endif

<!-- Metric KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
        <div>
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Total Vendor</div>
            <div class="text-2xl font-black text-gray-900 mt-0.5">{{ $vendors->count() }}</div>
            <div class="text-[10px] font-semibold text-gray-500 mt-0.5">Tersimpan dalam database</div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#4e73df] flex items-center justify-center text-lg">
            <i class="fa-solid fa-address-book"></i>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
        <div>
            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-wider">Fotografer Ruangan</div>
            <div class="text-2xl font-black text-emerald-700 mt-0.5">{{ $photographerVendorsCount }}</div>
            <div class="text-[10px] font-semibold text-gray-500 mt-0.5">Dokumentasi & studio</div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-camera"></i>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
        <div>
            <div class="text-[10px] font-black text-red-600 uppercase tracking-wider">Live Streaming Youtube</div>
            <div class="text-2xl font-black text-red-700 mt-0.5">{{ $streamingVendorsCount }}</div>
            <div class="text-[10px] font-semibold text-gray-500 mt-0.5">Multi-cam & broadcast</div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
            <i class="fa-brands fa-youtube"></i>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
        <div>
            <div class="text-[10px] font-black text-purple-600 uppercase tracking-wider">Status Aktif</div>
            <div class="text-2xl font-black text-purple-700 mt-0.5">{{ $vendors->where('is_active', true)->count() }}</div>
            <div class="text-[10px] font-semibold text-gray-500 mt-0.5">Tampil di portal rekomendasi</div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="bg-white p-3 rounded-xl border border-gray-200 shadow-2xs mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
        <a href="{{ $indexRoute }}" class="px-3.5 py-1.5 rounded-lg text-xs font-black transition-all flex items-center gap-1.5 shrink-0 {{ empty($category) ? 'bg-[#4e73df] text-white shadow-2xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-solid fa-border-all"></i>
            <span>Semua Vendor</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ empty($category) ? 'bg-white/30 text-white' : 'bg-gray-200 text-gray-800' }}">{{ $vendors->count() }}</span>
        </a>

        <a href="{{ $indexRoute }}?category=photographer" class="px-3.5 py-1.5 rounded-lg text-xs font-black transition-all flex items-center gap-1.5 shrink-0 {{ $category === 'photographer' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-solid fa-camera"></i>
            <span>Fotografer Ruangan</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $category === 'photographer' ? 'bg-white/30 text-white' : 'bg-gray-200 text-gray-800' }}">{{ $photographerVendorsCount }}</span>
        </a>

        <a href="{{ $indexRoute }}?category=live_streaming" class="px-3.5 py-1.5 rounded-lg text-xs font-black transition-all flex items-center gap-1.5 shrink-0 {{ $category === 'live_streaming' ? 'bg-red-600 text-white shadow-2xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-brands fa-youtube"></i>
            <span>Live Streaming YouTube</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $category === 'live_streaming' ? 'bg-white/30 text-white' : 'bg-gray-200 text-gray-800' }}">{{ $streamingVendorsCount }}</span>
        </a>
    </div>

    <div class="text-[11px] font-bold text-gray-500 flex items-center gap-1.5">
        <i class="fa-solid fa-circle-info text-[#4e73df]"></i>
        <span>Klik tombol WA untuk tes chat langsung.</span>
    </div>
</div>

<!-- Vendor Grid Cards -->
@if($vendors->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-2xs p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-2xl mx-auto mb-3">
            <i class="fa-solid fa-camera-slash"></i>
        </div>
        <h3 class="text-base font-black text-gray-900">Belum Ada Kontak Vendor Terdaftar</h3>
        <p class="text-xs font-semibold text-gray-500 max-w-md mx-auto mt-1 leading-relaxed">
            Silakan masukkan kontak WhatsApp fotografer atau penyedia jasa streaming live YouTube yang biasa menjadi langganan peserta sumpah profesi.
        </p>
        <button type="button" onclick="openModal('modal-create-vendor')" class="mt-4 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-sm inline-flex items-center gap-2">
            <i class="fa-solid fa-plus-circle"></i> Daftarkan Vendor Pertama
        </button>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($vendors as $vendor)
            <div class="bg-white rounded-xl border {{ $vendor->is_active ? 'border-gray-200' : 'border-dashed border-gray-300 opacity-75' }} shadow-2xs p-5 flex flex-col justify-between hover:shadow-md hover:border-gray-300 transition-all group">
                <div>
                    <!-- Top Category & Status Header -->
                    <div class="flex items-center justify-between gap-2 pb-3 mb-3 border-b border-gray-100">
                        <div class="flex items-center gap-1.5">
                            @if($vendor->category === 'live_streaming')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-50 text-red-700 border border-red-200">
                                    <i class="fa-brands fa-youtube text-red-600"></i> Live Streaming
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-camera text-emerald-600"></i> Fotografer
                                </span>
                            @endif

                            @if($vendor->studyProgram)
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-[#4e73df] border border-blue-200 text-[10px] font-black uppercase">
                                    {{ $vendor->studyProgram->code }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-black">
                                    Semua Prodi
                                </span>
                            @endif
                        </div>

                        <!-- Active Toggle Form -->
                        <form action="{{ $isSuperadmin ? route('superadmin.vendors.toggle', $vendor->id) : route('admin.vendors.toggle', $vendor->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-2 py-0.5 rounded-full text-[10px] font-black transition-all flex items-center gap-1 {{ $vendor->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}" title="Klik untuk ubah status aktif/nonaktif">
                                <span class="w-1.5 h-1.5 rounded-full {{ $vendor->is_active ? 'bg-emerald-600 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ $vendor->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </div>

                    <!-- Vendor Name & Contact Person -->
                    <div class="mb-3">
                        <h3 class="text-base font-black text-gray-900 group-hover:text-[#4e73df] transition-colors leading-tight">
                            {{ $vendor->name }}
                        </h3>
                        @if($vendor->contact_person)
                            <div class="text-xs font-semibold text-gray-600 mt-0.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-tie text-gray-400 text-[11px]"></i>
                                <span>PIC: <strong>{{ $vendor->contact_person }}</strong></span>
                            </div>
                        @endif
                    </div>

                    <!-- WhatsApp & Contact Info Box -->
                    <div class="p-3 rounded-xl bg-gray-50/80 border border-gray-100 mb-3 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-semibold text-[11px]">WhatsApp:</span>
                            <span class="font-mono font-black text-gray-900">{{ $vendor->whatsapp_number }}</span>
                        </div>

                        @if($vendor->price_estimate)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 font-semibold text-[11px]">Estimasi Tarif:</span>
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">{{ $vendor->price_estimate }}</span>
                            </div>
                        @endif

                        @if($vendor->instagram_handle)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 font-semibold text-[11px]">Instagram:</span>
                                <a href="https://instagram.com/{{ ltrim($vendor->instagram_handle, '@') }}" target="_blank" class="font-bold text-pink-600 hover:underline flex items-center gap-1 text-[11px]">
                                    <i class="fa-brands fa-instagram"></i>
                                    <span>{{ $vendor->instagram_handle }}</span>
                                </a>
                            </div>
                        @endif

                        @if($vendor->youtube_channel)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 font-semibold text-[11px]">Channel YouTube:</span>
                                <a href="{{ $vendor->youtube_channel }}" target="_blank" class="font-bold text-red-600 hover:underline flex items-center gap-1 text-[11px]">
                                    <i class="fa-brands fa-youtube"></i>
                                    <span>Buka Channel &rarr;</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Description / Catatan Layanan -->
                    @if($vendor->description)
                        <p class="text-xs text-gray-600 font-medium leading-relaxed mb-4 line-clamp-3">
                            {{ $vendor->description }}
                        </p>
                    @endif
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                    <a href="{{ $vendor->whatsapp_url }}" target="_blank" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs text-center flex items-center justify-center gap-1.5 transition-all">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Chat WhatsApp</span>
                    </a>

                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openEditModal({{ json_encode($vendor) }})" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 flex items-center justify-center text-xs transition-colors" title="Edit Vendor">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <form action="{{ $isSuperadmin ? route('superadmin.vendors.destroy', $vendor->id) : route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor rekomendasi ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-700 flex items-center justify-center text-xs transition-colors" title="Hapus Vendor">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Modal Tambah Vendor Rekomendasi Baru -->
<div id="modal-create-vendor" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-emerald-600 px-5 py-4 text-white flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Vendor Rekomendasi Baru
                </h3>
                <p class="text-[11px] text-emerald-100 font-semibold mt-0.5">
                    Data kontak WhatsApp fotografer atau jasa live streaming YouTube
                </p>
            </div>
            <button type="button" onclick="closeModal('modal-create-vendor')" class="text-white/80 hover:text-white text-xl font-bold leading-none">&times;</button>
        </div>

        <form action="{{ $storeRoute }}" method="POST" class="p-5 space-y-3.5 overflow-y-auto flex-1">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Kategori Layanan <span class="text-red-500">*</span>
                </label>
                <select name="category" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-bold focus:ring-2 focus:ring-emerald-500">
                    <option value="photographer">📷 Fotografer Ruangan (Foto & Cetak)</option>
                    <option value="live_streaming">▶️ Jasa Streaming Live Youtube (Multi-cam Broadcast)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Nama Studio / Vendor / Jasa <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="Contoh: Pontianak Visual Studio" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-bold focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Kontak Person (PIC)
                    </label>
                    <input type="text" name="contact_person" placeholder="Nama PIC / Admin" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="whatsapp_number" required placeholder="0812... / 628..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-mono font-bold focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Estimasi Kisaran Tarif
                    </label>
                    <input type="text" name="price_estimate" placeholder="Contoh: Rp 1.500.000 / Sesi" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Akun Instagram
                    </label>
                    <input type="text" name="instagram_handle" placeholder="@namavendor" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Channel / Link Portofolio YouTube
                </label>
                <input type="url" name="youtube_channel" placeholder="https://youtube.com/@channel" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500">
            </div>

            @if($isSuperadmin)
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Alokasi Program Studi
                    </label>
                    <select name="study_program_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-bold focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Umum (Tersedia Untuk Seluruh Prodi) --</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="flex items-center gap-2 p-2.5 rounded-lg bg-gray-50 border border-gray-200">
                    <input type="checkbox" name="is_global" id="create_is_global" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <label for="create_is_global" class="text-xs font-semibold text-gray-700 cursor-pointer">
                        Bagikan ke seluruh program studi (Umum Fakultas)
                    </label>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Deskripsi Layanan & Catatan
                </label>
                <textarea name="description" rows="3" placeholder="Jelaskan fasilitas: kamera, lighting, cetak langsung, multi-cam, sound system, dsb." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-create-vendor')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-sm flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Vendor Rekomendasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Vendor Rekomendasi -->
<div id="modal-edit-vendor" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Vendor Rekomendasi
                </h3>
                <p class="text-[11px] text-blue-100 font-semibold mt-0.5">
                    Perbarui kontak WhatsApp atau portofolio vendor
                </p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-vendor')" class="text-white/80 hover:text-white text-xl font-bold leading-none">&times;</button>
        </div>

        <form id="form-edit-vendor" method="POST" class="p-5 space-y-3.5 overflow-y-auto flex-1">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Kategori Layanan <span class="text-red-500">*</span>
                </label>
                <select name="category" id="edit_category" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-bold focus:ring-2 focus:ring-[#4e73df]">
                    <option value="photographer">📷 Fotografer Ruangan (Foto & Cetak)</option>
                    <option value="live_streaming">▶️ Jasa Streaming Live Youtube (Multi-cam Broadcast)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Nama Studio / Vendor / Jasa <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-bold focus:ring-2 focus:ring-[#4e73df]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Kontak Person (PIC)
                    </label>
                    <input type="text" name="contact_person" id="edit_contact_person" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="whatsapp_number" id="edit_whatsapp_number" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-mono font-bold focus:ring-2 focus:ring-[#4e73df]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Estimasi Kisaran Tarif
                    </label>
                    <input type="text" name="price_estimate" id="edit_price_estimate" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Akun Instagram
                    </label>
                    <input type="text" name="instagram_handle" id="edit_instagram_handle" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Channel / Link Portofolio YouTube
                </label>
                <input type="url" name="youtube_channel" id="edit_youtube_channel" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
            </div>

            @if($isSuperadmin)
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Alokasi Program Studi
                    </label>
                    <select name="study_program_id" id="edit_study_program_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 font-bold focus:ring-2 focus:ring-[#4e73df]">
                        <option value="">-- Umum (Tersedia Untuk Seluruh Prodi) --</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Deskripsi Layanan & Catatan
                </label>
                <textarea name="description" id="edit_description" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]"></textarea>
            </div>

            <div class="flex items-center gap-2 p-2.5 rounded-lg bg-gray-50 border border-gray-200">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-[#4e73df] focus:ring-[#4e73df]">
                <label for="edit_is_active" class="text-xs font-semibold text-gray-700 cursor-pointer">
                    Status Aktif (Tampilkan di portal publik / peserta)
                </label>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-vendor')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-black text-xs rounded-xl shadow-sm flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function openEditModal(vendor) {
        const isSuperadmin = @json($isSuperadmin);
        const updateBaseUrl = isSuperadmin ? "{{ url('/superadmin/vendors') }}" : "{{ url('/admin/vendors') }}";
        const form = document.getElementById('form-edit-vendor');
        if (form) {
            form.action = `${updateBaseUrl}/${vendor.id}`;
        }

        document.getElementById('edit_category').value = vendor.category || 'photographer';
        document.getElementById('edit_name').value = vendor.name || '';
        document.getElementById('edit_contact_person').value = vendor.contact_person || '';
        document.getElementById('edit_whatsapp_number').value = vendor.whatsapp_number || '';
        document.getElementById('edit_price_estimate').value = vendor.price_estimate || '';
        document.getElementById('edit_instagram_handle').value = vendor.instagram_handle || '';
        document.getElementById('edit_youtube_channel').value = vendor.youtube_channel || '';
        document.getElementById('edit_description').value = vendor.description || '';

        const editSp = document.getElementById('edit_study_program_id');
        if (editSp) {
            editSp.value = vendor.study_program_id || '';
        }

        const editActive = document.getElementById('edit_is_active');
        if (editActive) {
            editActive.checked = Boolean(vendor.is_active);
        }

        openModal('modal-edit-vendor');
    }

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal('modal-create-vendor');
            closeModal('modal-edit-vendor');
        }
    });
</script>
@endsection
