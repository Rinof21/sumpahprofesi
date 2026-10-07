@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Sumpah Profesi')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <div class="text-center mb-6">
            <h1 class="text-xl font-black text-gray-900">Formulir Pendaftaran Peserta Sumpah Profesi</h1>
            <p class="text-xs text-gray-500 mt-1">Lengkapi biodata resmi calon lulusan untuk pencetakan ijazah, lafal sumpah, dan persuratan rohaniwan.</p>
        </div>

        @if(!$activePeriod)
            <div class="p-4 rounded bg-amber-50 border border-amber-200 text-amber-800 text-center text-xs font-bold">
                <i class="fa-solid fa-exclamation-triangle text-base mb-1 block"></i>
                Saat ini belum ada periode pendaftaran sumpah profesi yang aktif untuk Program Studi Anda. Silakan hubungi Sekretariat Prodi.
            </div>
        @elseif($activePeriod->is_locked)
            <div class="p-4 rounded bg-red-50 border border-red-200 text-red-800 text-center text-xs font-bold">
                <i class="fa-solid fa-lock text-base mb-1 block"></i>
                Pendaftaran & Pengisian Data untuk {{ $activePeriod->name }} telah DIKUNCI oleh Panitia Admin Prodi.
            </div>
        @else
            <form action="{{ route('candidate.register.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="period_id" value="{{ $activePeriod->id }}">

                <div class="p-3.5 rounded bg-blue-50/50 border border-blue-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-gray-500">Periode Sumpah Aktif</div>
                        <div class="text-sm font-bold text-[#4e73df]">{{ $activePeriod->name }}</div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs font-bold w-fit">
                        {{ $activePeriod->studyProgram->name }} ({{ $activePeriod->studyProgram->code }})
                    </span>
                </div>

                <!-- Input Kode Token Akses dari Admin -->
                @if(isset($isTokenVerified) && $isTokenVerified)
                    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-300 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-black text-emerald-900 uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> Kode Token Akses Terverifikasi
                            </div>
                            <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">
                                Sesi token Anda masih aktif (berlaku 60 menit, bebas keluar-masuk tanpa perlu mengetik ulang token).
                            </div>
                        </div>
                        <input type="hidden" name="access_token" value="{{ $activePeriod->access_token }}">
                        <span class="px-2.5 py-1 rounded bg-emerald-200 text-emerald-800 font-mono font-black text-xs">OK</span>
                    </div>
                @else
                    <div class="p-4 rounded-lg bg-amber-50 border border-amber-200">
                        <label class="block text-xs font-black text-amber-900 uppercase mb-1">
                            <i class="fa-solid fa-key text-amber-600 mr-1"></i> Masukkan Kode Token Akses Periode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="access_token" required placeholder="Contoh: TK-DOKTER26" value="{{ old('access_token') }}" class="w-full px-3 py-2 bg-white border border-amber-300 rounded text-xs font-mono font-bold text-[#4e73df] uppercase focus:ring-2 focus:ring-amber-500">
                        <p class="text-[10px] text-amber-700 font-semibold mt-1">
                            * Minta Kode Token Akses resmi dari Panitia Admin Prodi untuk melakukan pendaftaran. Token yang berhasil dimasukkan berlaku 60 menit.
                        </p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" name="nim" required value="{{ old('nim') }}" placeholder="Contoh: 22010119001" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" required value="{{ old('nik') }}" placeholder="16 Digit NIK KTP" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar Akademik Terdahulu</label>
                        <input type="text" name="full_name" required value="{{ old('full_name', Auth::user()->name) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" required value="{{ old('birth_place') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" required value="{{ old('birth_date') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Kandung Ayah</label>
                        <input type="text" name="father_name" required value="{{ old('father_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Kandung Ibu</label>
                        <input type="text" name="mother_name" required value="{{ old('mother_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jalur Masuk Kuliah</label>
                        <select name="admission_path" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                            <option value="SNBP/SNMPTN">SNBP / SNMPTN</option>
                            <option value="SNBT/SBMPTN">SNBT / SBMPTN</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="Kerjasama/Afirmasi">Kerjasama / Afirmasi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Agama</label>
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

                <div class="pt-3 border-t border-gray-200">
                    <button type="submit" class="w-full py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-circle"></i> Verifikasi Token & Simpan Pendaftaran Sumpah
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
