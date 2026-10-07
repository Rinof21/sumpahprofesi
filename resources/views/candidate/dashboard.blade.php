@extends('layouts.app')

@section('title', 'Dasbor Peserta Sumpah')

@section('content')
<!-- Page Header -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                NIM: {{ $candidate->nim }}
            </span>
            <span class="px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">
                Agama: {{ $candidate->religion }}
            </span>
            <span class="px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">
                Jalur: {{ $candidate->admission_path }}
            </span>
            @if($candidate->period->is_locked)
                <span class="px-2.5 py-0.5 rounded bg-red-100 text-red-800 border border-red-300 text-[10px] font-black uppercase flex items-center gap-1">
                    <i class="fa-solid fa-lock"></i> Dikunci Admin
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black uppercase flex items-center gap-1">
                    <i class="fa-solid fa-lock-open"></i> Pendaftaran Terbuka
                </span>
            @endif
        </div>
        <h1 class="text-2xl font-black text-gray-900">{{ $candidate->full_name }}</h1>
        <p class="text-xs text-gray-500">
            Prodi: <strong class="text-gray-800">{{ $candidate->period->studyProgram->name }} ({{ $candidate->period->studyProgram->degree_title }})</strong> &bull; Periode: <strong class="text-[#4e73df]">{{ $candidate->period->name }}</strong>
        </p>
    </div>

    <div>
        @if($candidate->agreed_rules)
            <span class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold inline-flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-check-circle text-emerald-600"></i> Compliance Passed (Pakta Disetujui)
            </span>
        @else
            <span class="px-3 py-1.5 rounded-lg bg-red-100 text-red-800 border border-red-300 text-xs font-bold inline-flex items-center gap-1.5 animate-pulse shadow-sm">
                <i class="fa-solid fa-lock text-red-600"></i> Terkunci (Perlu Disetujui)
            </span>
        @endif
    </div>
</div>

<!-- Banner Lock Admin Status -->
@if($candidate->period->is_locked)
    <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg border border-red-200 text-red-900 text-xs font-bold flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-lock text-red-600 text-xl"></i>
            <div>
                <div class="font-extrabold uppercase tracking-wide">Pendaftaran & Perubahan Data Dikunci Admin</div>
                <div class="text-[11px] font-semibold text-red-700 mt-0.5">Admin Prodi telah mengunci pendaftaran ini. Anda tidak dapat melakukan Tambah, Edit, atau Hapus data biodata & berkas PPT.</div>
            </div>
        </div>
    </div>
@endif

