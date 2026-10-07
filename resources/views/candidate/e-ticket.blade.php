@extends('layouts.app')

@section('title', 'e-Ticket Undangan Sumpah Profesi')

@section('content')
<!-- Page Heading & Actions -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="{{ route('candidate.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dasbor
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">e-Ticket Undangan Resmi Keluarga</h1>
        <p class="text-xs font-semibold text-gray-500">Tiket digital akses ruang aula prosesi {{ $candidate->period->studyProgram->name }}</p>
    </div>

    <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-print"></i> Cetak / Simpan e-Ticket
    </button>
</div>

<!-- Ticket Container inside SB Admin 2 Layout -->
<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden text-gray-900">
    
    <!-- Header -->
    <div class="bg-[#4e73df] p-6 text-white flex items-center justify-between">
        <div>
            <div class="text-[10px] font-black uppercase tracking-widest text-blue-100">E-TICKET RESMI UNDANGAN KELUARGA</div>
            <h2 class="text-lg font-black tracking-tight text-white">SUMPAH PROFESI {{ strtoupper($candidate->period->studyProgram->name) }}</h2>
            <p class="text-xs font-bold text-blue-100">{{ $candidate->period->name }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-2xl">
            <i class="fa-solid fa-ticket-alt"></i>
        </div>
    </div>

    <!-- CRITICAL WARNING BANNER (FR-D3 & FR-D2) -->
    <div class="bg-red-600 text-white p-4 text-center font-black border-y border-red-700">
        <div class="flex items-center justify-center gap-2 text-xs uppercase tracking-wide">
            <i class="fa-solid fa-exclamation-triangle text-sm"></i>
            <span>TATA TERTIB MUTLAK RUANG AULA</span>
        </div>
        <p class="text-xs font-black mt-0.5 text-white uppercase tracking-tight">
            DILARANG MEMBAWA ANAK KECIL / BALITA KE DALAM RUANG PROSESI DEMI MENJAGA KEKHIDMATAN SAKRAL ACARA
        </p>
    </div>

    <!-- Ticket Body Details -->
    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
        
        <div class="sm:col-span-2 space-y-3 text-xs">
            <div>
                <span class="text-gray-500 text-[10px] font-bold uppercase block">Nama Wisudawan / Wisudawati</span>
                <span class="text-base font-black text-gray-900">{{ $candidate->full_name }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <span class="text-gray-500 text-[10px] font-bold uppercase block">NIM / ID</span>
                    <span class="font-mono font-bold text-[#4e73df]">{{ $candidate->nim }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-[10px] font-bold uppercase block">Agama</span>
                    <span class="font-bold text-gray-800">{{ $candidate->religion }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <span class="text-gray-500 text-[10px] font-bold uppercase block">Tanggal Pelaksanaan</span>
                    <span class="font-bold text-gray-800">{{ $candidate->period->event_date->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 text-[10px] font-bold uppercase block">Lokasi Utama</span>
                    <span class="font-bold text-gray-800">Aula Utama FKIK</span>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <span class="text-gray-500 text-[10px] font-bold uppercase block">Kuota Pendamping Keluarga</span>
                <span class="font-bold text-emerald-700">Maksimal 2 Orang Dewasa (Tanpa Balita)</span>
            </div>
        </div>

        <!-- QR Code Mockup -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-center flex flex-col items-center justify-center">
            <div class="w-28 h-28 bg-white p-2 rounded border border-gray-300 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-qrcode text-gray-900 text-6xl"></i>
            </div>
            <div class="text-[10px] font-black text-[#4e73df] uppercase tracking-widest mt-2">VERIFIED E-TICKET</div>
            <div class="text-[9px] text-gray-400 font-mono">{{ md5($candidate->nim . $candidate->id) }}</div>
        </div>

    </div>

    <!-- Ticket Footer -->
    <div class="bg-gray-50 p-3 px-6 border-t border-gray-200 text-center text-[11px] text-gray-600 flex items-center justify-between">
        <span>Pakta Integritas Digital Status: <strong class="text-emerald-700">VALIDATED</strong></span>
        <span>Tunjukkan e-Ticket ini saat registrasi meja depan</span>
    </div>

</div>
@endsection
