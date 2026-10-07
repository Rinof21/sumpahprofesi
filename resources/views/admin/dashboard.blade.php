@extends('layouts.app')

@section('title', 'Dashboard Admin Prodi')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Dashboard Admin {{ $prodi->name }} ({{ $prodi->code }})</h1>
        <p class="text-xs font-semibold text-gray-500">Organisasi Profesi: {{ $prodi->organization }} &bull; Gelar: {{ $prodi->degree_title }}</p>
    </div>
    
    <a href="{{ route('admin.periods.index') }}" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Kelola Periode Sumpah
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r text-emerald-800 text-xs font-medium flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r text-red-800 text-xs font-medium flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-red-500 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
    </div>
@endif

<!-- Active Period Card -->
@if($activePeriod)
    <div class="bg-white rounded-lg shadow-sm border-l-4 border-emerald-500 p-5 mb-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-wider">Periode Aktif Saat Ini</span>
                </div>
                <h2 class="text-lg font-black text-gray-900 mt-0.5">{{ $activePeriod->name }}</h2>
                <p class="text-xs text-gray-500">Tanggal Acara: <strong>{{ $activePeriod->event_date->format('d F Y') }}</strong> &bull; Siklus: <strong>{{ $activePeriod->quarter_code }}</strong></p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.candidates.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded border border-gray-300">
                    <i class="fa-solid fa-users text-[#4e73df] mr-1"></i> Data Peserta ({{ $candidatesCount }})
                </a>
                <a href="{{ route('admin.photographers.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded border border-gray-300">
                    <i class="fa-solid fa-camera text-emerald-600 mr-1"></i> Fotografer ({{ $photographersCount }}/3)
                </a>
            </div>
        </div>
    </div>
@else
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-lg text-xs font-bold text-amber-800 flex items-center justify-between border border-amber-200">
        <div><i class="fa-solid fa-exclamation-triangle mr-1"></i> Belum ada periode sumpah berstatus AKTIF.</div>
        <a href="{{ route('admin.periods.index') }}" class="px-3 py-1 bg-amber-600 text-white rounded font-bold text-xs">Buka Periode</a>
    </div>
@endif

