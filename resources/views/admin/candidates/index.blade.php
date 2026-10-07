@extends('layouts.app')

@section('title', 'Validasi Peserta & Rekapitulasi Rohaniwan')

@section('content')
<!-- Page Heading -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Validasi Candidates Peserta & Rekapitulasi Rohaniwan</h1>
        <p class="text-xs font-semibold text-gray-500">Verifikasi pakta tata tertib, pemetaan rohaniwan per agama, dan penunjukan perwakilan pesan & kesan wisudawan.</p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.candidates.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2 transition-colors">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- Cleric Breakdown Summary Cards (FR-C2) -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
    <h2 class="text-xs font-black text-[#4e73df] uppercase tracking-wider mb-3 flex items-center gap-2">
        <i class="fa-solid fa-hands-praying"></i> Hasil Rekapitulasi Jumlah Peserta Per Agama (Surat Tugas Resmi Rohaniwan)
    </h2>
    
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
        @foreach(['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
            @php
                $rec = $clericRecap->firstWhere('religion', $rel);
                $totalRel = $rec ? $rec->total : 0;
            @endphp
            <div class="p-3 rounded bg-gray-50 border border-gray-200 text-center">
                <div class="text-xl font-black text-[#4e73df]">{{ $totalRel }}</div>
                <div class="text-[10px] font-bold text-gray-600 uppercase mt-0.5">{{ $rel }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.candidates.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pilih Periode Sumpah</label>
            <select name="period_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:outline-none focus:ring-2 focus:ring-[#4e73df]">
                @foreach($periods as $per)
                    <option value="{{ $per->id }}" {{ $selectedPeriodId == $per->id ? 'selected' : '' }}>
                        {{ $per->name }} ({{ $per->status }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Filter Agama</label>
            <select name="religion" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:outline-none focus:ring-2 focus:ring-[#4e73df]">
                <option value="">-- Semua Agama --</option>
                @foreach(['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
                    <option value="{{ $rel }}" {{ request('religion') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Cari Nama / NIM</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kandidat atau NIM..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#4e73df]">
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">Cari</button>
            </div>
        </div>
    </form>
</div>

<!-- Candidates Table Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
        <h6 class="text-xs font-black text-gray-700 uppercase tracking-wider m-0">Daftar Candidates Peserta</h6>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-700 uppercase font-bold border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 w-8 text-center">No.</th>
                    <th class="px-4 py-3">Nama & NIM</th>
                    <th class="px-4 py-3">Agama</th>
                    <th class="px-4 py-3">Jalur Masuk</th>
                    <th class="px-4 py-3">Pakta Tata Tertib</th>
                    <th class="px-4 py-3">Slide PPT</th>
                    <th class="px-4 py-3 text-center">Perwakilan Pesan-Kesan</th>
                    <th class="px-4 py-3 text-center">Koordinator Sumpah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-800">
                @forelse($candidates as $i => $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center text-[11px] font-black text-gray-400 w-8">
                            {{ $candidates->firstItem() + $i }}
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-900">
                            <div>{{ $c->full_name }}</div>
                            <div class="text-[10px] text-[#4e73df] font-mono">NIM: {{ $c->nim }}</div>
                            @if($c->is_oath_coordinator)
                                <span class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.5 bg-indigo-100 text-indigo-800 border border-indigo-200 rounded text-[9px] font-black">
                                    <i class="fa-solid fa-star text-[8px]"></i> Koordinator Sumpah
                                </span>
                            @endif
                            @if($c->is_speech_rep)
                                <span class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded text-[9px] font-black">
                                    <i class="fa-solid fa-microphone text-[8px]"></i> Perwakilan Pesan & Kesan
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $c->religion }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $c->admission_path }}</td>
                        <td class="px-4 py-3">
                            @if($c->agreed_rules)
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <i class="fa-solid fa-check"></i> Disetujui
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-800 border border-red-300">
                                    Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($c->ppt_file_path)
                                <a href="{{ asset('storage/' . $c->ppt_file_path) }}" target="_blank" class="text-[#4e73df] font-bold underline hover:text-[#2e59d9]">
                                    <i class="fa-solid fa-file-powerpoint"></i> PPT Terunggah
                                </a>
                            @else
                                <span class="text-gray-400 italic">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($c->is_speech_rep)
                                <span class="px-3 py-1 bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-black rounded-full inline-flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-microphone"></i> Perwakilan Resmi (1 Orang)
                                </span>
                            @else
                                <form action="{{ route('admin.candidates.speech-rep', $c->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-gray-100 hover:bg-[#4e73df] hover:text-white text-gray-700 text-[10px] font-bold rounded border border-gray-300 transition-colors">
                                        Tunjuk Perwakilan
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($c->is_oath_coordinator)
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-900 border border-indigo-300 text-[10px] font-black rounded-full inline-flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-star"></i> Koordinator (1 Orang)
                                </span>
                            @else
                                <form action="{{ route('admin.candidates.oath-coordinator', $c->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-gray-100 hover:bg-indigo-600 hover:text-white text-gray-700 text-[10px] font-bold rounded border border-gray-300 transition-colors">
                                        Tunjuk Koordinator
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-400">Tidak ada data kandidat ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 bg-gray-50 border-t border-gray-200">
        {{ $candidates->withQueryString()->links() }}
    </div>
</div>
@endsection
