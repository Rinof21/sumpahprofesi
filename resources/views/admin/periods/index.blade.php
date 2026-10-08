@extends('layouts.app')

@section('title', 'Manajemen Periode Sumpah')

@section('content')
<!-- Page Header with Action Button -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Manajemen Periode Sumpah, Token Akses & Lock Data</h1>
        <p class="text-xs text-gray-500">Kelola periode aktif, buat Kode Token Akses untuk mahasiswa, cantumkan Link Google Drive, dan kunci/buka edit data peserta.</p>
    </div>
    <div class="flex-shrink-0">
        <button type="button" onclick="openModal('modal-create-period')" class="px-4 py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded-xl shadow-sm flex items-center gap-2 transition-all hover:shadow-md">
            <i class="fa-solid fa-plus-circle text-sm"></i> Tambah Periode Baru
        </button>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r text-emerald-800 text-xs font-medium flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r text-red-800 text-xs font-medium flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-red-500 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
    </div>
@endif

<!-- Periods Table List Full-Width Container -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm w-full overflow-hidden">
    <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4 bg-gradient-to-r from-gray-50/50 to-white">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#4e73df] flex items-center justify-center font-bold text-base shadow-xs">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h2 class="text-base font-black text-gray-900 tracking-tight">Daftar Periode Sumpah & Akses Token</h2>
                <p class="text-xs text-gray-500 font-medium">Tabel lengkap periode sumpah, kuota pendaftaran, dan kontrol akses</p>
            </div>
        </div>

        <!-- Tab Switcher and Add Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="tab-btn-active" onclick="switchPeriodTab('active')" class="px-4 py-2 rounded-lg font-bold text-xs bg-[#4e73df] text-white shadow-xs flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-folder-open"></i> Periode ({{ count($periods) }})
            </button>
            <button type="button" id="tab-btn-trash" onclick="switchPeriodTab('trash')" class="px-4 py-2 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-trash-can"></i> Trash / Sampah
                @if(count($trashedPeriods) > 0)
                    <span class="px-1.5 py-0.2 bg-red-500 text-white rounded-full text-[10px] font-extrabold">{{ count($trashedPeriods) }}</span>
                @endif
            </button>
            <button type="button" onclick="openModal('modal-create-period')" class="px-4 py-2 rounded-lg font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-plus"></i> Tambah Periode
            </button>
        </div>
    </div>

    <!-- TAB ACTIVE PERIODS -->
    <div id="view-active-periods" class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-gray-50/80 text-gray-600 uppercase font-extrabold border-b border-gray-200 tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No.</th>
                        <th class="px-5 py-3.5">Nama & Token Akses</th>
                        <th class="px-5 py-3.5">Tanggal Pelaksanaan</th>
                        <th class="px-5 py-3.5 text-center">Statistik Peserta</th>
                        <th class="px-5 py-3.5">Status Periode</th>
                        <th class="px-5 py-3.5">Kunci Data Peserta</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 bg-white">
                    @forelse($periods as $i => $p)
                        <tr class="hover:bg-blue-50/20 transition-colors">
                            <td class="px-4 py-4 text-center text-xs font-black text-gray-400">
                                {{ $i + 1 }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-black text-gray-900 text-sm tracking-tight">{{ $p->name }}</div>
                                <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                    <span onclick="copyToken('{{ $p->access_token }}')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-[#4e73df] border border-blue-200 rounded-md font-mono font-black text-[11px] tracking-wider cursor-pointer inline-flex items-center gap-1.5 transition-colors" title="Klik untuk menyalin token ke clipboard">
                                        <i class="fa-solid fa-key text-[10px]"></i>
                                        <span>{{ $p->access_token ?: 'TANPA TOKEN' }}</span>
                                        <i class="fa-regular fa-copy text-[10px] text-blue-400"></i>
                                    </span>
                                    @if($p->drive_url)
                                        <a href="{{ $p->drive_url }}" target="_blank" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md font-bold text-[11px] inline-flex items-center gap-1.5 transition-colors" title="Buka Link Google Drive Berkas/PPT">
                                            <i class="fa-brands fa-google-drive text-emerald-600"></i>
                                            <span>Folder Drive</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-emerald-500"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800 flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-[#4e73df]"></i>
                                    <span>{{ $p->event_date->format('d M Y') }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-200 rounded text-[10px] font-bold">
                                        {{ $p->quarter_code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-[#4e73df] border border-blue-200 rounded-full font-bold text-xs">
                                    <i class="fa-solid fa-users text-[11px]"></i>
                                    <span>{{ $p->candidates_count }} Peserta</span>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.periods.update-status', $p->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 hover:bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-[#4e73df] focus:border-[#4e73df] transition-all cursor-pointer">
                                        <option value="draft" {{ $p->status === 'draft' ? 'selected' : '' }}>Draft (Persiapan)</option>
                                        <option value="active" {{ $p->status === 'active' ? 'selected' : '' }}>Active (Buka Pendaftaran)</option>
                                        <option value="archived" {{ $p->status === 'archived' ? 'selected' : '' }}>Archived (Tutup/Selesai)</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.periods.toggle-lock', $p->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($p->is_locked)
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-extrabold rounded-lg text-xs flex items-center gap-1.5 transition-all shadow-2xs" title="Klik untuk membuka kunci edit data peserta">
                                            <i class="fa-solid fa-lock text-red-600"></i> DIKUNCI (Locked)
                                        </button>
                                    @else
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-extrabold rounded-lg text-xs flex items-center gap-1.5 transition-all shadow-2xs" title="Klik untuk mengunci pendaftaran & edit data peserta">
                                            <i class="fa-solid fa-lock-open text-emerald-600"></i> DIBUKA (Editable)
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openEditPeriodModal('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ $p->event_date->format('Y-m-d') }}', '{{ $p->quarter_code }}', '{{ $p->access_token }}', '{{ addslashes($p->drive_url) }}', '{{ $p->status }}')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1" title="Edit Periode Sumpah">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </button>
                                    <form action="{{ route('admin.periods.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?\n\nPeriode akan dipindahkan ke Trash (Sampah) dan dapat dipulihkan kapan saja.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1" title="Hapus Periode (Soft Delete)">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                </div>
                                <div class="font-bold text-gray-600 text-sm">Belum ada periode sumpah.</div>
                                <p class="text-xs text-gray-400 mt-1">Silakan klik tombol "Tambah Periode Baru" di atas untuk membuat periode sumpah.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB TRASHED PERIODS -->
    <div id="view-trashed-periods" class="hidden p-0">
        <div class="p-4 bg-red-50/70 border-b border-red-200 text-xs text-red-800 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
                <span>Periode di bawah ini berada di dalam <strong>Trash (Sampah)</strong>. Anda dapat memulihkannya atau menghapusnya secara permanen.</span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-gray-50/80 text-gray-600 uppercase font-extrabold border-b border-gray-200 tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">No.</th>
                        <th class="px-5 py-3.5">Nama & Token Akses</th>
                        <th class="px-5 py-3.5">Tanggal Pelaksanaan</th>
                        <th class="px-5 py-3.5">Tanggal Dihapus</th>
                        <th class="px-5 py-3.5 text-center">Peserta</th>
                        <th class="px-5 py-3.5 text-right">Aksi Pemulihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 bg-white">
                    @forelse($trashedPeriods as $i => $tp)
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-4 py-4 text-center text-xs font-black text-gray-400">
                                {{ $i + 1 }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="line-through text-gray-500 font-bold text-sm">{{ $tp->name }}</div>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 border border-gray-200 rounded font-mono text-[10px]">
                                        <i class="fa-solid fa-key mr-1"></i>{{ $tp->access_token ?: 'TANPA TOKEN' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-600">{{ $tp->event_date->format('d M Y') }}</div>
                                <div class="font-mono text-gray-400 text-[10px] mt-0.5">{{ $tp->quarter_code }}</div>
                            </td>
                            <td class="px-5 py-4 text-red-600 font-semibold">
                                <i class="fa-regular fa-clock mr-1"></i>{{ $tp->deleted_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 border border-gray-200 rounded-full font-bold text-xs">
                                    <i class="fa-solid fa-users text-[11px]"></i>
                                    <span>{{ $tp->candidates_count }} Peserta</span>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.periods.restore', $tp->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold rounded-lg text-xs transition-colors flex items-center gap-1" title="Pulihkan Periode ke List Aktif">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>Pulihkan</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.periods.force-delete', $tp->id) }}" method="POST" onsubmit="return confirm('PERINGATAN MANAJEMEN:\n\nApakah Anda yakin ingin menghapus PERMANEN periode ini?\nData yang dihapus permanen tidak dapat dikembalikan lagi!');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1" title="Hapus Permanen">
                                            <i class="fa-solid fa-skull"></i>
                                            <span>Hapus Permanen</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 font-medium">Trash / Sampah kosong. Tidak ada periode yang terhapus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Periode Baru -->
<div id="modal-create-period" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-2xl w-full overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-[#4e73df] to-[#224abe] px-6 py-4 text-white flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm font-bold">
                    <i class="fa-solid fa-calendar-plus"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm uppercase tracking-wider">
                        Tambah Periode Sumpah Baru
                    </h3>
                    <p class="text-[11px] text-blue-100 font-normal">Buat periode sumpah baru dan konfigurasi token akses pendaftaran</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-create-period')" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-lg transition">&times;</button>
        </div>

        <form action="{{ route('admin.periods.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Sumpah Dokter Periode II 2026" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Siklus Triwulan <span class="text-red-500">*</span></label>
                    <select name="quarter_code" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 font-bold focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition">
                        <option value="Q1">Q1 (Triwulan 1 - Jan-Mar)</option>
                        <option value="Q2">Q2 (Triwulan 2 - Apr-Jun)</option>
                        <option value="Q3">Q3 (Triwulan 3 - Jul-Sep)</option>
                        <option value="Q4">Q4 (Triwulan 4 - Okt-Des)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Kode Token Akses Mahasiswa
                    </label>
                    <input type="text" name="access_token" placeholder="Kosongkan untuk auto-generate (Misal: TK-DOKTER26)" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-mono font-bold text-[#4e73df] uppercase focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition">
                    <p class="text-[10px] text-gray-400 mt-1">Kode rahasia ini wajib dimasukkan mahasiswa saat registrasi.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Awal Periode <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition">
                        <option value="draft">Draft (Persiapan Internal)</option>
                        <option value="active" selected>Active (Buka Pendaftaran Mahasiswa)</option>
                        <option value="archived">Archived (Selesai/Terarsip)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Mengaktifkan periode ini akan mengarsipkan periode aktif sebelumnya.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Link Google Drive (Upload PPT / Berkas)</label>
                <input type="url" name="drive_url" placeholder="https://drive.google.com/drive/folders/..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition">
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modal-create-period')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-key"></i> Simpan Periode Baru & Generate Token
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Periode -->
<div id="modal-edit-period" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-2xl w-full overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4 text-white flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white text-sm font-bold">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm uppercase tracking-wider">
                        Edit Periode Sumpah & Token Akses
                    </h3>
                    <p class="text-[11px] text-amber-100 font-normal">Perbarui data pelaksanaan, siklus triwulan, dan token akses</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-edit-period')" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-lg transition">&times;</button>
        </div>

        <form id="form-edit-period" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" id="edit-period-name" name="name" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                    <input type="date" id="edit-period-date" name="event_date" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Siklus Triwulan <span class="text-red-500">*</span></label>
                    <select id="edit-period-quarter" name="quarter_code" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 font-bold focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                        <option value="Q1">Q1 (Triwulan 1 - Jan-Mar)</option>
                        <option value="Q2">Q2 (Triwulan 2 - Apr-Jun)</option>
                        <option value="Q3">Q3 (Triwulan 3 - Jul-Sep)</option>
                        <option value="Q4">Q4 (Triwulan 4 - Okt-Des)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Token Akses Mahasiswa</label>
                    <input type="text" id="edit-period-token" name="access_token" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-mono font-bold text-[#4e73df] uppercase focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika ingin generate otomatis atau gunakan token yang ada.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Periode <span class="text-red-500">*</span></label>
                    <select id="edit-period-status" name="status" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                        <option value="draft">Draft (Persiapan)</option>
                        <option value="active">Active (Buka Pendaftaran Mahasiswa)</option>
                        <option value="archived">Archived (Selesai/Terarsip)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Mengaktifkan periode ini akan mengarsipkan periode aktif sebelumnya.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Link Google Drive (Upload PPT / Berkas)</label>
                <input type="url" id="edit-period-drive" name="drive_url" placeholder="https://drive.google.com/drive/folders/..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modal-edit-period')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Simpan Perubahan Periode
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Close modals on clicking backdrop
    window.addEventListener('click', function(e) {
        if (e.target.id === 'modal-create-period') {
            closeModal('modal-create-period');
        }
        if (e.target.id === 'modal-edit-period') {
            closeModal('modal-edit-period');
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('modal-create-period');
            closeModal('modal-edit-period');
        }
    });

    function openEditPeriodModal(id, name, date, quarter, token, drive, status) {
        const form = document.getElementById('form-edit-period');
        if (form) {
            form.action = "{{ url('admin/periods') }}/" + id;
        }
        const nameEl = document.getElementById('edit-period-name');
        if (nameEl) nameEl.value = name;
        
        const dateEl = document.getElementById('edit-period-date');
        if (dateEl) dateEl.value = date;
        
        const quarterEl = document.getElementById('edit-period-quarter');
        if (quarterEl) quarterEl.value = quarter;
        
        const tokenEl = document.getElementById('edit-period-token');
        if (tokenEl) tokenEl.value = token;
        
        const driveEl = document.getElementById('edit-period-drive');
        if (driveEl) driveEl.value = drive;
        
        const statusEl = document.getElementById('edit-period-status');
        if (statusEl) statusEl.value = status;

        openModal('modal-edit-period');
    }

    function switchPeriodTab(tabName) {
        const activeView = document.getElementById('view-active-periods');
        const trashView = document.getElementById('view-trashed-periods');
        const activeBtn = document.getElementById('tab-btn-active');
        const trashBtn = document.getElementById('tab-btn-trash');

        if (tabName === 'active') {
            activeView.classList.remove('hidden');
            trashView.classList.add('hidden');
            activeBtn.className = "px-3.5 py-2 rounded-lg font-bold text-xs bg-[#4e73df] text-white shadow-sm flex items-center gap-1.5 transition-all";
            trashBtn.className = "px-3.5 py-2 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 flex items-center gap-1.5 transition-all";
        } else {
            activeView.classList.add('hidden');
            trashView.classList.remove('hidden');
            activeBtn.className = "px-3.5 py-2 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 flex items-center gap-1.5 transition-all";
            trashBtn.className = "px-3.5 py-2 rounded-lg font-bold text-xs bg-red-600 text-white shadow-sm flex items-center gap-1.5 transition-all";
        }
    }

    function copyToken(token) {
        if (!token) return;
        navigator.clipboard.writeText(token).then(() => {
            alert('Kode Token Akses disalin ke clipboard:\n' + token);
        }).catch(() => {
            prompt('Salin token secara manual:', token);
        });
    }

    @if(isset($errors) && $errors->any())
        // Auto reopen create modal if there are validation errors
        openModal('modal-create-period');
    @endif
</script>
@endpush
@endsection