<!-- Modul D: Compliance Gate (Pakta Integritas Digital) -->
@if(!$candidate->agreed_rules)
    <div class="mb-6 bg-red-50 rounded-lg border-l-4 border-red-600 p-6 shadow-sm border border-red-200">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-lg shrink-0">
                <i class="fa-solid fa-shield-alt"></i>
            </div>
            <div class="flex-grow">
                <h2 class="text-lg font-black text-red-900">Gerbang Tata Tertib Acara (Compliance Gate)</h2>
                <p class="text-xs text-red-700 mt-1 font-semibold">
                    Setiap calon lulusan WAJIB membaca dan menyetujui komitmen Pakta Integritas digital di bawah ini sebelum dapat mengakses <strong>e-Ticket Undangan Keluarga</strong> dan <strong>Naskah Lafal Sumpah Official</strong>.
                </p>

                <form action="{{ route('candidate.agree-rules') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    
                    <!-- Point 1: Children Prohibition -->
                    <div class="p-3.5 rounded bg-white border border-red-300 flex items-start space-x-3">
                        <input type="checkbox" id="confirm_no_children" name="confirm_no_children" required class="mt-1 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                        <label for="confirm_no_children" class="text-xs text-red-900 font-bold leading-relaxed cursor-pointer">
                            <span class="uppercase text-red-700 font-black tracking-wider block text-xs mb-0.5">⚠️ LARANGAN MUTLAK MEMBAWA ANAK KECIL / BALITA:</span>
                            Saya berjanji dan menjamin seluruh keluarga/tamu undangan saya TIDAK AKAN MEMBAWA ANAK KECIL / BALITA KE DALAM RUANG PROSESI AULA demi menjaga kekhidmatan sakral angkat sumpah profesi.
                        </label>
                    </div>

                    <!-- Point 2: Punctuality -->
                    <div class="p-3.5 rounded bg-white border border-gray-300 flex items-start space-x-3">
                        <input type="checkbox" id="confirm_punctuality" name="confirm_punctuality" required class="mt-1 w-4 h-4 rounded border-gray-300 text-[#4e73df] focus:ring-[#4e73df] cursor-pointer">
                        <label for="confirm_punctuality" class="text-xs text-gray-800 font-semibold leading-relaxed cursor-pointer">
                            <strong>Kewajiban Kehadiran:</strong> Saya bersedia hadir Gladi Resik dan Hari-H prosesi tepat waktu sesuai jadwal panitia.
                        </label>
                    </div>

                    <!-- Point 3: Dresscode -->
                    <div class="p-3.5 rounded bg-white border border-gray-300 flex items-start space-x-3">
                        <input type="checkbox" id="confirm_dresscode" name="confirm_dresscode" required class="mt-1 w-4 h-4 rounded border-gray-300 text-[#4e73df] focus:ring-[#4e73df] cursor-pointer">
                        <label for="confirm_dresscode" class="text-xs text-gray-800 font-semibold leading-relaxed cursor-pointer">
                            <strong>Standar Tata Busana Resmi:</strong> Saya wajib mengenakan busana toga / jas sipil lengkap sesuai standar etika profesi.
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-signature"></i> Setujui Pakta Integritas & Buka Fitur
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Section 1: Biodata Peserta & Manajemen Data (Edit / Delete) -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-gray-200">
        <div>
            <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-[#4e73df]"></i> Biodata Peserta Sumpah Terdaftar
            </h2>
            <p class="text-xs text-gray-500">Informasi resmi yang tersimpan untuk pencetakan ijazah & sertifikat sumpah.</p>
        </div>

        @if(!$candidate->period->is_locked)
            <div class="flex items-center gap-2">
                <button type="button" onclick="openModal('modal-edit-biodata')" class="px-3 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Biodata
                </button>

                <form action="{{ route('candidate.registration.delete') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan/menghapus pendaftaran sumpah ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i> Hapus Pendaftaran
                    </button>
                </form>
            </div>
        @else
            <span class="px-3 py-1.5 bg-gray-100 text-gray-500 border border-gray-300 font-bold rounded-lg text-xs flex items-center gap-1 cursor-not-allowed" title="Perubahan data dikunci oleh Admin">
                <i class="fa-solid fa-lock"></i> Edit & Hapus Dikunci
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">NIM</div>
            <div class="font-extrabold text-gray-900 text-sm mt-0.5">{{ $candidate->nim }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">NIK KTP</div>
            <div class="font-extrabold text-gray-900 text-sm mt-0.5">{{ $candidate->nik }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">Tempat, Tgl Lahir</div>
            <div class="font-bold text-gray-900 mt-0.5">{{ $candidate->birth_place }}, {{ $candidate->birth_date->format('d M Y') }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">Agama / Rohaniwan</div>
            <div class="font-bold text-gray-900 mt-0.5">{{ $candidate->religion }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">Nama Ayah</div>
            <div class="font-bold text-gray-900 mt-0.5">{{ $candidate->father_name }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">Nama Ibu</div>
            <div class="font-bold text-gray-900 mt-0.5">{{ $candidate->mother_name }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">Jalur Masuk</div>
            <div class="font-bold text-gray-900 mt-0.5">{{ $candidate->admission_path }}</div>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-[10px] text-gray-400 font-bold uppercase">Kode Token Akses Periode</div>
            <div class="font-mono font-black text-[#4e73df] mt-0.5">{{ $candidate->period->access_token ?: '-' }}</div>
        </div>
    </div>
</div>

<!-- Section 2: Features Grid Cards (Lafal Sumpah, Google Drive PPT, e-Ticket) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <!-- Card 1: Naskah Lafal Sumpah Dalam Konten -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center text-lg font-bold mb-3">
                <i class="fa-solid fa-scroll"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Naskah Lafal Sumpah</h3>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                Teks naskah lafal sumpah resmi untuk Prodi <strong>{{ $candidate->period->studyProgram->name }}</strong> dan Agama <strong>{{ $candidate->religion }}</strong>.
            </p>
        </div>

        @if($candidate->agreed_rules)
            <a href="{{ route('candidate.oath-script') }}" class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm text-center flex items-center justify-center gap-2">
                <i class="fa-solid fa-scroll"></i> Buka Naskah Lafal Sumpah
            </a>
        @else
            <button disabled class="w-full py-2 bg-gray-200 text-gray-500 font-bold text-xs rounded cursor-not-allowed flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock"></i> Fitur Terkunci
            </button>
        @endif
    </div>

    <!-- Card 2: Link Google Drive Admin & Upload PPT -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center text-lg font-bold mb-3">
                <i class="fa-solid fa-file-powerpoint"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Upload PPT & Google Drive</h3>
            <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                Unggah slide PPT profil (.ppt, .pptx, .pdf max 20MB) atau gunakan tautan Google Drive resmi panitia.
            </p>

            @if($candidate->period->drive_url)
                <a href="{{ $candidate->period->drive_url }}" target="_blank" class="mb-3 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg font-bold text-xs flex items-center justify-between transition-colors">
                    <span class="flex items-center gap-2"><i class="fa-brands fa-google-drive text-emerald-600 text-sm"></i> Buka Link Drive Admin</span>
                    <i class="fa-solid fa-external-link text-[10px]"></i>
                </a>
            @endif

            @if($candidate->ppt_file_path)
                <div class="p-2 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold mb-3">
                    <i class="fa-solid fa-check text-emerald-600"></i> Berkas PPT Berhasil Terunggah
                </div>
            @endif
        </div>

        @if(!$candidate->period->is_locked)
            <form action="{{ route('candidate.upload-ppt') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <input type="file" name="ppt_file" required accept=".ppt,.pptx,.pdf" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                <button type="submit" class="w-full py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-upload"></i> Unggah Berkas PPT
                </button>
            </form>
        @else
            <div class="p-2 bg-gray-100 text-gray-500 border border-gray-300 rounded text-center text-xs font-bold">
                <i class="fa-solid fa-lock mr-1"></i> Upload PPT Dikunci Admin
            </div>
        @endif
    </div>

    <!-- Card 3: e-Ticket Undangan -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
        <div>
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold mb-3">
                <i class="fa-solid fa-ticket-alt"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">e-Ticket Undangan</h3>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                Tiket digital akses ruang aula untuk keluarga, lengkap dengan QR Code verifikasi dan peringatan larangan balita.
            </p>
        </div>

        @if($candidate->agreed_rules)
            <a href="{{ route('candidate.e-ticket') }}" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm text-center flex items-center justify-center gap-2">
                <i class="fa-solid fa-qrcode"></i> Lihat e-Ticket
            </a>
        @else
            <button disabled class="w-full py-2 bg-gray-200 text-gray-500 font-bold text-xs rounded cursor-not-allowed flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock"></i> Fitur Terkunci
            </button>
        @endif
    </div>

</div>

<!-- Section 3: Tampilan Lafal Sumpah Langsung Di Dalam Konten Dashboard -->
@if($candidate->agreed_rules)
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-black text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-scroll text-[#4e73df]"></i> Lafal Sumpah Resmi {{ $candidate->period->studyProgram->name }} ({{ $candidate->religion }})
        </h2>
        <div class="p-4 bg-blue-50/50 rounded-lg border border-blue-200 text-xs font-semibold text-gray-800 leading-relaxed space-y-2">
            <p class="font-extrabold text-[#4e73df] uppercase tracking-wide text-[11px]">DEMI ALLAH / DEMI TUHAN SAYA BERSUMPAH BOHWA:</p>
            <p>1. Saya akan mengabdikan hidup saya guna kepentingan kemanusiaan.</p>
            <p>2. Saya akan menjalankan tugas saya dengan cara yang terhormat dan bersusila, sesuai dengan martabat profesi {{ $candidate->period->studyProgram->name }}.</p>
            <p>3. Kesehatan dan keselamatan pasien saya akan selalu menjadi pertimbangan utama saya.</p>
            <p>4. Saya akan merahasiakan segala sesuatu yang saya ketahui karena pekerjaan saya dan karena keilmuan saya sebagai {{ $candidate->period->studyProgram->degree_title }}.</p>
            <p>5. Saya akan memelihara dengan sekuat tenaga martabat dan tradisi luhur profesi kesehatan FK UNTAN.</p>
        </div>
    </div>
@endif

<!-- Modal Edit Biodata Peserta -->
@if(!$candidate->period->is_locked)
<div id="modal-edit-biodata" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-xl w-full overflow-hidden">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Biodata Peserta Sumpah
            </h3>
            <button type="button" onclick="closeModal('modal-edit-biodata')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form action="{{ route('candidate.biodata.update') }}" method="POST" class="p-5 space-y-3">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim', $candidate->nim) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIK KTP</label>
                    <input type="text" name="nik" value="{{ old('nik', $candidate->nik) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $candidate->full_name) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $candidate->birth_place) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $candidate->birth_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Kandung Ayah</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $candidate->father_name) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Kandung Ibu</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $candidate->mother_name) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Jalur Masuk Kuliah</label>
                    <select name="admission_path" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                        <option value="SNBP/SNMPTN" {{ $candidate->admission_path === 'SNBP/SNMPTN' ? 'selected' : '' }}>SNBP / SNMPTN</option>
                        <option value="SNBT/SBMPTN" {{ $candidate->admission_path === 'SNBT/SBMPTN' ? 'selected' : '' }}>SNBT / SBMPTN</option>
                        <option value="Mandiri" {{ $candidate->admission_path === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="Kerjasama/Afirmasi" {{ $candidate->admission_path === 'Kerjasama/Afirmasi' ? 'selected' : '' }}>Kerjasama / Afirmasi</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Agama</label>
                    <select name="religion" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                        <option value="Islam" {{ $candidate->religion === 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Protestan" {{ $candidate->religion === 'Protestan' ? 'selected' : '' }}>Protestan</option>
                        <option value="Katolik" {{ $candidate->religion === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ $candidate->religion === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ $candidate->religion === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Konghucu" {{ $candidate->religion === 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-biodata')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Segmented Helpdesk Area -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-headset text-[#4e73df]"></i> Kontak Narahubung Bantuan
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- IT Desk -->
        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
            <div class="text-xs font-bold text-[#4e73df] uppercase tracking-wider mb-1">Meja Bantuan IT Fakultas</div>
            <p class="text-[11px] text-gray-500 mb-3">Untuk kendala reset password, upload PPT, atau bug web.</p>
            @foreach($itContacts as $it)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $it->phone) }}" target="_blank" class="flex items-center justify-between p-2 rounded bg-white border border-gray-200 hover:border-[#4e73df] text-xs font-bold text-gray-800 transition-colors">
                    <span><i class="fa-brands fa-whatsapp text-emerald-600 mr-1.5"></i> {{ $it->name }}</span>
                    <span class="text-[10px] text-[#4e73df] underline">Hubungi WA</span>
                </a>
            @endforeach
        </div>

        <!-- Admin Desk -->
        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
            <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Meja Bantuan Admin {{ $candidate->period->studyProgram->code }}</div>
            <p class="text-[11px] text-gray-500 mb-3">Untuk berkas kelulusan, legalisir, toga, dan rohaniwan.</p>
            @foreach($adminContacts as $ac)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ac->phone) }}" target="_blank" class="flex items-center justify-between p-2 rounded bg-white border border-gray-200 hover:border-emerald-600 text-xs font-bold text-gray-800 transition-colors">
                    <span><i class="fa-brands fa-whatsapp text-emerald-600 mr-1.5"></i> {{ $ac->name }}</span>
                    <span class="text-[10px] text-emerald-700 underline">Hubungi WA</span>
                </a>
            @endforeach
        </div>
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
</script>
@endpush
@endsection
