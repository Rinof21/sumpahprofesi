@extends('layouts.app')

@section('title', 'Susunan Acara & Tata Tertib - ' . $period->name)

@section('content')
<div class="print:m-0 print:p-0">

    <!-- Header Actions (Hidden in Print) -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <a href="{{ route('token-access.portal') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Pendaftaran Peserta
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight">Susunan Acara & Tata Tertib Resmi Prosesi</h1>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">Rundown prosesi dan tata tertib resmi pelaksanaan sumpah profesi {{ $period->studyProgram->name }}.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center justify-center gap-2 transition-colors">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
            <a href="{{ route('token-access.portal') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-id-card text-xs"></i> Pendaftaran
            </a>
        </div>
    </div>

    <!-- Official Period Banner Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 print:border-none print:shadow-none print:p-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                        {{ $period->studyProgram->name }} ({{ $period->studyProgram->code }})
                    </span>
                    <span class="px-2.5 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px] font-bold">
                        Siklus: {{ $period->quarter_code }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold flex items-center gap-1 print:hidden">
                        <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Sesi: {{ $remainingMinutes }} mnt
                    </span>
                </div>
                <h2 class="text-xl font-black text-gray-900">{{ $period->name }}</h2>
                <p class="text-xs text-gray-600 mt-1 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1.5 font-bold text-gray-800">
                        <i class="fa-regular fa-calendar text-[#4e73df]"></i>
                        {{ $period->event_date->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="flex items-center gap-1.5 font-bold text-gray-800">
                        <i class="fa-regular fa-clock text-[#4e73df]"></i>
                        Mulai 07.30 WIB - Selesai
                    </span>
                </p>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <span class="px-3 py-1.5 rounded-lg bg-blue-50 text-[#4e73df] border border-blue-200 text-xs font-black">
                    <i class="fa-solid fa-list-check mr-1"></i> {{ $period->schedules->count() }} Agenda
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black">
                    <i class="fa-solid fa-shield-alt mr-1"></i> {{ $period->rules->count() }} Tata Tertib
                </span>
            </div>
        </div>

        <div class="bg-blue-50/70 border-l-4 border-[#4e73df] p-3.5 rounded-r-lg text-xs text-gray-700 leading-relaxed font-medium">
            <strong class="text-gray-900 font-bold">Pemberitahuan Resmi Panitia:</strong> Seluruh calon wisudawan sumpah dan pendamping keluarga diwajibkan memahami serta mematuhi seluruh susunan rundown acara dan pakta tata tertib ruangan di bawah ini demi kelancaran dan kekhidmatan sakral prosesi.
        </div>
    </div>

    <!-- Section 1: Susunan Acara (Rundown) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-8 print:border-none print:shadow-none print:p-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-4 border-b border-gray-200 gap-1">
            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-[#4e73df]"></i> Susunan Acara (Rundown Prosesi Resmi)
            </h3>
            <span class="text-xs text-gray-500 font-bold">Waktu Indonesia Barat (WIB)</span>
        </div>

        @if($period->schedules->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <i class="fa-solid fa-clock text-3xl mb-2 text-gray-300"></i>
                <p class="font-bold text-xs">Susunan acara resmi belum dirilis oleh panitia admin prodi.</p>
                <p class="text-[11px] text-gray-400">Silakan cek kembali secara berkala sebelum hari pelaksanaan.</p>
            </div>
        @else
            <!-- Mobile View: Modern Timeline Cards (Block on mobile, hidden on md+) -->
            <div class="block md:hidden space-y-3">
                @foreach($period->schedules as $index => $schedule)
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
                                    <span class="font-bold text-gray-800">{{ $schedule->person_in_charge ?? '-' }}</span>
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
                        @foreach($period->schedules as $index => $schedule)
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
                                <td class="px-3.5 py-3 text-gray-600 font-medium">
                                    {{ $schedule->person_in_charge ?? '-' }}
                                </td>
                                <td class="px-3.5 py-3 text-gray-500 text-[11px]">
                                    {{ $schedule->notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Section 2: Tata Tertib Resmi Aula -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 print:border-none print:shadow-none print:p-0">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
            <div>
                <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-shield-alt text-emerald-600"></i> Tata Tertib & Ketentuan Resmi Ruang Prosesi
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Wajib ditaati dan dipedomani oleh seluruh wisudawan dan keluarga pendamping.</p>
            </div>
            <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
                Pedoman Resmi
            </span>
        </div>

        @if($period->rules->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <i class="fa-solid fa-shield-cat text-3xl mb-2 text-gray-300"></i>
                <p class="font-bold text-xs">Ketentuan tata tertib belum diunggah oleh panitia admin prodi.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($period->rules as $rule)
                    <div class="p-4 rounded-xl border {{ $rule->is_strict ? 'bg-red-50/70 border-red-200 ring-1 ring-red-300/50' : 'bg-gray-50/80 border-gray-200' }} flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $rule->is_strict ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $rule->category }}
                                </span>
                                @if($rule->is_strict)
                                    <span class="px-2 py-0.5 rounded bg-red-600 text-white text-[9px] font-black uppercase tracking-wider flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Wajib / Ketat
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-bold">
                                        Ketentuan Umum
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-black text-sm {{ $rule->is_strict ? 'text-red-950' : 'text-gray-900' }} mb-1.5">
                                {{ $rule->title }}
                            </h4>
                            <p class="text-xs {{ $rule->is_strict ? 'text-red-900 font-medium' : 'text-gray-600' }} leading-relaxed">
                                {{ $rule->description }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Printable Official Signature Area (Only on Print) -->
    <div class="hidden print:block mt-12 pt-6 border-t-2 border-gray-300 text-xs">
        <div class="flex justify-between items-end">
            <div>
                <p class="font-bold text-gray-700">Dikeluarkan oleh:</p>
                <p class="font-black text-gray-900 uppercase">Panitia Sumpah Profesi FKIK UNTAN</p>
                <p class="text-[10px] text-gray-500">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
            <div class="text-center w-64">
                <p class="text-gray-700">Pontianak, {{ $period->event_date->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-gray-800 mt-1 mb-16">Ketua Panitia Pelaksana,</p>
                <p class="font-black text-gray-950 underline">( Panitia Sumpah Profesi )</p>
            </div>
        </div>
    </div>

</div>
@endsection
