@extends('layouts.app')

@section('title', 'Arsip ' . $period->name)

@section('content')
<!-- Back Button -->
<a href="{{ route('public.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4e73df] hover:underline mb-4">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke Portal Utama
</a>

<!-- Header Card -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                    {{ $period->studyProgram->name }} ({{ $period->studyProgram->code }})
                </span>
                <span class="px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">
                    Organisasi: {{ $period->studyProgram->organization }}
                </span>
            </div>

            <h1 class="text-2xl font-black text-gray-900">{{ $period->name }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Pelaksanaan Tanggal: <strong class="text-gray-800">{{ $period->event_date->format('d F Y') }}</strong> &bull; Kode Triwulan: <strong class="text-[#4e73df]">{{ $period->quarter_code }}</strong>
            </p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-2xl font-black text-[#4e73df]">{{ $totalCandidates }}</div>
            <div class="text-[10px] font-bold text-gray-500 uppercase">Lulusan Disumpah</div>
        </div>
    </div>
</div>

<!-- Speech Representative Spotlight (FR-E3) -->
@if($speechRep)
    <div class="mb-6 bg-blue-50 p-5 rounded-lg border-l-4 border-[#4e73df] flex flex-col sm:flex-row items-center gap-4 border border-blue-200">
        <div class="w-12 h-12 rounded-full bg-[#4e73df] text-white flex items-center justify-center text-xl font-bold shrink-0">
            <i class="fa-solid fa-microphone"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-[#4e73df] uppercase tracking-wider">Perwakilan Pesan & Kesan Wisudawan (Resmi 1 Orang)</div>
            <div class="text-base font-black text-gray-900">{{ $speechRep->full_name }} (NIM: {{ $speechRep->nim }})</div>
            <p class="text-xs text-gray-600 italic mt-0.5">"{{ $speechRep->speech_notes ?? 'Ditunjuk mewakili seluruh calon lulusan menyampaikan pesan dan kesan di hadapan Senat dan Organisasi Profesi.' }}"</p>
        </div>
    </div>
@endif

<!-- Statistics Grid (FR-G1 & FR-C2) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    
    <!-- Religion Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-hands-praying text-[#4e73df]"></i>
            Rekapitulasi Agama Peserta (Persuratan Rohaniwan)
        </h3>
        <div class="space-y-3">
            @foreach(['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
                @php
                    $count = $religionStats->get($rel, 0);
                    $percentage = $totalCandidates > 0 ? round(($count / $totalCandidates) * 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-bold text-gray-700">{{ $rel }}</span>
                        <span class="text-gray-500 font-semibold">{{ $count }} Peserta ({{ $percentage }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200">
                        <div class="bg-[#4e73df] h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Admission Path Breakdown -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-emerald-600"></i>
            Sebaran Jalur Masuk Kuliah Lulusan
        </h3>
        <div class="space-y-3">
            @foreach(['SNBP/SNMPTN', 'SNBT/SBMPTN', 'Mandiri', 'Kerjasama/Afirmasi'] as $path)
                @php
                    $count = $admissionStats->get($path, 0);
                    $percentage = $totalCandidates > 0 ? round(($count / $totalCandidates) * 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-bold text-gray-700">{{ $path }}</span>
                        <span class="text-gray-500 font-semibold">{{ $count }} Peserta ({{ $percentage }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Candidate List Table Section -->
<div class="mb-6 bg-white rounded-lg border border-gray-200 shadow-sm p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
        <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-users text-[#4e73df]"></i>
            Daftar Peserta Sumpah & Calon Lulusan ({{ $totalCandidates }} Orang)
        </h3>
        <span class="px-2.5 py-0.5 rounded bg-blue-50 text-[#4e73df] border border-blue-200 text-[10px] font-bold">
            Resmi Terdaftar
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                <tr>
                    <th class="px-3 py-2.5 w-10 text-center">No</th>
                    <th class="px-4 py-2.5">NIM & Nama Lengkap</th>
                    <th class="px-4 py-2.5">Jalur Masuk</th>
                    <th class="px-4 py-2.5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($candidates as $index => $c)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-3 py-3 text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="font-black text-gray-900 flex items-center gap-2">
                                {{ $c->full_name }}
                                @if($c->is_speech_rep)
                                    <span class="px-2 py-0.5 bg-blue-100 text-[#4e73df] rounded text-[10px] font-black border border-blue-200" title="Perwakilan Pesan & Kesan Wisudawan">
                                        <i class="fa-solid fa-microphone mr-1"></i>Perwakilan
                                    </span>
                                @endif
                            </div>
                            <div class="text-[11px] font-mono text-[#4e73df] font-bold mt-0.5">NIM: {{ $c->nim }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ $c->admission_path ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-extrabold text-[10px]">
                                <i class="fa-solid fa-check-circle mr-1"></i>Disumpah
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-400 font-medium">Belum ada data peserta yang terdaftar untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- YouTube Video Documentation (FR-G3) -->
@if($period->youtube_url)
    <div class="mb-6">
        <h3 class="text-sm font-extrabold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-brands fa-youtube text-red-600 text-lg"></i>
            Dokumentasi Video Live Processi YouTube
        </h3>
        <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm overflow-hidden">
            @php
                preg_match("/(?:v=|\/embed\/|\/1\/|youtu.be\/|\/v\/|\/e\/|watch\?v=|&v=)([^#&?]*)/", $period->youtube_url, $matches);
                $youtubeId = $matches[1] ?? null;
            @endphp
            @if($youtubeId)
                <div class="aspect-video w-full rounded-lg overflow-hidden border border-gray-200">
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}" title="YouTube Video Documentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            @else
                <div class="p-4 text-center text-xs text-gray-500">
                    Link YouTube: <a href="{{ $period->youtube_url }}" target="_blank" class="text-[#4e73df] underline">{{ $period->youtube_url }}</a>
                </div>
            @endif
        </div>
    </div>
@endif

<!-- Top 10 Curated Photos Gallery (FR-G2) -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-camera text-[#4e73df]"></i>
            Kurasi 10 Foto Terbaik Prosesi Sakral
        </h3>
        <span class="px-2.5 py-0.5 rounded bg-gray-100 border border-gray-300 text-gray-700 text-xs font-bold">
            {{ $period->eventPhotos->count() }} / 10 Foto
        </span>
    </div>

    @if($period->eventPhotos->isEmpty())
        <div class="bg-white p-6 rounded-lg text-center border border-gray-200 text-xs text-gray-400">
            Belum ada foto kurasi pasca-acara yang diunggah untuk periode ini.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($period->eventPhotos as $photo)
                <div class="bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm group hover:shadow-md transition-shadow">
                    <div class="aspect-video w-full overflow-hidden bg-gray-100 relative">
                        <img src="{{ Str::startsWith($photo->photo_path, 'http') ? $photo->photo_path : asset('storage/' . $photo->photo_path) }}" 
                             alt="Foto Prosesi" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 left-2 px-2 py-0.5 rounded bg-black/70 text-white text-[10px] font-black">
                            #{{ $photo->sort_order }}
                        </div>
                    </div>
                    @if($photo->caption)
                        <div class="p-3 text-xs text-gray-700 font-semibold border-t border-gray-100">
                            {{ $photo->caption }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