<!-- SB Admin 2 Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <!-- Cleric Recap Card (FR-C2) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-xs font-black text-[#4e73df] uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-hands-praying"></i> Rekapitulasi Agama Peserta (Rohaniwan)
            </h3>
            <a href="{{ route('admin.candidates.index') }}" class="text-[10px] font-bold text-[#4e73df] hover:underline">Detail Peserta &rarr;</a>
        </div>

        <div class="grid grid-cols-3 gap-3">
            @foreach(['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
                <div class="p-3 rounded bg-gray-50 border border-gray-200 text-center">
                    <div class="text-xl font-black text-gray-900">{{ $religionRecap->get($rel, 0) }}</div>
                    <div class="text-[10px] font-bold text-gray-500 uppercase mt-0.5">{{ $rel }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Photographer Quota Card (FR-E4 - Max 3) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                <h3 class="text-xs font-black text-emerald-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-camera"></i> Kuota Fotografer Ruangan (Maks 3)
                </h3>
                <span class="px-2 py-0.5 rounded text-xs font-black border
                    @if($photographersCount >= 3) bg-red-100 text-red-700 border-red-200
                    @else bg-emerald-100 text-emerald-700 border-emerald-200 @endif">
                    {{ $photographersCount }} / 3 Kuota
                </span>
            </div>
            <p class="text-xs text-gray-500 leading-relaxed mb-4">
                Pendaftaran fotografer resmi ruangan dibatasi tepat maksimal 3 orang per periode. Pendaftaran ke-4 ditolak otomatis oleh transaksi database.
            </p>
        </div>

        <a href="{{ route('admin.photographers.index') }}" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm text-center flex items-center justify-center gap-2">
            <i class="fa-solid fa-id-badge"></i> Kelola Fotografer & E-Badge
        </a>
    </div>

    <!-- Naskah Lafal Sumpah (PDF) Card -->
    <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 class="text-xs font-black text-red-600 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-file-pdf"></i> Naskah Lafal Sumpah Prodi (PDF)
        </h3>

        <div class="flex items-start gap-5">

            {{-- Thumbnail: small document card --}}
            <div class="flex-none">
                @if($prodi->oath_pdf_path)
                    <a href="{{ asset('storage/' . $prodi->oath_pdf_path) }}" target="_blank" title="Klik untuk buka PDF" class="block group">
                        <div class="w-24 rounded border border-red-200 overflow-hidden shadow-sm group-hover:shadow-md transition-shadow">
                            <div class="bg-red-500 px-2 py-1 flex items-center justify-center gap-1">
                                <i class="fa-solid fa-file-pdf text-white text-[10px]"></i>
                                <span class="text-white text-[9px] font-black uppercase">PDF</span>
                            </div>
                            <iframe
                                src="{{ asset('storage/' . $prodi->oath_pdf_path) }}#toolbar=0&view=FitH&page=1&zoom=40"
                                class="w-24 border-0 pointer-events-none block"
                                style="height: 108px;"
                                title="Preview PDF"
                            ></iframe>
                            <div class="bg-gray-50 px-2 py-1 text-center border-t border-gray-100">
                                <span class="text-[9px] text-gray-500 font-semibold">Klik untuk buka</span>
                            </div>
                        </div>
                    </a>
                @else
                    <div class="w-24 rounded border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center" style="height: 140px;">
                        <i class="fa-solid fa-file-pdf text-2xl text-gray-300 mb-1"></i>
                        <span class="text-[9px] text-gray-400 font-semibold text-center">Belum<br>ada PDF</span>
                    </div>
                @endif
            </div>

            {{-- Info + Upload --}}
            <div class="flex-1 flex flex-col sm:flex-row gap-4">
                {{-- Info text --}}
                <div class="flex-1 text-xs text-gray-500 leading-relaxed">
                    <p class="font-semibold text-gray-700 mb-1.5">
                        {{ $prodi->oath_pdf_path ? 'PDF Lafal Sumpah sudah diunggah.' : 'Belum ada PDF Lafal Sumpah.' }}
                    </p>
                    <p class="mb-1.5">File ini bersifat <strong class="text-gray-700">tetap untuk Prodi Anda</strong> (tidak berubah per periode) dan akan ditampilkan di:</p>
                    <ul class="list-disc list-inside text-gray-500 space-y-0.5 mb-3">
                        <li>Portal Pendaftaran Peserta (Token Akses)</li>
                        <li>Halaman Naskah Sumpah masing-masing Kandidat</li>
                    </ul>
                    @if($prodi->oath_pdf_path)
                        <a href="{{ asset('storage/' . $prodi->oath_pdf_path) }}" target="_blank" download
                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded font-bold text-[10px] transition-colors">
                            <i class="fa-solid fa-download"></i> Unduh PDF
                        </a>
                    @endif
                </div>

                {{-- Upload form --}}
                <div class="flex-none w-full sm:w-56">
                    <form action="{{ route('admin.dashboard.update-oath-pdf') }}" method="POST" enctype="multipart/form-data"
                          class="bg-red-50 p-3 rounded-lg border border-red-200 h-full flex flex-col justify-between gap-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-black text-red-700 uppercase mb-1.5">
                                {{ $prodi->oath_pdf_path ? '↑ Ganti File PDF' : '↑ Upload File PDF' }}
                            </label>
                            <input type="file" name="oath_pdf" required accept=".pdf"
                                   class="w-full text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-red-600 file:text-white hover:file:bg-red-700">
                            <p class="text-[10px] text-gray-400 mt-1">Maks. 10MB · Format PDF</p>
                        </div>
                        <button type="submit"
                                class="w-full py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded shadow-sm transition-colors flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Simpan PDF
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Navigation Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    <a href="{{ route('admin.periods.index') }}" class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow text-center group">
        <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#4e73df] mx-auto flex items-center justify-center text-lg font-bold mb-2">
            <i class="fa-solid fa-calendar-alt"></i>
        </div>
        <div class="text-xs font-bold text-gray-800 group-hover:text-[#4e73df]">Periode Sumpah</div>
        <div class="text-[10px] text-gray-400 mt-0.5">Draft, Active, Archived</div>
    </a>

    <a href="{{ route('admin.candidates.index') }}" class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow text-center group">
        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center text-lg font-bold mb-2">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="text-xs font-bold text-gray-800 group-hover:text-emerald-700">Validasi Candidates</div>
        <div class="text-[10px] text-gray-400 mt-0.5">Rohaniwan & Speech Rep</div>
    </a>

    <a href="{{ route('admin.contacts.index') }}" class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow text-center group">
        <div class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-700 mx-auto flex items-center justify-center text-lg font-bold mb-2">
            <i class="fa-solid fa-address-book"></i>
        </div>
        <div class="text-xs font-bold text-gray-800 group-hover:text-cyan-700">Narahubung Admin</div>
        <div class="text-[10px] text-gray-400 mt-0.5">Meja Bantuan Admin</div>
    </a>

    <a href="{{ route('admin.gallery.index') }}" class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow text-center group">
        <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 mx-auto flex items-center justify-center text-lg font-bold mb-2">
            <i class="fa-solid fa-images"></i>
        </div>
        <div class="text-xs font-bold text-gray-800 group-hover:text-purple-700">Kurasi Galeri & Video</div>
        <div class="text-[10px] text-gray-400 mt-0.5">Maks 10 Foto & YouTube</div>
    </a>
</div>
@endsection
