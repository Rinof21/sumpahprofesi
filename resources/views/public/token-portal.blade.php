@extends('layouts.app')

@section('title', 'Portal Pendaftaran Peserta Sumpah')

@section('content')
<!-- Header Banner Periode -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
        <div>
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                    PRODI: {{ $period->studyProgram->name }} ({{ $period->studyProgram->code }})
                </span>
                <span class="px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">
                    Siklus: {{ $period->quarter_code }} &bull; {{ $period->event_date->format('d M Y') }}
                </span>
                <span class="px-2.5 py-0.5 rounded bg-purple-100 text-purple-700 font-mono font-black text-[10px]" title="Token Akses Periode">
                    <i class="fa-solid fa-key mr-1"></i>{{ $period->access_token }}
                </span>
                <span class="px-2.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold flex items-center gap-1" title="Sesi token berlaku 60 menit (bebas keluar-masuk)">
                    <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Sesi: {{ $remainingMinutes }} mnt
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

            <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">{{ $period->name }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Portal Pendaftaran & Pengisian Data Bersama Mahasiswa (Akses Token Active &bull; Bebas Keluar-Masuk 60 Menit).
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            @if($period->drive_url)
                <a href="{{ $period->drive_url }}" target="_blank" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center justify-center gap-2 transition-all">
                    <i class="fa-brands fa-google-drive text-sm"></i> Buka Drive PPT Admin
                </a>
            @endif
            <form action="{{ route('token-access.reset') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit" onclick="return confirm('Sesi token 60 menit akan direset. Apakah Anda ingin keluar dan memasukkan token baru?')" class="w-full px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors flex items-center justify-center gap-1">
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
    
    <!-- Left Column (Wide, lg:col-span-2): Daftar Peserta Terdaftar -->
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-users text-[#4e73df]"></i> Daftar Peserta Terdaftar ({{ $period->candidates->count() }} Mahasiswa)
                    </h2>
                    <p class="text-[11px] text-gray-500 font-semibold mt-0.5">
                        Nomor urut dapat diatur naik/turun, serta peserta dapat menunjuk <strong>Koordinator Sumpah</strong> dan <strong>Pembaca Pesan & Kesan</strong> melalui tombol <em>Tunjuk Koordinator & Peran</em>.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    @if(!$period->is_locked && $period->candidates->count() > 0)
                        <button type="button" onclick="openModal('modal-assign-roles')" class="px-3 py-2 sm:py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-xs">
                            <i class="fa-solid fa-user-check text-xs"></i> Tunjuk Peran
                        </button>
                    @endif

                    @if(!$period->is_locked && $period->candidates->count() > 1)
                        <button type="button" onclick="openModal('modal-reorder-candidates')" class="px-3 py-2 sm:py-1.5 bg-blue-50 hover:bg-blue-100 text-[#4e73df] border border-blue-200 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-xs">
                            <i class="fa-solid fa-arrow-down-1-9 text-xs"></i> Atur Semua Nomor Urut
                        </button>
                    @endif

                    @if(!$period->is_locked)
                        <button type="button" onclick="openModal('modal-create-candidate')" class="px-3.5 py-2 sm:py-1.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-user-plus text-xs"></i> Tambah Peserta
                        </button>
                    @else
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 border border-gray-200 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-lock text-xs"></i> Pendaftaran Dikunci
                        </span>
                    @endif
                </div>
            </div>

            <!-- Mobile View: Clean Touch-friendly Cards (Block on mobile, hidden on md+) -->
            <div class="block md:hidden space-y-3">
                @forelse($period->candidates as $i => $c)
                    <div class="bg-white border border-gray-200 rounded-xl p-3.5 shadow-2xs space-y-3 hover:border-blue-300 transition-colors">
                        <!-- Top Row: Nomor Urut Badge + Move Buttons + Action Buttons -->
                        <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 text-[#4e73df] font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ $c->sort_order ?: ($i + 1) }}
                                </span>

                                @if(!$period->is_locked && $period->candidates->count() > 1)
                                    <div class="flex items-center gap-1">
                                        @if(!$loop->first)
                                            <form action="{{ route('token-access.move', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" title="Naikkan Urutan (Ke Atas)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-[#4e73df] hover:text-white text-gray-600 rounded-md text-[10px] active:scale-95 transition-all">
                                                    <i class="fa-solid fa-chevron-up"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="w-7 h-7 block"></span>
                                        @endif

                                        @if(!$loop->last)
                                            <form action="{{ route('token-access.move', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" title="Turunkan Urutan (Ke Bawah)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-[#4e73df] hover:text-white text-gray-600 rounded-md text-[10px] active:scale-95 transition-all">
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="w-7 h-7 block"></span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if(!$period->is_locked)
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" onclick="openEditCandidateModal('{{ $c->id }}', '{{ addslashes($c->nim) }}', '{{ addslashes($c->full_name) }}', '{{ addslashes($c->birth_place) }}', '{{ $c->birth_date->format('Y-m-d') }}', '{{ addslashes($c->father_name) }}', '{{ addslashes($c->mother_name) }}', '{{ $c->admission_path }}', '{{ $c->religion }}', '{{ $c->sort_order ?: ($i + 1) }}', {{ $c->is_oath_coordinator ? 'true' : 'false' }}, {{ $c->is_speech_rep ? 'true' : 'false' }})" class="p-1.5 px-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold rounded-lg text-xs transition-colors" title="Edit Biodata & Peran">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                    </button>

                                    <form action="{{ route('token-access.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus data peserta {{ $c->full_name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 px-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold rounded-lg text-xs transition-colors" title="Hapus Peserta">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-[10px]"><i class="fa-solid fa-lock"></i> Dikunci</span>
                            @endif
                        </div>

                        <!-- Candidate Identity -->
                        <div>
                            <div class="font-black text-sm text-gray-900 leading-snug">{{ $c->full_name }}</div>
                            <div class="text-[11px] font-mono text-[#4e73df] font-bold mt-0.5">NIM: {{ $c->nim }}</div>

                            @if($c->is_oath_coordinator || $c->is_speech_rep)
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @if($c->is_oath_coordinator)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 text-indigo-800 border border-indigo-200 rounded text-[9px] font-black">
                                            <i class="fa-solid fa-star text-[8px]"></i> Koordinator Sumpah
                                        </span>
                                    @endif
                                    @if($c->is_speech_rep)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded text-[9px] font-black">
                                            <i class="fa-solid fa-microphone text-[8px]"></i> Perwakilan Pesan & Kesan
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Details: Agama, TTL & Orang Tua -->
                        <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50/80 p-2.5 rounded-lg border border-gray-100">
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Agama & TTL</span>
                                <div class="mt-1">
                                    <span class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-bold text-gray-700">{{ $c->religion }}</span>
                                    <div class="text-[11px] text-gray-600 mt-1 leading-tight">{{ $c->birth_place }}, {{ $c->birth_date->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Orang Tua</span>
                                <div class="text-[11px] text-gray-700 mt-1 space-y-0.5">
                                    <div class="truncate">Ayah: <span class="font-bold text-gray-900">{{ $c->father_name }}</span></div>
                                    <div class="truncate">Ibu: <span class="font-bold text-gray-900">{{ $c->mother_name }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 border border-dashed border-gray-200 rounded-xl">
                        <i class="fa-solid fa-users text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-xs text-gray-500 mb-3">Belum ada peserta yang mendaftar pada periode ini.</p>
                        @if(!$period->is_locked)
                            <button type="button" onclick="openModal('modal-create-candidate')" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white text-xs font-bold rounded-lg shadow-sm inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-plus-circle"></i> Tambah Peserta Sekarang
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Desktop View: Table (Hidden on mobile, visible on md and up) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5 w-16 text-center">No.</th>
                            <th class="px-3 py-2.5">NIM & Nama Peserta</th>
                            <th class="px-3 py-2.5">Agama & TTL</th>
                            <th class="px-3 py-2.5">Orang Tua</th>
                            <th class="px-3 py-2.5 text-right">Aksi Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($period->candidates as $i => $c)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-3 text-center w-16">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if(!$period->is_locked && $period->candidates->count() > 1)
                                            <div class="flex flex-col gap-0.5">
                                                @if(!$loop->first)
                                                    <form action="{{ route('token-access.move', $c->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="direction" value="up">
                                                        <button type="submit" title="Naikkan Urutan (Ke Atas)" class="w-5 h-3.5 flex items-center justify-center bg-gray-100 hover:bg-[#4e73df] hover:text-white text-gray-600 rounded text-[8px] transition-colors">
                                                            <i class="fa-solid fa-chevron-up"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="w-5 h-3.5 block"></span>
                                                @endif

                                                @if(!$loop->last)
                                                    <form action="{{ route('token-access.move', $c->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="direction" value="down">
                                                        <button type="submit" title="Turunkan Urutan (Ke Bawah)" class="w-5 h-3.5 flex items-center justify-center bg-gray-100 hover:bg-[#4e73df] hover:text-white text-gray-600 rounded text-[8px] transition-colors">
                                                            <i class="fa-solid fa-chevron-down"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="w-5 h-3.5 block"></span>
                                                @endif
                                            </div>
                                        @endif

                                        <span class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-200 text-[#4e73df] font-black text-xs flex items-center justify-center shadow-xs">
                                            {{ $c->sort_order ?: ($i + 1) }}
                                        </span>
                                    </div>
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
                                <td class="px-3 py-3 text-right whitespace-nowrap">
                                    @if(!$period->is_locked)
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" onclick="openEditCandidateModal('{{ $c->id }}', '{{ addslashes($c->nim) }}', '{{ addslashes($c->full_name) }}', '{{ addslashes($c->birth_place) }}', '{{ $c->birth_date->format('Y-m-d') }}', '{{ addslashes($c->father_name) }}', '{{ addslashes($c->mother_name) }}', '{{ $c->admission_path }}', '{{ $c->religion }}', '{{ $c->sort_order ?: ($i + 1) }}', {{ $c->is_oath_coordinator ? 'true' : 'false' }}, {{ $c->is_speech_rep ? 'true' : 'false' }})" class="p-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold rounded text-xs transition-colors" title="Edit Biodata & Peran">
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
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-gray-400 font-medium">
                                    <div class="max-w-xs mx-auto py-3">
                                        <i class="fa-solid fa-users text-3xl text-gray-300 mb-2 block"></i>
                                        <p class="text-xs text-gray-500 mb-2">Belum ada peserta yang mendaftar pada periode ini.</p>
                                        @if(!$period->is_locked)
                                            <button type="button" onclick="openModal('modal-create-candidate')" class="px-3 py-1.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white text-xs font-bold rounded-lg shadow-sm inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-plus-circle"></i> Tambah Peserta Sekarang
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Right Column (Sidebar, lg:col-span-1): Quick Access Banners -->
    <div class="lg:col-span-1 space-y-4">
        
        <!-- Banner Susunan Acara & Tata Tertib -->
        <div class="bg-gradient-to-br from-white to-blue-50/50 rounded-xl shadow-sm border border-blue-200 p-5 flex flex-col justify-between hover:border-blue-300 transition-all group">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black">
                            {{ $period->schedules->count() }} Agenda
                        </span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-black">
                            {{ $period->rules->count() }} Tata Tertib
                        </span>
                    </div>
                </div>
                <h3 class="font-black text-sm text-gray-900 group-hover:text-[#4e73df] transition-colors">
                    Susunan Acara & Tata Tertib
                </h3>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Lihat rundown lengkap agenda prosesi sumpah profesi dan tata tertib resmi ruangan aula.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-blue-100 flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400">Tersedia Cetak PDF</span>
                <a href="{{ route('token-access.event-schedule') }}" class="px-3.5 py-1.5 rounded-lg bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                    <span>Buka Halaman</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Banner Naskah Lafal Sumpah -->
        <div class="bg-gradient-to-br from-white to-blue-50/50 rounded-xl shadow-sm border border-blue-200 p-5 flex flex-col justify-between hover:border-blue-300 transition-all group">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-scroll"></i>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                        {{ $period->studyProgram->code }} &bull; {{ $period->studyProgram->degree_title }}
                    </span>
                </div>
                <h3 class="font-black text-sm text-gray-900 group-hover:text-[#4e73df] transition-colors">
                    Naskah Lafal Sumpah Official
                </h3>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Teks resmi pengucapan sumpah per agama & dokumen PDF resmi untuk dibaca atau diunduh.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-blue-100 flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400">PDF & Panduan Agama</span>
                <a href="{{ route('token-access.oath-script') }}" class="px-3.5 py-1.5 rounded-lg bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                    <span>Buka Halaman</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Banner Rekomendasi Vendor Fotografer & Streaming Live YouTube -->
        <div class="bg-gradient-to-br from-white to-blue-50/50 rounded-xl shadow-sm border border-blue-200 p-5 flex flex-col justify-between hover:border-blue-300 transition-all group">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-black uppercase">
                        {{ $vendors->count() }} Mitra Rekomendasi
                    </span>
                </div>
                <h3 class="font-black text-sm text-gray-900 group-hover:text-[#4e73df] transition-colors">
                    Rekomendasi Fotografer & Live Streaming
                </h3>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Kontak WhatsApp fotografer ruangan & penyedia live streaming YouTube langganan peserta sumpah profesi.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-blue-100 flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400">Kontak WA Langsung</span>
                <button type="button" onclick="openModal('modal-vendor-reference')" class="px-3.5 py-1.5 rounded-lg bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                    <i class="fa-solid fa-list-check text-xs"></i>
                    <span>Lihat Kontak Vendor</span>
                </button>
            </div>
        </div>

    </div>

</div>

<!-- Modal Tambah Data Peserta Baru -->
@if(!$period->is_locked)
<div id="modal-create-candidate" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-xl w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Tambah Data Peserta Sumpah Baru
                </h3>
                <p class="text-[11px] text-blue-100 font-semibold mt-0.5">
                    Periode: {{ $period->name }} &bull; {{ $period->studyProgram->name }}
                </p>
            </div>
            <button type="button" onclick="closeModal('modal-create-candidate')" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form action="{{ route('token-access.store') }}" method="POST" class="p-5 space-y-3.5 overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="period_id" value="{{ $period->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1" title="Nomor Urut Panggilan/Daftar Peserta">
                            No. Urut
                        </label>
                        <input type="number" name="sort_order" min="1" value="{{ old('sort_order', ($period->candidates->max('sort_order') ?? 0) + 1) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-extrabold text-center focus:ring-2 focus:ring-[#4e73df]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 uppercase mb-1">NIM Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" required placeholder="Contoh: 22010119001" value="{{ old('nim') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required placeholder="Nama Lengkap Mahasiswa" value="{{ old('full_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" name="birth_place" required placeholder="Kota Lahir" value="{{ old('birth_place') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" name="birth_date" required value="{{ old('birth_date') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Ayah <span class="text-red-500">*</span></label>
                    <input type="text" name="father_name" required placeholder="Nama Kandung" value="{{ old('father_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Ibu <span class="text-red-500">*</span></label>
                    <input type="text" name="mother_name" required placeholder="Nama Kandung" value="{{ old('mother_name') }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Jalur Masuk <span class="text-red-500">*</span></label>
                    <select name="admission_path" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                        <option value="SNBP/SNMPTN" {{ old('admission_path') == 'SNBP/SNMPTN' ? 'selected' : '' }}>SNBP/SNMPTN</option>
                        <option value="SNBT/SBMPTN" {{ old('admission_path') == 'SNBT/SBMPTN' ? 'selected' : '' }}>SNBT/SBMPTN</option>
                        <option value="Mandiri" {{ old('admission_path') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="Kerjasama/Afirmasi" {{ old('admission_path') == 'Kerjasama/Afirmasi' ? 'selected' : '' }}>Kerjasama</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Agama <span class="text-red-500">*</span></label>
                    <select name="religion" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                        <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Protestan" {{ old('religion') == 'Protestan' ? 'selected' : '' }}>Protestan</option>
                        <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                    </select>
                </div>

                <div class="sm:col-span-2 p-3 bg-indigo-50/60 border border-indigo-200 rounded-lg space-y-2">
                    <div class="text-[10px] font-black text-indigo-900 uppercase tracking-wider">Peran Sumpah Wisudawan (Opsional)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-indigo-100 hover:border-indigo-300 cursor-pointer">
                            <input type="checkbox" name="is_oath_coordinator" value="1" {{ old('is_oath_coordinator') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-bold text-gray-800 text-[11px] flex items-center gap-1.5">
                                <i class="fa-solid fa-star text-indigo-600 text-[10px]"></i> Koordinator Sumpah
                            </span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-amber-100 hover:border-amber-300 cursor-pointer">
                            <input type="checkbox" name="is_speech_rep" value="1" {{ old('is_speech_rep') ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                            <span class="font-bold text-gray-800 text-[11px] flex items-center gap-1.5">
                                <i class="fa-solid fa-microphone text-amber-600 text-[10px]"></i> Pembaca Pesan & Kesan
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-create-candidate')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs rounded shadow-sm flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-plus-circle"></i> Simpan Data Peserta
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Edit Biodata Peserta -->
<div id="modal-edit-candidate" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-xl w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-yellow-600 px-5 py-4 text-white flex items-center justify-between shrink-0">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Biodata Peserta Sumpah
            </h3>
            <button type="button" onclick="closeModal('modal-edit-candidate')" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form id="form-edit-candidate" method="POST" class="p-5 space-y-3 overflow-y-auto flex-1">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nomor Urut</label>
                        <input type="number" id="edit-cand-sort-order" name="sort_order" min="1" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-extrabold text-center focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 uppercase mb-1">NIM</label>
                        <input type="text" id="edit-cand-nim" name="nim" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
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

                <div class="sm:col-span-2 p-3 bg-indigo-50/60 border border-indigo-200 rounded-lg space-y-2">
                    <div class="text-[10px] font-black text-indigo-900 uppercase tracking-wider">Peran Sumpah Wisudawan (Opsional)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-indigo-100 hover:border-indigo-300 cursor-pointer">
                            <input type="checkbox" name="is_oath_coordinator" id="edit-cand-oath-coordinator" value="1" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-bold text-gray-800 text-[11px] flex items-center gap-1.5">
                                <i class="fa-solid fa-star text-indigo-600 text-[10px]"></i> Koordinator Sumpah
                            </span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-amber-100 hover:border-amber-300 cursor-pointer">
                            <input type="checkbox" name="is_speech_rep" id="edit-cand-speech-rep" value="1" class="rounded text-amber-600 focus:ring-amber-500">
                            <span class="font-bold text-gray-800 text-[11px] flex items-center gap-1.5">
                                <i class="fa-solid fa-microphone text-amber-600 text-[10px]"></i> Pembaca Pesan & Kesan
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modal-edit-candidate')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs rounded shadow-sm transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Penunjukan Koordinator Sumpah & Pembaca Pesan Kesan -->
@if(!$period->is_locked && $period->candidates->count() > 0)
<div id="modal-assign-roles" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-user-check"></i> Penunjukan Koordinator Sumpah & Pesan Kesan
                </h3>
                <p class="text-[11px] text-blue-100 font-semibold mt-0.5">
                    Tentukan perwakilan mahasiswa untuk memimpin sumpah dan menyampaikan pesan & kesan di aula
                </p>
            </div>
            <button type="button" onclick="closeModal('modal-assign-roles')" class="text-white/80 hover:text-white text-xl font-bold leading-none">&times;</button>
        </div>

        <form action="{{ route('token-access.assign-roles') }}" method="POST" class="p-5 space-y-4 overflow-y-auto flex-1 text-xs">
            @csrf

            <!-- Koordinator Sumpah Field -->
            <div class="p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-xl space-y-1.5">
                <label for="select_oath_coordinator" class="font-extrabold text-indigo-950 flex items-center gap-1.5 uppercase text-[11px]">
                    <i class="fa-solid fa-star text-indigo-600"></i> Koordinator Sumpah Wisudawan (1 Orang)
                </label>
                <p class="text-[11px] text-indigo-700 leading-relaxed">
                    Bertugas berdiri di depan panggung memegang mikrofon dan memimpin rekan sejawat melafalkan naskah sumpah profesi.
                </p>
                <select id="select_oath_coordinator" name="oath_coordinator_id" class="w-full mt-1 px-3 py-2 bg-white border border-indigo-300 rounded-lg text-xs font-bold text-gray-900 focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Belum Ditunjuk (Kosongkan) --</option>
                    @foreach($period->candidates as $idx => $cand)
                        <option value="{{ $cand->id }}" {{ $cand->is_oath_coordinator ? 'selected' : '' }}>
                            #{{ $cand->sort_order ?: ($idx + 1) }} - {{ $cand->full_name }} (NIM: {{ $cand->nim }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pembaca Pesan & Kesan Field -->
            <div class="p-3.5 bg-amber-50/70 border border-amber-200 rounded-xl space-y-1.5">
                <label for="select_speech_rep" class="font-extrabold text-amber-950 flex items-center gap-1.5 uppercase text-[11px]">
                    <i class="fa-solid fa-microphone text-amber-600"></i> Pembaca Pesan & Kesan Wisudawan (1 Orang)
                </label>
                <p class="text-[11px] text-amber-700 leading-relaxed">
                    Bertugas maju ke podium mewakili seluruh wisudawan menyampaikan sambutan pesan dan kesan selama studi.
                </p>
                <select id="select_speech_rep" name="speech_rep_id" class="w-full mt-1 px-3 py-2 bg-white border border-amber-300 rounded-lg text-xs font-bold text-gray-900 focus:ring-2 focus:ring-amber-500">
                    <option value="">-- Belum Ditunjuk (Kosongkan) --</option>
                    @foreach($period->candidates as $idx => $cand)
                        <option value="{{ $cand->id }}" {{ $cand->is_speech_rep ? 'selected' : '' }}>
                            #{{ $cand->sort_order ?: ($idx + 1) }} - {{ $cand->full_name }} (NIM: {{ $cand->nim }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modal-assign-roles')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-black text-xs rounded-xl shadow-sm flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Penunjukan Peran</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Atur Semua Nomor Urut Peserta -->
@if(!$period->is_locked && $period->candidates->count() > 1)
<div id="modal-reorder-candidates" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-indigo-700 px-5 py-3.5 text-white flex items-center justify-between shrink-0">
            <h3 class="font-bold text-xs uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-arrow-down-1-9"></i> Atur Nomor Urut Seluruh Peserta
            </h3>
            <button type="button" onclick="closeModal('modal-reorder-candidates')" class="text-white/80 hover:text-white text-lg leading-none">&times;</button>
        </div>

        <form action="{{ route('token-access.reorder') }}" method="POST" class="p-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <p class="text-xs text-gray-500">Sesuaikan nomor urut masing-masing atau urutkan otomatis.</p>
                <button type="button" onclick="autoNumberCandidates()" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-[11px] font-bold shrink-0 transition-colors">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Urutkan 1, 2, 3..
                </button>
            </div>

            <div class="max-h-72 overflow-y-auto space-y-2 pr-1">
                @foreach($period->candidates as $idx => $cand)
                    <div class="flex items-center justify-between p-2.5 bg-gray-50 border border-gray-200 rounded-lg gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded bg-gray-200 text-gray-700 text-[10px] font-black flex items-center justify-center shrink-0">
                                #{{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="text-xs font-bold text-gray-900 truncate">{{ $cand->full_name }}</div>
                                <div class="text-[10px] text-gray-500 font-mono">NIM: {{ $cand->nim }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">No. Urut:</label>
                            <input type="number" name="orders[{{ $cand->id }}]" min="1" value="{{ $cand->sort_order ?: ($idx + 1) }}" class="reorder-input w-16 px-2.5 py-1 bg-white border border-gray-300 rounded text-xs font-bold text-center text-gray-900 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal('modal-reorder-candidates')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded shadow-sm transition-colors">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Urutan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Upload PPT (Unified) -->
@if(!$period->is_locked)
<div id="modal-upload-ppt" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-cyan-700 px-4 py-3 text-white flex items-center justify-between">
            <h3 class="font-bold text-xs uppercase tracking-wider flex items-center gap-2 truncate pr-2">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span id="modal-upload-ppt-title">Upload Slide PPT</span>
            </h3>
            <button type="button" onclick="closeModal('modal-upload-ppt')" class="text-white/80 hover:text-white text-xl leading-none shrink-0">&times;</button>
        </div>
        <form id="form-upload-ppt" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih File PPT (.ppt, .pptx, .pdf max 20MB)</label>
                <input type="file" name="ppt_file" required accept=".ppt,.pptx,.pdf" class="w-full text-xs text-gray-500">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeModal('modal-upload-ppt')" class="px-3 py-1.5 bg-gray-100 text-xs font-bold rounded">Batal</button>
                <button type="submit" class="px-3 py-1.5 bg-cyan-700 text-white text-xs font-bold rounded">Unggah</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Rekomendasi Vendor Fotografer & Streaming Live YouTube -->
<div id="modal-vendor-reference" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-2xl w-full max-h-[92vh] flex flex-col overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-video"></i> Rekomendasi Vendor Fotografer & Streaming YouTube
                </h3>
                <p class="text-[11px] text-blue-100 font-semibold mt-0.5">
                    Daftar kontak rekomendasi panitia yang biasa disewa mahasiswa untuk prosesi sumpah profesi
                </p>
            </div>
            <button type="button" onclick="closeModal('modal-vendor-reference')" class="text-white/80 hover:text-white text-xl font-bold leading-none">&times;</button>
        </div>

        <div class="p-5 overflow-y-auto space-y-3.5 flex-1">
            @if($vendors->isEmpty())
                <div class="p-8 text-center text-gray-500 text-xs">
                    <i class="fa-solid fa-camera-slash text-3xl text-gray-300 mb-2 block"></i>
                    Belum ada kontak vendor yang dimasukkan panitia prodi.
                </div>
            @else
                @foreach($vendors as $vendor)
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 hover:bg-white hover:border-emerald-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                @if($vendor->category === 'live_streaming')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-700">
                                        <i class="fa-brands fa-youtube text-red-600"></i> Live Streaming YouTube
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-700">
                                        <i class="fa-solid fa-camera text-emerald-600"></i> Fotografer Ruangan
                                    </span>
                                @endif

                                @if($vendor->price_estimate)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                        {{ $vendor->price_estimate }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-sm font-black text-gray-900">{{ $vendor->name }}</h4>

                            <div class="text-xs text-gray-600 flex flex-wrap items-center gap-x-3 gap-y-1">
                                @if($vendor->contact_person)
                                    <span>PIC: <strong>{{ $vendor->contact_person }}</strong></span>
                                @endif
                                <span>WA: <strong class="font-mono text-gray-900">{{ $vendor->whatsapp_number }}</strong></span>
                            </div>

                            @if($vendor->description)
                                <p class="text-[11px] text-gray-500 leading-relaxed pt-1">
                                    {{ $vendor->description }}
                                </p>
                            @endif

                            <div class="flex items-center gap-3 pt-1 text-[11px]">
                                @if($vendor->instagram_handle)
                                    <a href="https://instagram.com/{{ ltrim($vendor->instagram_handle, '@') }}" target="_blank" class="font-bold text-pink-600 hover:underline flex items-center gap-1">
                                        <i class="fa-brands fa-instagram"></i>
                                        <span>{{ $vendor->instagram_handle }}</span>
                                    </a>
                                @endif

                                @if($vendor->youtube_channel)
                                    <a href="{{ $vendor->youtube_channel }}" target="_blank" class="font-bold text-red-600 hover:underline flex items-center gap-1">
                                        <i class="fa-brands fa-youtube"></i>
                                        <span>Channel YouTube</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 sm:self-center">
                            <a href="{{ $vendor->whatsapp_url }}" target="_blank" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>Hubungi via WA</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-gray-500">
            <span>Disimpan sebagai rekomendasi resmi panitia</span>
            <button type="button" onclick="closeModal('modal-vendor-reference')" class="px-3.5 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg text-xs transition-colors">
                Tutup
            </button>
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

    function openEditCandidateModal(id, nim, name, place, date, father, mother, path, religion, sortOrder, isOathCoordinator, isSpeechRep) {
        document.getElementById('form-edit-candidate').action = "{{ url('pendaftaran-token/peserta') }}/" + id;
        document.getElementById('edit-cand-sort-order').value = sortOrder || 1;
        document.getElementById('edit-cand-nim').value = nim;
        document.getElementById('edit-cand-name').value = name;
        document.getElementById('edit-cand-place').value = place;
        document.getElementById('edit-cand-date').value = date;
        document.getElementById('edit-cand-father').value = father;
        document.getElementById('edit-cand-mother').value = mother;
        document.getElementById('edit-cand-path').value = path;
        document.getElementById('edit-cand-religion').value = religion;
        document.getElementById('edit-cand-oath-coordinator').checked = Boolean(isOathCoordinator);
        document.getElementById('edit-cand-speech-rep').checked = Boolean(isSpeechRep);

        openModal('modal-edit-candidate');
    }

    function openPptModal(id, name) {
        document.getElementById('modal-upload-ppt-title').innerText = 'Upload Slide PPT: ' + name;
        document.getElementById('form-upload-ppt').action = "{{ url('pendaftaran-token/peserta') }}/" + id + "/upload-ppt";
        openModal('modal-upload-ppt');
    }

    function autoNumberCandidates() {
        const inputs = document.querySelectorAll('.reorder-input');
        inputs.forEach((input, index) => {
            input.value = index + 1;
        });
    }

    @if($errors->any() && old('period_id') == $period->id && !old('_method'))
        document.addEventListener('DOMContentLoaded', function () {
            openModal('modal-create-candidate');
        });
    @endif
</script>
@endpush
@endsection
