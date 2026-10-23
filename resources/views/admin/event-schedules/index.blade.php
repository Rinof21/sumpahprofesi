@extends('layouts.app')

@section('title', 'Manajemen Susunan Acara & Tata Tertib')

@section('content')
<!-- Page Heading -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Susunan Acara & Tata Tertib Sumpah Profesi</h1>
        <p class="text-xs font-semibold text-gray-500">Kelola rundown acara prosesi sumpah dan ketentuan tata tertib resmi yang akan ditampilkan pada halaman peserta.</p>
    </div>

    @if($selectedPeriod)
        <div class="flex flex-wrap items-center gap-2">
            <!-- Muat Template Periode Ini -->
            <form action="{{ route('admin.event-schedules.load-template') }}" method="POST">
                @csrf
                <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">
                <input type="hidden" name="apply_all" value="0">
                <button type="submit" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-extrabold text-xs rounded-lg shadow-2xs flex items-center gap-1.5 transition-colors" onclick="return confirm('Muat ulang template standar untuk periode ini? Susunan acara dan tata tertib pada periode ini akan disesuaikan dengan standar resmi program studi.')">
                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i> Muat Template (Periode Ini)
                </button>
            </form>

            <!-- Terapkan Template ke SEMUA Periode -->
            <form action="{{ route('admin.event-schedules.load-template') }}" method="POST">
                @csrf
                <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">
                <input type="hidden" name="apply_all" value="1">
                <button type="submit" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center gap-1.5 transition-colors" onclick="return confirm('PERHATIAN: Apakah Anda yakin ingin menerapkan/menyinkronkan template susunan acara dan tata tertib standar prodi ini ke SEMUA periode dalam program studi Anda?')">
                    <i class="fa-solid fa-layer-group"></i> Terapkan ke Semua Periode
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Period Selector Banner -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.event-schedules.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <label class="text-xs font-extrabold text-gray-700 uppercase tracking-wider shrink-0 flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-check text-[#4e73df]"></i> Pilih Periode Sumpah:
            </label>
            <select name="period_id" onchange="this.form.submit()" class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 font-bold focus:outline-none focus:ring-2 focus:ring-[#4e73df]">
                @forelse($periods as $per)
                    <option value="{{ $per->id }}" {{ $selectedPeriodId == $per->id ? 'selected' : '' }}>
                        {{ $per->name }} &bull; {{ $per->event_date->format('d M Y') }} [{{ $per->schedules_count ?? $per->schedules()->count() }} Agenda, {{ $per->rules_count ?? $per->rules()->count() }} Tertib] ({{ strtoupper($per->status) }})
                    </option>
                @empty
                    <option value="">Belum ada periode sumpah</option>
                @endforelse
            </select>
        </div>

        @if($selectedPeriod)
            <div class="flex items-center gap-3 text-xs">
                <span class="px-3 py-1 bg-blue-50 text-[#4e73df] border border-blue-200 rounded-full font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> {{ $schedules->count() }} Agenda Acara
                </span>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-alt"></i> {{ $rules->count() }} Butir Tata Tertib
                </span>
            </div>
        @endif
    </form>
</div>

@if(!$selectedPeriod)
    <div class="p-8 bg-white rounded-xl border border-gray-200 text-center text-gray-500">
        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-2"></i>
        <p class="font-bold text-sm">Tidak ada periode sumpah yang ditemukan.</p>
        <p class="text-xs">Silakan buat periode sumpah terlebih dahulu di menu Periode Sumpah.</p>
    </div>
@else

<!-- Tab Navigation -->
<div class="mb-6 flex border-b border-gray-200">
    <button type="button" onclick="switchTab('tab-schedules')" id="btn-tab-schedules" class="tab-button py-3 px-5 text-xs font-black border-b-2 border-[#4e73df] text-[#4e73df] flex items-center gap-2 transition-all">
        <i class="fa-solid fa-clipboard-list text-sm"></i> Susunan Acara (Rundown)
        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-[#4e73df] text-[10px]">{{ $schedules->count() }}</span>
    </button>
    <button type="button" onclick="switchTab('tab-rules')" id="btn-tab-rules" class="tab-button py-3 px-5 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2 transition-all">
        <i class="fa-solid fa-scale-balanced text-sm"></i> Tata Tertib Acara
        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[10px]">{{ $rules->count() }}</span>
    </button>
</div>

<!-- ==================== TAB 1: SUSUNAN ACARA ==================== -->
<div id="tab-schedules" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Tambah Agenda Acara -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                <h2 class="text-sm font-black text-gray-900 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-[#4e73df]"></i> Tambah Agenda Acara
                </h2>
                <p class="text-xs text-gray-500 mb-4">Tambahkan poin kegiatan ke dalam susunan rundown acara.</p>

                <form action="{{ route('admin.event-schedules.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="text" name="start_time" required placeholder="08:00" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Jam Selesai</label>
                            <input type="text" name="end_time" placeholder="08:30" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Nama Kegiatan / Agenda <span class="text-red-500">*</span></label>
                        <input type="text" name="activity_name" required placeholder="Contoh: Prosesi Lafal Sumpah Bersama Rohaniwan" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Penanggung Jawab / Pengisi Acara</label>
                        <input type="text" name="person_in_charge" placeholder="Contoh: Dekan FK UNTAN / Rohaniwan" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Catatan / Detail Lokasi</label>
                        <textarea name="notes" rows="2" placeholder="Keterangan tambahan untuk peserta atau protokol..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]"></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" value="{{ ($schedules->max('sort_order') ?? 0) + 1 }}" min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center justify-center gap-2 mt-2 transition-all">
                        <i class="fa-solid fa-plus-circle"></i> Simpan Agenda Acara
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Susunan Acara (Rundown Table & Timeline) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Rundown Acara Resmi: {{ $selectedPeriod->name }}</h2>
                        <p class="text-xs text-gray-500">Tanggal: {{ $selectedPeriod->event_date->format('d F Y') }}</p>
                    </div>
                    @if($schedules->isNotEmpty())
                        <form action="{{ route('admin.event-schedules.load-template') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">
                            <button type="submit" onclick="return confirm('Tambahkan 10 agenda template standar sumpah?')" class="px-3 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold rounded-lg transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-plus"></i> Tambah Template
                            </button>
                        </form>
                    @endif
                </div>

                @if($schedules->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-[#4e73df] flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <h3 class="font-bold text-sm text-gray-800 mb-1">Belum Ada Susunan Acara</h3>
                        <p class="text-xs text-gray-500 max-w-md mx-auto mb-4">Susunan acara untuk periode ini belum diisi. Anda dapat menambahkan agenda satu per satu atau memuat template standar otomatis.</p>
                        
                        <form action="{{ route('admin.event-schedules.load-template') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">
                            <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs rounded-lg shadow-sm inline-flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Muat Template Susunan Acara Standar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-100/70 text-gray-600 font-bold uppercase text-[10px] border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-center w-12">No</th>
                                    <th class="px-4 py-3 w-32">Waktu (WIB)</th>
                                    <th class="px-4 py-3">Nama Kegiatan</th>
                                    <th class="px-4 py-3">Pengisi / Penanggung Jawab</th>
                                    <th class="px-4 py-3">Catatan</th>
                                    <th class="px-4 py-3 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @foreach($schedules as $index => $item)
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="px-4 py-3 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-[#4e73df] font-mono font-black text-[11px] border border-blue-200">
                                                <i class="fa-regular fa-clock mr-1 text-[10px]"></i>
                                                {{ $item->start_time }} {{ $item->end_time ? '- ' . $item->end_time : '' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-gray-900">{{ $item->activity_name }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $item->person_in_charge ?: '-' }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-[11px]">{{ $item->notes ?: '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" 
                                                    onclick="editSchedule({{ $item->id }}, '{{ addslashes($item->start_time) }}', '{{ addslashes($item->end_time ?? '') }}', '{{ addslashes($item->activity_name) }}', '{{ addslashes($item->person_in_charge ?? '') }}', '{{ addslashes($item->notes ?? '') }}', {{ $item->sort_order }})"
                                                    class="p-1.5 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded transition-colors" title="Edit Agenda">
                                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                                </button>
                                                <form action="{{ route('admin.event-schedules.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus agenda {{ $item->activity_name }}?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors" title="Hapus Agenda">
                                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- ==================== TAB 2: TATA TERTIB ==================== -->
<div id="tab-rules" class="tab-content hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Tambah Butir Tata Tertib -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                <h2 class="text-sm font-black text-gray-900 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-shield-plus text-emerald-600"></i> Tambah Butir Tata Tertib
                </h2>
                <p class="text-xs text-gray-500 mb-4">Tambahkan ketentuan atau tata tertib pelaksanaan prosesi.</p>

                <form action="{{ route('admin.event-rules.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Judul Aturan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Larangan Membawa Anak Kecil & Balita" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-emerald-500">
                                <option value="Kehadiran">Kehadiran</option>
                                <option value="Pakaian">Pakaian / Dresscode</option>
                                <option value="Tamu & Keluarga">Tamu & Keluarga</option>
                                <option value="Prosesi Aula">Prosesi Aula</option>
                                <option value="Dokumentasi">Dokumentasi</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Urutan (Sort Order)</label>
                            <input type="number" name="sort_order" value="{{ ($rules->max('sort_order') ?? 0) + 1 }}" min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Uraian / Deskripsi Aturan <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required placeholder="Jelaskan secara lengkap ketentuan aturan yang wajib ditaati..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2.5">
                        <input type="checkbox" id="is_strict" name="is_strict" value="1" class="mt-0.5 rounded border-red-300 text-red-600 focus:ring-red-500 cursor-pointer">
                        <label for="is_strict" class="text-xs text-red-900 font-bold cursor-pointer">
                            Tandai sebagai Aturan Ketat / Larangan Mutlak
                            <span class="block text-[10px] text-red-700 font-normal">Akan ditampilkan dengan penanda khusus (warna merah tegas) di halaman peserta.</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center justify-center gap-2 mt-2 transition-all">
                        <i class="fa-solid fa-plus-circle"></i> Simpan Butir Tata Tertib
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Butir Tata Tertib -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Tata Tertib Resmi: {{ $selectedPeriod->name }}</h2>
                        <p class="text-xs text-gray-500">Pedoman perilaku dan etika kehadiran prosesi</p>
                    </div>
                </div>

                @if($rules->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <h3 class="font-bold text-sm text-gray-800 mb-1">Belum Ada Butir Tata Tertib</h3>
                        <p class="text-xs text-gray-500 max-w-md mx-auto mb-4">Tata tertib untuk periode ini belum diisi. Anda dapat menambahkan aturan baru atau memuat template standar.</p>
                        
                        <form action="{{ route('admin.event-schedules.load-template') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm inline-flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Muat Template Tata Tertib Standar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-5 space-y-3">
                        @foreach($rules as $index => $rule)
                            <div class="p-4 rounded-xl border {{ $rule->is_strict ? 'bg-red-50/50 border-red-300' : 'bg-gray-50/70 border-gray-200' }} transition-all hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shrink-0 {{ $rule->is_strict ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                                <h3 class="font-black text-xs {{ $rule->is_strict ? 'text-red-900' : 'text-gray-900' }}">
                                                    {{ $rule->title }}
                                                </h3>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $rule->is_strict ? 'bg-red-200 text-red-800' : 'bg-blue-100 text-[#4e73df]' }}">
                                                    {{ $rule->category }}
                                                </span>
                                                @if($rule->is_strict)
                                                    <span class="px-2 py-0.5 rounded bg-red-600 text-white text-[9px] font-black uppercase tracking-wider">
                                                        <i class="fa-solid fa-triangle-exclamation mr-0.5"></i> Wajib Mutlak
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-700 leading-relaxed font-normal">
                                                {{ $rule->description }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button" 
                                            onclick="editRule({{ $rule->id }}, '{{ addslashes($rule->title) }}', '{{ addslashes($rule->category) }}', '{{ addslashes($rule->description) }}', {{ $rule->is_strict ? 'true' : 'false' }}, {{ $rule->sort_order }})"
                                            class="p-1.5 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-100/50 rounded transition-colors" title="Edit Tata Tertib">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </button>
                                        <form action="{{ route('admin.event-rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Hapus tata tertib {{ $rule->title }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-100/50 rounded transition-colors" title="Hapus Tata Tertib">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endif

<!-- ==================== MODAL EDIT SUSUNAN ACARA ==================== -->
<div id="modal-edit-schedule" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden">
        <div class="bg-[#4e73df] px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Agenda Susunan Acara
            </h3>
            <button type="button" onclick="closeModal('modal-edit-schedule')" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <form id="form-edit-schedule" method="POST" class="p-5 space-y-3">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_start_time" name="start_time" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Jam Selesai</label>
                    <input type="text" id="edit_end_time" name="end_time" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Nama Kegiatan / Agenda <span class="text-red-500">*</span></label>
                <input type="text" id="edit_activity_name" name="activity_name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df]">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Penanggung Jawab / Pengisi</label>
                <input type="text" id="edit_person_in_charge" name="person_in_charge" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Catatan / Detail Lokasi</label>
                <textarea id="edit_notes" name="notes" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Urutan Tampil (Sort Order)</label>
                <input type="number" id="edit_sort_order" name="sort_order" min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-edit-schedule')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-save"></i> Perbarui Agenda
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT TATA TERTIB ==================== -->
<div id="modal-edit-rule" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden">
        <div class="bg-emerald-600 px-5 py-4 text-white flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Butir Tata Tertib
            </h3>
            <button type="button" onclick="closeModal('modal-edit-rule')" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <form id="form-edit-rule" method="POST" class="p-5 space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Judul Aturan <span class="text-red-500">*</span></label>
                <input type="text" id="edit_rule_title" name="title" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select id="edit_rule_category" name="category" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-emerald-500">
                        <option value="Kehadiran">Kehadiran</option>
                        <option value="Pakaian">Pakaian / Dresscode</option>
                        <option value="Tamu & Keluarga">Tamu & Keluarga</option>
                        <option value="Prosesi Aula">Prosesi Aula</option>
                        <option value="Dokumentasi">Dokumentasi</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Urutan Tampil (Sort Order)</label>
                    <input type="number" id="edit_rule_sort_order" name="sort_order" min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Uraian / Deskripsi Aturan <span class="text-red-500">*</span></label>
                <textarea id="edit_rule_description" name="description" rows="4" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2.5">
                <input type="checkbox" id="edit_rule_is_strict" name="is_strict" value="1" class="mt-0.5 rounded border-red-300 text-red-600 focus:ring-red-500 cursor-pointer">
                <label for="edit_rule_is_strict" class="text-xs text-red-900 font-bold cursor-pointer">
                    Tandai sebagai Aturan Ketat / Larangan Mutlak
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-edit-rule')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-save"></i> Perbarui Tata Tertib
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(el => {
            el.classList.remove('border-[#4e73df]', 'text-[#4e73df]');
            el.classList.add('border-transparent', 'text-gray-500');
        });

        document.getElementById(tabId).classList.remove('hidden');

        if (tabId === 'tab-schedules') {
            document.getElementById('btn-tab-schedules').classList.add('border-[#4e73df]', 'text-[#4e73df]');
            document.getElementById('btn-tab-schedules').classList.remove('border-transparent', 'text-gray-500');
        } else {
            document.getElementById('btn-tab-rules').classList.add('border-[#4e73df]', 'text-[#4e73df]');
            document.getElementById('btn-tab-rules').classList.remove('border-transparent', 'text-gray-500');
        }
    }

    function editSchedule(id, startTime, endTime, activityName, personInCharge, notes, sortOrder) {
        document.getElementById('form-edit-schedule').action = '{{ url("admin/event-schedules") }}/' + id;
        document.getElementById('edit_start_time').value = startTime;
        document.getElementById('edit_end_time').value = endTime;
        document.getElementById('edit_activity_name').value = activityName;
        document.getElementById('edit_person_in_charge').value = personInCharge;
        document.getElementById('edit_notes').value = notes;
        document.getElementById('edit_sort_order').value = sortOrder;
        openModal('modal-edit-schedule');
    }

    function editRule(id, title, category, description, isStrict, sortOrder) {
        document.getElementById('form-edit-rule').action = '{{ url("admin/event-rules") }}/' + id;
        document.getElementById('edit_rule_title').value = title;
        document.getElementById('edit_rule_category').value = category;
        document.getElementById('edit_rule_description').value = description;
        document.getElementById('edit_rule_is_strict').checked = isStrict;
        document.getElementById('edit_rule_sort_order').value = sortOrder;
        openModal('modal-edit-rule');
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
