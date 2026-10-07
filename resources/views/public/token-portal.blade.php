@extends('layouts.app')

@section('title', 'Portal Pendaftaran Peserta Sumpah')

@section('content')
<!-- Header Banner Periode -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                    PRODI: {{ $period->studyProgram->name }} ({{ $period->studyProgram->code }})
                </span>
                <span class="px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">
                    Siklus: {{ $period->quarter_code }} &bull; Tanggal: {{ $period->event_date->format('d M Y') }}
                </span>
                <span class="px-2.5 py-0.5 rounded bg-purple-100 text-purple-700 font-mono font-black text-[10px]" title="Token Akses Periode">
                    <i class="fa-solid fa-key mr-1"></i>{{ $period->access_token }}
                </span>
                <span class="px-2.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold flex items-center gap-1" title="Sesi token berlaku 60 menit (bebas keluar-masuk)">
                    <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Sesi Token: {{ $remainingMinutes }} mnt
                </span>
                @if($period->is_locked)
                    <span class="px-2.5 py-0.5 rounded bg-red-100 text-red-800 border border-red-300 text-[10px] font-black uppercase flex items-center gap-1">
                        <i class="fa-solid fa-lock"></i> DIKUNCI ADMIN
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase flex items-center gap-1">
                        <i class="fa-solid fa-lock-open"></i> DISEDIAKAN PENGISIAN
                    </span>
                @endif
            </div>

            <h1 class="text-2xl font-black text-gray-900">{{ $period->name }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Portal Pendaftaran & Pengisian Data Bersama Mahasiswa (Akses Token Active &bull; Bebas Keluar-Masuk 60 Menit).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($period->drive_url)
                <a href="{{ $period->drive_url }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center gap-2 transition-all">
                    <i class="fa-brands fa-google-drive text-sm"></i> Buka Drive PPT Admin
                </a>
            @endif
            <form action="{{ route('token-access.reset') }}" method="POST" class="inline">
                @csrf
                <button type="submit" onclick="return confirm('Sesi token 60 menit akan direset. Apakah Anda ingin keluar dan memasukkan token baru?')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Reset Token / Ganti Periode
                </button>
            </form>
        </div>
    </div>

    @if($period->is_locked)
        <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg border border-red-200 text-red-900 text-xs font-bold flex items-center gap-3">
            <i class="fa-solid fa-lock text-red-600 text-xl shrink-0"></i>
            <div>
                <div class="font-extrabold uppercase tracking-wide">Pendaftaran & Data Peserta Dikunci Oleh Admin Prodi</div>
                <div class="text-[11px] font-semibold text-red-700 mt-0.5">
                    Proses penambahan, pengubahan (edit), dan penghapusan data peserta untuk periode ini telah dikunci oleh panitia. Data yang ditampilkan adalah final.
                </div>
            </div>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Left Column: Form Tambah Peserta Baru (jika belum dikunci) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
            <h2 class="text-sm font-black text-gray-900 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-[#4e73df]"></i> Tambah Data Peserta Sumpah
            </h2>
            <p class="text-xs text-gray-500 mb-4">Isi biodata calon lulusan yang akan mengikuti sumpah profesi ini.</p>

            @if($period->is_locked)
                <div class="p-4 bg-gray-100 border border-gray-300 rounded-lg text-center text-xs font-bold text-gray-500">
                    <i class="fa-solid fa-lock text-lg mb-1 block"></i>
                    Formulir Ditutup (Pendaftaran Dikunci Admin)
                </div>
            @else
                <form action="{{ route('token-access.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $period->id }}">

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">NIM (Nomor Induk Mahasiswa) <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" required placeholder="Contoh: 22010119001" value="{{ old('nim') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">NIK KTP <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" required placeholder="16 Digit NIK KTP" value="{{ old('nik') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" required placeholder="Nama & Gelar Terdahulu" value="{{ old('full_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" required placeholder="Kota Lahir" value="{{ old('birth_place') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Tgl Lahir</label>
                            <input type="date" name="birth_date" required value="{{ old('birth_date') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Nama Ayah</label>
                            <input type="text" name="father_name" required placeholder="Nama Kandung" value="{{ old('father_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Nama Ibu</label>
                            <input type="text" name="mother_name" required placeholder="Nama Kandung" value="{{ old('mother_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Jalur Masuk</label>
                            <select name="admission_path" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                                <option value="SNBP/SNMPTN">SNBP/SNMPTN</option>
                                <option value="SNBT/SBMPTN">SNBT/SBMPTN</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="Kerjasama/Afirmasi">Kerjasama</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Agama</label>
                            <select name="religion" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                                <option value="Islam">Islam</option>
                                <option value="Protestan">Protestan</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center justify-center gap-2 mt-2">
                        <i class="fa-solid fa-plus-circle"></i> Tambah Peserta Baru
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Right Column: Daftar Peserta Terdaftar & Aksi (Edit / Hapus / PPT Upload) -->
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-users text-[#4e73df]"></i> Daftar Peserta Terdaftar ({{ $period->candidates->count() }} Mahasiswa)
                </h2>
                <span class="text-[11px] text-gray-500 font-semibold">Bisa diedit/dihapus masing-masing sebelum dikunci Admin.</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5 w-8 text-center">No.</th>
                            <th class="px-3 py-2.5">NIM & Nama Peserta</th>
                            <th class="px-3 py-2.5">Agama & TTL</th>
                            <th class="px-3 py-2.5">Orang Tua</th>
                            <th class="px-3 py-2.5">Slide PPT Profil</th>
                            <th class="px-3 py-2.5 text-right">Aksi Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($period->candidates as $i => $c)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-3 text-center text-[11px] font-black text-gray-400 w-8">
                                    {{ $i + 1 }}
                                </td>
                                <td class="px-3 py-3 font-bold text-gray-900">
                                    <div>{{ $c->full_name }}</div>
                                    <div class="text-[10px] text-[#4e73df] font-mono mt-0.5">NIM: {{ $c->nim }}</div>
                                    @if($c->is_oath_coordinator)
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-indigo-100 text-indigo-800 border border-indigo-200 rounded text-[9px] font-black">
                                            <i class="fa-solid fa-star text-[8px]"></i> Koordinator Sumpah
                                        </span>
                                    @endif
                                    @if($c->is_speech_rep)
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded text-[9px] font-black">
                                            <i class="fa-solid fa-microphone text-[8px]"></i> Perwakilan Pesan & Kesan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <span class="px-1.5 py-0.5 bg-gray-100 border rounded text-[10px] font-bold text-gray-700">{{ $c->religion }}</span>
                                    <div class="text-[10px] text-gray-500 mt-1">{{ $c->birth_place }}, {{ $c->birth_date->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-3 py-3 text-[11px]">
                                    <div>Ayah: <span class="font-semibold text-gray-800">{{ $c->father_name }}</span></div>
                                    <div>Ibu: <span class="font-semibold text-gray-800">{{ $c->mother_name }}</span></div>
                                </td>
                                <td class="px-3 py-3">
                                    @if($c->ppt_file_path)
                                        <a href="{{ asset('storage/' . $c->ppt_file_path) }}" target="_blank" class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-bold text-[10px] inline-flex items-center gap-1 hover:underline">
                                            <i class="fa-solid fa-file-powerpoint"></i> PPT Terunggah
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic text-[10px]">Belum unggah</span>
                                    @endif

                                    @if(!$period->is_locked)
                                        <button type="button" onclick="openModal('modal-ppt-{{ $c->id }}')" class="block mt-1 text-[10px] font-bold text-cyan-700 hover:underline">
                                            + Upload PPT
                                        </button>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-right whitespace-nowrap">
                                    @if(!$period->is_locked)
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" onclick="openEditCandidateModal('{{ $c->id }}', '{{ addslashes($c->nim) }}', '{{ addslashes($c->nik) }}', '{{ addslashes($c->full_name) }}', '{{ addslashes($c->birth_place) }}', '{{ $c->birth_date->format('Y-m-d') }}', '{{ addslashes($c->father_name) }}', '{{ addslashes($c->mother_name) }}', '{{ $c->admission_path }}', '{{ $c->religion }}')" class="p-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold rounded text-xs transition-colors" title="Edit Biodata">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <form action="{{ route('token-access.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus data peserta {{ $c->full_name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold rounded text-xs transition-colors" title="Hapus Peserta">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-[10px]"><i class="fa-solid fa-lock"></i> Dikunci</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Upload PPT Per Candidate -->
                            @if(!$period->is_locked)
                                <div id="modal-ppt-{{ $c->id }}" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
                                    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden text-left">
                                        <div class="bg-cyan-700 px-4 py-3 text-white flex items-center justify-between">
                                            <h3 class="font-bold text-xs uppercase tracking-wider">Upload Slide PPT: {{ $c->full_name }}</h3>
                                            <button type="button" onclick="closeModal('modal-ppt-{{ $c->id }}')" class="text-white/80 hover:text-white">&times;</button>
                                        </div>
                                        <form action="{{ route('token-access.upload-ppt', $c->id) }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih File PPT (.ppt, .pptx, .pdf max 20MB)</label>
                                                <input type="file" name="ppt_file" required accept=".ppt,.pptx,.pdf" class="w-full text-xs text-gray-500">
                                            </div>
                                            <div class="flex justify-end gap-2 pt-2 border-t">
                                                <button type="button" onclick="closeModal('modal-ppt-{{ $c->id }}')" class="px-3 py-1.5 bg-gray-100 text-xs font-bold rounded">Batal</button>
                                                <button type="submit" class="px-3 py-1.5 bg-cyan-700 text-white text-xs font-bold rounded">Unggah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-400 font-medium">Belum ada peserta yang mendaftar pada periode ini. Silakan isi form di sebelah kiri.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Lafal Sumpah (PDF dari Admin atau Fallback Teks) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h2 class="text-sm font-black text-gray-900 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-scroll text-[#4e73df]"></i> Naskah Lafal Sumpah Official ({{ $period->studyProgram->name }})
            </h2>
            <p class="text-xs text-gray-500 mb-3">Teks resmi lafal sumpah profesi yang akan dibacakan secara sakral saat prosesi angkat sumpah.</p>

            @if($period->studyProgram->oath_pdf_path)
                {{-- PDF Viewer & Download --}}
                <div class="rounded-lg border border-blue-200 overflow-hidden">
                    <div class="bg-blue-50 px-4 py-2.5 flex items-center justify-between border-b border-blue-200">
                        <span class="text-[11px] font-bold text-blue-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-file-pdf text-red-500"></i> Dokumen Lafal Sumpah (PDF)
                        </span>
                        <a href="{{ asset('storage/' . $period->studyProgram->oath_pdf_path) }}" target="_blank" download class="px-3 py-1 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-[10px] rounded flex items-center gap-1 transition-colors">
                            <i class="fa-solid fa-download"></i> Unduh PDF
                        </a>
                    </div>
                    <iframe src="{{ asset('storage/' . $period->studyProgram->oath_pdf_path) }}" class="w-full border-0" style="height: 500px;" title="Naskah Lafal Sumpah PDF"></iframe>
                </div>
            @else
                {{-- Fallback: teks hardcoded jika belum ada PDF --}}
                <div class="p-4 bg-amber-50/60 rounded-lg border border-amber-200 text-[11px] font-semibold text-amber-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    Dokumen PDF lafal sumpah belum diunggah oleh Admin Prodi. Menampilkan teks default.
                </div>
                <div class="p-4 bg-blue-50/50 rounded-lg border border-blue-200 text-xs font-semibold text-gray-800 leading-relaxed space-y-2">
                    <p class="font-extrabold text-[#4e73df] uppercase tracking-wide text-[11px]">DEMI ALLAH / DEMI TUHAN SAYA BERSUMPAH BAHWA:</p>
                    <p>1. Saya akan mengabdikan hidup saya guna kepentingan kemanusiaan.</p>
                    <p>2. Saya akan menjalankan tugas saya dengan cara yang terhormat dan bersusila, sesuai dengan martabat profesi {{ $period->studyProgram->name }}.</p>
                    <p>3. Kesehatan dan keselamatan pasien saya akan selalu menjadi pertimbangan utama saya.</p>
                    <p>4. Saya akan merahasiakan segala sesuatu yang saya ketahui karena pekerjaan saya dan karena keilmuan saya sebagai {{ $period->studyProgram->degree_title }}.</p>
                    <p>5. Saya akan memelihara dengan sekuat tenaga martabat dan tradisi luhur profesi kesehatan FK UNTAN.</p>
                </div>
            @endif
        </div>

    </div>

</div>

<!-- Modal Edit Biodata Peserta -->
<div id="modal-edit-candidate" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-xl w-full overflow-hidden">
        <div class="bg-yellow-600 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Biodata Peserta Sumpah
            </h3>
            <button type="button" onclick="closeModal('modal-edit-candidate')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="form-edit-candidate" method="POST" class="p-5 space-y-3">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIM</label>
                    <input type="text" id="edit-cand-nim" name="nim" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIK KTP</label>
                    <input type="text" id="edit-cand-nik" name="nik" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" id="edit-cand-name" name="full_name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tempat Lahir</label>
                    <input type="text" id="edit-cand-place" name="birth_place" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tanggal Lahir</label>
                    <input type="date" id="edit-cand-date" name="birth_date" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Ayah</label>
                    <input type="text" id="edit-cand-father" name="father_name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Ibu</label>
                    <input type="text" id="edit-cand-mother" name="mother_name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Jalur Masuk</label>
                    <select id="edit-cand-path" name="admission_path" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                        <option value="SNBP/SNMPTN">SNBP/SNMPTN</option>
                        <option value="SNBT/SBMPTN">SNBT/SBMPTN</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="Kerjasama/Afirmasi">Kerjasama</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Agama</label>
                    <select id="edit-cand-religion" name="religion" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                        <option value="Islam">Islam</option>
                        <option value="Protestan">Protestan</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-candidate')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs rounded shadow-sm">Simpan Perubahan</button>
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

    function openEditCandidateModal(id, nim, nik, name, place, date, father, mother, path, religion) {
        document.getElementById('form-edit-candidate').action = "{{ url('pendaftaran-token/peserta') }}/" + id;
        document.getElementById('edit-cand-nim').value = nim;
        document.getElementById('edit-cand-nik').value = nik;
        document.getElementById('edit-cand-name').value = name;
        document.getElementById('edit-cand-place').value = place;
        document.getElementById('edit-cand-date').value = date;
        document.getElementById('edit-cand-father').value = father;
        document.getElementById('edit-cand-mother').value = mother;
        document.getElementById('edit-cand-path').value = path;
        document.getElementById('edit-cand-religion').value = religion;

        openModal('modal-edit-candidate');
    }
</script>
@endpush
@endsection
