@extends('layouts.app')

@section('title', 'Susunan Acara & Tata Tertib Resmi')

@section('content')
<div class="print:m-0 print:p-0">

    <!-- Header Actions (Hidden in Print) -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <a href="{{ route('candidate.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight">Susunan Acara & Tata Tertib Prosesi</h1>
            <p class="text-xs font-semibold text-gray-500">Panduan lengkap agenda prosesi dan aturan pelaksanaan angkat sumpah profesi.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
            <a href="{{ route('candidate.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-house text-xs"></i> Dasbor
            </a>
        </div>
    </div>

    <!-- Official Banner Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 print:border-none print:shadow-none print:p-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                        {{ $candidate->period->studyProgram->name }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px] font-bold">
                        Siklus: {{ $candidate->period->quarter_code }}
                    </span>
                </div>
                <h2 class="text-xl font-black text-gray-900">{{ $candidate->period->name }}</h2>
                <p class="text-xs text-gray-600 mt-1 flex items-center gap-3">
                    <span class="flex items-center gap-1.5 font-bold text-gray-800">
                        <i class="fa-regular fa-calendar text-[#4e73df]"></i>
                        {{ $candidate->period->event_date->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="flex items-center gap-1.5 font-bold text-gray-800">
                        <i class="fa-regular fa-clock text-[#4e73df]"></i>
                        Mulai 07.30 WIB - Selesai
                    </span>
                </p>
            </div>

            <div class="text-right print:hidden">
                <div class="text-[10px] text-gray-400 font-bold uppercase">Peserta Terdaftar</div>
                <div class="font-extrabold text-gray-900 text-xs">{{ $candidate->full_name }}</div>
                <div class="font-mono text-gray-500 text-[11px]">NIM: {{ $candidate->nim }} &bull; {{ $candidate->religion }}</div>
            </div>
        </div>

        <div class="bg-blue-50/70 border-l-4 border-[#4e73df] p-3.5 rounded-r-lg text-xs text-gray-700 leading-relaxed font-medium">
            <strong class="text-gray-900 font-bold">Pemberitahuan Resmi Panitia:</strong> Seluruh calon lulusan sumpah dan pendamping keluarga diwajibkan memahami serta mematuhi seluruh susunan rundown acara dan pakta tata tertib ruangan di bawah ini demi kelancaran dan kekhidmatan sakral prosesi.
        </div>
    </div>

    <!-- Section 1: Susunan Acara (Rundown) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-8 print:border-none print:shadow-none print:p-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-4 border-b border-gray-200 gap-1">
            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-[#4e73df]"></i> Susunan Acara (Rundown Prosesi)
            </h3>
            <span class="text-xs text-gray-500 font-bold">Waktu Indonesia Barat (WIB)</span>
        </div>

        @if($candidate->period->schedules->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <i class="fa-solid fa-clock text-3xl mb-2 text-gray-300"></i>
                <p class="font-bold text-xs">Susunan acara resmi belum dirilis oleh panitia admin prodi.</p>
                <p class="text-[11px] text-gray-400">Silakan cek kembali secara berkala sebelum hari pelaksanaan.</p>
            </div>
        @else
            <!-- Mobile View: Modern Timeline Cards (Block on mobile, hidden on md+) -->
            <div class="block md:hidden space-y-3">
                @foreach($candidate->period->schedules as $index => $schedule)
                    <div class="bg-white border border-gray-200 rounded-xl p-3.5 space-y-2.5 shadow-2xs hover:border-blue-300 transition-colors">
                        <!-- Top Row: Sequence No & Time Badge -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-[#4e73df] font-mono font-black text-xs border border-blue-200 shadow-2xs">
                                <i class="fa-regular fa-clock mr-1.5 text-[11px]"></i>
                                {{ $schedule->start_time }} {{ $schedule->end_time ? '- ' . $schedule->end_time : '' }} WIB
                            </span>
                            <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 font-black text-[11px] flex items-center justify-center shrink-0 border border-gray-200">
                                {{ $index + 1 }}
                            </span>
                        </div>

                        <!-- Agenda Title -->
                        <div class="font-black text-sm text-gray-900 leading-snug">
                            {{ $schedule->activity_name }}
                        </div>

                        <!-- PIC & Location/Notes -->
                        <div class="space-y-1.5 pt-2 border-t border-gray-100 text-xs">
                            <div class="flex items-start gap-2 text-gray-700">
                                <i class="fa-solid fa-user-tie text-[#4e73df] mt-0.5 shrink-0 text-xs"></i>
                                <div>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Pengisi Acara / Penanggung Jawab</span>
                                    <span class="font-bold text-gray-800">{{ $schedule->person_in_charge ?: '-' }}</span>
                                </div>
                            </div>

                            @if($schedule->notes)
                                <div class="flex items-start gap-2 text-gray-600 bg-gray-50/80 p-2.5 rounded-lg border border-gray-100 mt-1">
                                    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 shrink-0 text-xs"></i>
                                    <div class="text-[11px] leading-relaxed">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Keterangan / Tempat</span>
                                        <span class="font-semibold text-gray-700">{{ $schedule->notes }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View: Table (Hidden on mobile, visible on md and up) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[680px]">
                    <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] border-b border-gray-200">
                        <tr>
                            <th class="px-3.5 py-2.5 text-center w-10 whitespace-nowrap">No</th>
                            <th class="px-3.5 py-2.5 w-36 whitespace-nowrap">Waktu (WIB)</th>
                            <th class="px-3.5 py-2.5">Agenda / Kegiatan</th>
                            <th class="px-3.5 py-2.5">Pengisi Acara / Penanggung Jawab</th>
                            <th class="px-3.5 py-2.5">Keterangan / Tempat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($candidate->period->schedules as $index => $schedule)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-3.5 py-3 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-3.5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-[#4e73df] font-mono font-black text-[11px] border border-blue-200">
                                        <i class="fa-regular fa-clock mr-1 text-[10px]"></i>
                                        {{ $schedule->start_time }} {{ $schedule->end_time ? '- ' . $schedule->end_time : '' }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-3 font-bold text-gray-900 text-xs">
                                    {{ $schedule->activity_name }}
                                </td>
                                <td class="px-3.5 py-3 text-gray-700 font-medium">
                                    {{ $schedule->person_in_charge ?: '-' }}
                                </td>
                                <td class="px-3.5 py-3 text-gray-500 text-[11px]">
                                    {{ $schedule->notes ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Section 2: Tata Tertib Resmi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 print:border-none print:shadow-none print:p-0">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-emerald-600"></i> Tata Tertib & Ketentuan Resmi Ruang Prosesi
            </h3>
            <span class="text-xs text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                Wajib Ditaati Seluruh Peserta & Undangan
            </span>
        </div>

        @if($candidate->period->rules->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <i class="fa-solid fa-shield-halved text-3xl mb-2 text-gray-300"></i>
                <p class="font-bold text-xs">Ketentuan tata tertib resmi belum diunggah oleh panitia admin prodi.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($candidate->period->rules as $index => $rule)
                    <div class="p-4 rounded-xl border {{ $rule->is_strict ? 'bg-red-50/60 border-red-300 shadow-xs' : 'bg-gray-50/80 border-gray-200' }}">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs shrink-0 {{ $rule->is_strict ? 'bg-red-600 text-white' : 'bg-[#4e73df] text-white' }}">
                                @if($rule->category === 'Kehadiran')
                                    <i class="fa-solid fa-user-clock text-xs"></i>
                                @elseif($rule->category === 'Pakaian')
                                    <i class="fa-solid fa-vest text-xs"></i>
                                @elseif($rule->category === 'Tamu & Keluarga')
                                    <i class="fa-solid fa-users text-xs"></i>
                                @elseif($rule->category === 'Prosesi Aula')
                                    <i class="fa-solid fa-bell-slash text-xs"></i>
                                @elseif($rule->category === 'Dokumentasi')
                                    <i class="fa-solid fa-camera text-xs"></i>
                                @else
                                    <i class="fa-solid fa-shield-alt text-xs"></i>
                                @endif
                            </div>

                            <div class="flex-grow">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h4 class="font-black text-xs {{ $rule->is_strict ? 'text-red-900' : 'text-gray-900' }}">
                                        {{ $rule->title }}
                                    </h4>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $rule->is_strict ? 'bg-red-200 text-red-800' : 'bg-blue-100 text-[#4e73df]' }}">
                                        {{ $rule->category }}
                                    </span>
                                    @if($rule->is_strict)
                                        <span class="px-2 py-0.5 rounded bg-red-600 text-white text-[9px] font-black uppercase tracking-wider">
                                            <i class="fa-solid fa-triangle-exclamation mr-0.5"></i> Larangan Mutlak
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs {{ $rule->is_strict ? 'text-red-950 font-medium' : 'text-gray-700' }} leading-relaxed">
                                    {{ $rule->description }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Print Footer -->
    <div class="hidden print:block text-center pt-8 border-t border-gray-300 text-xs text-gray-500">
        <p class="font-bold">Panitia Pelaksana Angkat Sumpah Profesi FK UNTAN &bull; Dicetak secara resmi melalui Portal Sumpah Profesi</p>
        <p class="text-[10px] mt-1">{{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

</div>

<style>
@media print {
    aside, nav, header, footer, button, .print\:hidden {
        display: none !important;
    }
    body, main {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
