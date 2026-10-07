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
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all hover:shadow-md">
        <!-- Card Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-gray-100 gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#4e73df] flex items-center justify-center font-bold text-base shadow-xs">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-800 tracking-tight flex items-center gap-2">
                        Naskah Lafal Sumpah Resmi Prodi
                    </h3>
                    <p class="text-[11px] font-semibold text-gray-500 mt-0.5">
                        Dokumen master lafal sumpah untuk {{ $prodi->name }} (berlaku tetap untuk seluruh periode)
                    </p>
                </div>
            </div>

            <div>
                @if($prodi->oath_pdf_path)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> File PDF Tersedia
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Mode Teks Baku (Belum Ada PDF)
                    </span>
                @endif
            </div>
        </div>

        <!-- Card Body (Balanced 2-Column Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            <!-- Left Column: Status, Preview & Distribution Info (7 cols) -->
            <div class="lg:col-span-7 flex flex-col justify-between bg-slate-50/70 rounded-xl p-5 border border-slate-200/80">
                <div>
                    @if($prodi->oath_pdf_path)
                        <!-- State: PDF Exists -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-14 rounded-lg bg-red-500 text-white flex flex-col items-center justify-center shadow-xs flex-shrink-0">
                                <i class="fa-solid fa-file-pdf text-xl"></i>
                                <span class="text-[8px] font-black uppercase tracking-wider mt-0.5">PDF</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-black text-gray-800 truncate mb-1">
                                    Naskah_Sumpah_{{ strtoupper($prodi->code) }}.pdf
                                </h4>
                                <p class="text-[11px] text-gray-500 leading-relaxed mb-3">
                                    File PDF resmi telah aktif dan terpasang. Mahasiswa dan rohaniwan dapat langsung membaca atau mengunduh naskah ini.
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ asset('storage/' . $prodi->oath_pdf_path) }}" target="_blank"
                                       class="px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-lg font-bold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-eye text-[#4e73df]"></i> Pratinjau Naskah
                                    </a>
                                    <a href="{{ asset('storage/' . $prodi->oath_pdf_path) }}" download
                                       class="px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-bold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-download"></i> Unduh Berkas
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- State: No PDF -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-14 rounded-lg bg-white border-2 border-dashed border-gray-300 text-gray-400 flex flex-col items-center justify-center shadow-2xs flex-shrink-0">
                                <i class="fa-regular fa-file-pdf text-xl"></i>
                                <span class="text-[8px] font-bold uppercase tracking-wider mt-0.5">KOSONG</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-black text-gray-800 mb-1">
                                    Belum Ada Dokumen PDF Master
                                </h4>
                                <p class="text-[11px] text-gray-500 leading-relaxed">
                                    Saat PDF belum diunggah, sistem secara otomatis menampilkan naskah lafal sumpah bawaan yang disesuaikan secara dinamis menurut agama masing-masing peserta.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Integration Badges -->
                <div class="pt-4 border-t border-slate-200/60 mt-3">
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider block mb-2">
                        Otomatis Disinkronkan Ke:
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center gap-2 text-xs text-gray-600 bg-white px-2.5 py-1.5 rounded-lg border border-gray-200/70 shadow-2xs">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                            <span class="truncate">Portal Pendaftaran Token</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600 bg-white px-2.5 py-1.5 rounded-lg border border-gray-200/70 shadow-2xs">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                            <span class="truncate">Dashboard Peserta Terdaftar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Upload / Update Form (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-xl p-5 border border-gray-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-black text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-cloud-arrow-up text-[#4e73df]"></i>
                            {{ $prodi->oath_pdf_path ? 'Perbarui File PDF' : 'Unggah File PDF' }}
                        </h4>
                        <span class="text-[10px] text-gray-400 font-semibold">Maks. 10MB</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mb-4 leading-relaxed">
                        {{ $prodi->oath_pdf_path ? 'Pilih file PDF baru untuk menimpa file naskah sumpah lama.' : 'Pilih file PDF naskah sumpah resmi yang diterbitkan organisasi profesi / prodi.' }}
                    </p>
                </div>

                <form action="{{ route('admin.dashboard.update-oath-pdf') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <input type="file" name="oath_pdf" required accept=".pdf"
                               class="block w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#4e73df] hover:file:bg-blue-100 border border-gray-200 rounded-lg bg-gray-50/50 p-1.5 focus:outline-none focus:ring-2 focus:ring-[#4e73df]/20 focus:border-[#4e73df] cursor-pointer transition">
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $prodi->oath_pdf_path ? 'Simpan & Ganti PDF' : 'Simpan & Publikasikan PDF' }}
                    </button>
                </form>
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
        <div class="text-xs font-bold text-gray-800 group-hover:text-emerald-700">Validasi Kandidat</div>
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
