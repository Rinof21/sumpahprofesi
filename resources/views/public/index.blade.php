@extends('layouts.app')

@section('title', 'Portal Sumpah Profesi Kesehatan Multi-Prodi')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Portal Sumpah Profesi Kesehatan Multi-Prodi</h1>
        <p class="text-xs font-semibold text-gray-500">Fakultas Kedokteran dan Ilmu Kesehatan (FKIK) &bull; Dokter, Apoteker, Ners</p>
    </div>
</div>

<!-- Active Periods Section -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Pelaksanaan Sumpah Terbuka (Active Period)
        </h2>
    </div>

    @if($activePeriods->isEmpty())
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm text-center text-xs text-gray-500">
            Saat ini belum ada periode sumpah yang berstatus terbuka.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($activePeriods as $ap)
                <div onclick="openTokenModal('{{ $ap->id }}', '{{ addslashes($ap->name) }}')" class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 hover:shadow-md hover:border-[#4e73df] transition-all cursor-pointer flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2.5 py-0.5 rounded bg-blue-100 text-[#4e73df] text-[10px] font-extrabold uppercase">
                                {{ $ap->studyProgram->code }} - {{ $ap->studyProgram->organization }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase">
                                {{ $ap->quarter_code }}
                            </span>
                        </div>

                        <h3 class="text-sm font-bold text-gray-900 mb-2 group-hover:text-[#4e73df] transition-colors flex items-center justify-between">
                            <span>{{ $ap->name }}</span>
                            <i class="fa-solid fa-key text-xs text-[#4e73df] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </h3>

                        <div class="space-y-1 text-xs text-gray-600 mb-4">
                            <div><i class="fa-solid fa-calendar-day text-[#4e73df] w-4"></i> {{ $ap->event_date->format('d F Y') }}</div>
                            <div><i class="fa-solid fa-users text-[#4e73df] w-4"></i> {{ $ap->candidates_count ?? $ap->candidates()->count() }} Calon Lulusan</div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs" onclick="event.stopPropagation();">
                        <a href="{{ route('public.archive-detail', $ap->slug) }}" class="font-bold text-gray-500 hover:text-[#4e73df] hover:underline flex items-center gap-1">
                            Buka Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                        <button type="button" onclick="openTokenModal('{{ $ap->id }}', '{{ addslashes($ap->name) }}')" class="px-3 py-1 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold rounded text-xs shadow-sm flex items-center gap-1">
                            <i class="fa-solid fa-key text-[10px]"></i> Masuk Pendaftaran
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- SB Admin 2 Card: Public Archive Directory -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
        <h6 class="text-xs font-black text-[#4e73df] uppercase tracking-wider m-0 flex items-center gap-2">
            <i class="fa-solid fa-box-archive"></i> Repositori Arsip Digital Multi-Prodi
        </h6>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3">Program Studi</th>
                    <th class="px-6 py-3">Nama Periode</th>
                    <th class="px-6 py-3">Tanggal Acara</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Portal Arsip</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @php
                    $allPeriods = \App\Models\OathPeriod::with(['studyProgram', 'candidates'])->orderBy('event_date', 'desc')->get();
                @endphp
                @forelse($allPeriods as $period)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-bold text-gray-900">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-blue-100 text-[#4e73df] flex items-center justify-center text-[10px] font-black">
                                    {{ $period->studyProgram->code }}
                                </span>
                                <div>
                                    <div>{{ $period->studyProgram->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-normal">{{ $period->studyProgram->organization }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-semibold text-gray-800">{{ $period->name }}</td>
                        <td class="px-6 py-3 font-semibold">{{ $period->event_date->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border
                                @if($period->status === 'active') bg-emerald-100 text-emerald-700 border-emerald-200
                                @elseif($period->status === 'archived') bg-gray-100 text-gray-600 border-gray-200
                                @else bg-amber-100 text-amber-700 border-amber-200 @endif">
                                {{ $period->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('public.archive-detail', $period->slug) }}" class="px-3 py-1 bg-gray-100 hover:bg-[#4e73df] hover:text-white text-gray-700 font-bold border border-gray-300 rounded text-xs transition-colors inline-flex items-center gap-1">
                                <i class="fa-solid fa-folder-open"></i> Buka Arsip
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-400">Belum ada arsip periode.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Masukkan Token Akses Sumpah -->
<div id="modal-enter-token" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full overflow-hidden transition-all">
        <div class="bg-gradient-to-r from-[#4e73df] to-[#224abe] px-6 py-5 text-white flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-base font-bold shrink-0 shadow-xs">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm uppercase tracking-wider">Akses Pendaftaran Sumpah</h3>
                    <p id="token-period-name-display" class="text-xs text-blue-100 font-bold mt-0.5 tracking-tight"></p>
                </div>
            </div>
            <button type="button" onclick="closeTokenModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition text-lg leading-none">&times;</button>
        </div>

        <form action="{{ route('token-access.verify') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="token-period-id" name="period_id">

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2 flex items-center justify-between">
                    <span>Kode Token Akses <span class="text-red-500">*</span></span>
                    <span class="text-[10px] font-normal text-gray-400 capitalize">Wajib diisi</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input type="text" id="token-input-field" name="access_token" required placeholder="Contoh: TK-DOKTER26" class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-mono font-bold text-[#4e73df] tracking-wider uppercase focus:ring-2 focus:ring-[#4e73df] focus:bg-white focus:border-[#4e73df] transition">
                </div>
                <p class="text-[11px] text-gray-500 mt-2 flex items-start gap-1.5 leading-tight">
                    <i class="fa-solid fa-circle-info text-[#4e73df] mt-0.5 shrink-0 text-xs"></i>
                    <span>Kode token diperoleh dari Panitia Program Studi atau Pengumuman Resmi Sumpah.</span>
                </p>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeTokenModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk Pendaftaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openTokenModal(periodId, periodName) {
        document.getElementById('token-period-id').value = periodId;
        const displayEl = document.getElementById('token-period-name-display');
        if (displayEl) {
            displayEl.textContent = periodName;
        }
        const modal = document.getElementById('modal-enter-token');
        if (modal) {
            modal.classList.remove('hidden');
        }
        setTimeout(() => {
            const input = document.getElementById('token-input-field');
            if (input) {
                input.value = '';
                input.focus();
            }
        }, 100);
    }

    function closeTokenModal() {
        const modal = document.getElementById('modal-enter-token');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        if (e.target.id === 'modal-enter-token') {
            closeTokenModal();
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTokenModal();
        }
    });
</script>
@endpush
@endsection
