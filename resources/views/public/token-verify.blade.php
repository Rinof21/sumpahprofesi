@extends('layouts.app')

@section('title', 'Akses Pendaftaran Sumpah Mahasiswa (Token Akses)')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        
        <div class="bg-gradient-to-r from-[#4e73df] to-[#224abe] p-6 text-white text-center">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-xl font-black uppercase tracking-wide">Portal Pendaftaran Sumpah Mahasiswa</h1>
            <p class="text-xs text-blue-100 mt-1 font-semibold">
                Tanpa perlu login akun. Cukup masukkan <strong>Kode Token Akses Resmi</strong> dari Panitia Admin Prodi Anda.
            </p>
        </div>

        <form action="{{ route('token-access.verify') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Pilih Periode Sumpah Aktif <span class="text-red-500">*</span>
                </label>
                <select name="period_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-bold text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                    <option value="">-- Pilih Periode Sumpah Program Studi Anda --</option>
                    @foreach($activePeriods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->studyProgram->name }}) - {{ $p->event_date->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Kode Token Akses dari Admin <span class="text-red-500">*</span>
                </label>
                <input type="text" name="access_token" required placeholder="Masukkan Kode Token (Contoh: TK-DOKTER26)" value="{{ old('access_token') }}" class="w-full px-3.5 py-2.5 bg-blue-50/50 border border-blue-300 rounded-lg text-sm font-mono font-black text-[#4e73df] uppercase focus:ring-2 focus:ring-[#4e73df]">
                <p class="text-[10px] text-gray-500 mt-1">
                    * Kode token didapatkan dari Sekretariat / Panitia Admin Prodi masing-masing.
                </p>
            </div>

            <button type="submit" class="w-full py-3 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs uppercase tracking-wider rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-[#unlock] fa-unlock"></i> Verifikasi Token & Masuk Portal
            </button>
        </form>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-center text-[11px] text-gray-500">
            Belum memiliki Kode Token Akses? Hubungi Panitia Admin Prodi di menu <a href="{{ route('public.helpdesk') }}" class="text-[#4e73df] font-bold hover:underline">Meja Bantuan</a>.
        </div>

    </div>
</div>
@endsection
