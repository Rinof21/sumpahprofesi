@extends('layouts.app')

@section('title', 'Manajemen Periode Sumpah')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Manajemen Periode Sumpah, Token Akses & Lock Data</h1>
    <p class="text-xs text-gray-500">Kelola periode aktif, buat Kode Token Akses untuk mahasiswa, cantumkan Link Google Drive, dan kunci/buka edit data peserta.</p>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Form Create New Period -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-[#4e73df]"></i> Tambah Periode Sumpah Baru
        </h2>

        <form action="{{ route('admin.periods.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Sumpah Dokter Periode II 2026" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 focus:ring-2 focus:ring-[#4e73df]">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Siklus Triwulan <span class="text-red-500">*</span></label>
                <select name="quarter_code" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-bold">
                    <option value="Q1">Q1 (Triwulan 1 - Jan-Mar)</option>
                    <option value="Q2">Q2 (Triwulan 2 - Apr-Jun)</option>
                    <option value="Q3">Q3 (Triwulan 3 - Jul-Sep)</option>
                    <option value="Q4">Q4 (Triwulan 4 - Okt-Des)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Kode Token Akses Mahasiswa
                </label>
                <input type="text" name="access_token" placeholder="Kosongkan untuk auto-generate (Misal: TK-DOKTER26)" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs font-mono font-bold text-[#4e73df] uppercase">
                <p class="text-[10px] text-gray-400 mt-0.5">Kode rahasia ini wajib dimasukkan mahasiswa saat memilih periode ini.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Link Google Drive (Upload PPT / Berkas)</label>
                <input type="url" name="drive_url" placeholder="https://drive.google.com/drive/folders/..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Awal Periode <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold">
                    <option value="draft">Draft (Persiapan Internal)</option>
                    <option value="active" selected>Active (Buka Pendaftaran Mahasiswa)</option>
                    <option value="archived">Archived (Selesai/Terarsip)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-key"></i> Simpan Periode Baru & Generate Token
            </button>
        </form>
    </div>

    <!-- Periods Table List & Trash Container -->
    <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#4e73df]"></i> Daftar Periode Sumpah & Akses Token
            </h2>

            <!-- Tab Switcher buttons -->
            <div class="flex items-center gap-2">
                <button type="button" id="tab-btn-active" onclick="switchPeriodTab('active')" class="px-3 py-1.5 rounded-lg font-bold text-xs bg-[#4e73df] text-white shadow-sm flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-folder-open"></i> Periode ({{ count($periods) }})
                </button>
                <button type="button" id="tab-btn-trash" onclick="switchPeriodTab('trash')" class="px-3 py-1.5 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-trash-can"></i> Trash / Sampah
                    @if(count($trashedPeriods) > 0)
                        <span class="px-1.5 py-0.2 bg-red-500 text-white rounded-full text-[10px] font-extrabold">{{ count($trashedPeriods) }}</span>
                    @endif
                </button>
            </div>
        </div>

        <!-- TAB ACTIVE PERIODS -->
        <div id="view-active-periods">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5 w-8 text-center">No.</th>
                            <th class="px-4 py-2.5">Nama & Token Akses</th>
                            <th class="px-4 py-2.5">Tanggal / Siklus</th>
                            <th class="px-4 py-2.5">Statistik Peserta</th>
                            <th class="px-4 py-2.5">Status Periode</th>
                            <th class="px-4 py-2.5">Kunci Data Peserta</th>
                            <th class="px-4 py-2.5 text-right">Aksi & Edit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($periods as $i => $p)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-3 text-center text-[11px] font-black text-gray-400">
                                    {{ $i + 1 }}
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-900">
                                    <div>{{ $p->name }}</div>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        <span class="px-2 py-0.5 bg-blue-50 text-[#4e73df] border border-blue-200 rounded font-mono font-black text-[10px] tracking-wide" title="Kode Token Akses Mahasiswa">
                                            <i class="fa-solid fa-key mr-1"></i>{{ $p->access_token ?: 'TANPA TOKEN' }}
                                        </span>
                                        @if($p->drive_url)
                                            <a href="{{ $p->drive_url }}" target="_blank" class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-bold text-[10px] hover:underline inline-flex items-center gap-1" title="Link Google Drive">
                                                <i class="fa-brands fa-google-drive"></i> Drive PPT
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ $p->event_date->format('d M Y') }}</div>
                                    <div class="font-mono text-[#4e73df] font-bold text-[10px] mt-0.5">{{ $p->quarter_code }}</div>
                                </td>
                                <td class="px-4 py-3 font-bold">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 border border-gray-200">
                                        <i class="fa-solid fa-users text-[10px] mr-1 text-gray-400"></i>{{ $p->candidates_count }} Peserta
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.periods.update-status', $p->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="px-2 py-1 bg-gray-50 border border-gray-300 rounded text-[11px] font-bold text-gray-800 focus:ring-1 focus:ring-[#4e73df]">
                                            <option value="draft" {{ $p->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="active" {{ $p->status === 'active' ? 'selected' : '' }}>Active (Buka)</option>
                                            <option value="archived" {{ $p->status === 'archived' ? 'selected' : '' }}>Archived (Tutup)</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.periods.toggle-lock', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if($p->is_locked)
                                            <button type="submit" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-800 border border-red-300 font-extrabold rounded text-[10px] flex items-center gap-1 transition-all" title="Klik untuk membuka kunci edit data peserta">
                                                <i class="fa-solid fa-lock text-red-600"></i> DIKUNCI (Locked)
                                            </button>
                                        @else
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border border-emerald-300 font-extrabold rounded text-[10px] flex items-center gap-1 transition-all" title="Klik untuk mengunci pendaftaran & edit data peserta">
                                                <i class="fa-solid fa-lock-open text-emerald-600"></i> DIBUKA (Editable)
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="openEditPeriodModal('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ $p->event_date->format('Y-m-d') }}', '{{ $p->quarter_code }}', '{{ $p->access_token }}', '{{ addslashes($p->drive_url) }}', '{{ $p->status }}')" class="w-8 h-8 flex items-center justify-center bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-bold rounded text-xs transition-colors" title="Edit Periode">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('admin.periods.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?\n\nPeriode akan dipindahkan ke Trash (Sampah) dan dapat dipulihkan kapan saja.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-700 border border-red-300 font-bold rounded text-xs transition-colors" title="Hapus Periode (Soft Delete)">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-400 font-medium">Belum ada periode sumpah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB TRASHED PERIODS -->
        <div id="view-trashed-periods" class="hidden">
            <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-xs text-red-800 flex items-center justify-between">
                <span><i class="fa-solid fa-trash-can text-red-600 mr-1.5"></i> Periode di bawah ini berada di dalam <strong>Trash (Sampah)</strong>. Anda dapat memulihkannya atau menghapusnya secara permanen.</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5 w-8 text-center">No.</th>
                            <th class="px-4 py-2.5">Nama & Token Akses</th>
                            <th class="px-4 py-2.5">Tanggal Pelaksanaan</th>
                            <th class="px-4 py-2.5">Tanggal Dihapus</th>
                            <th class="px-4 py-2.5">Peserta</th>
                            <th class="px-4 py-2.5 text-right">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($trashedPeriods as $i => $tp)
                            <tr class="hover:bg-red-50/40 transition-colors">
                                <td class="px-3 py-3 text-center text-[11px] font-black text-gray-400">
                                    {{ $i + 1 }}
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-900">
                                    <div class="line-through text-gray-500">{{ $tp->name }}</div>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-300 rounded font-mono text-[10px]">
                                            <i class="fa-solid fa-key mr-1"></i>{{ $tp->access_token ?: 'TANPA TOKEN' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ $tp->event_date->format('d M Y') }}</div>
                                    <div class="font-mono text-gray-400 text-[10px]">{{ $tp->quarter_code }}</div>
                                </td>
                                <td class="px-4 py-3 text-red-600 font-semibold">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $tp->deleted_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 font-bold">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 border border-gray-200">
                                        <i class="fa-solid fa-users text-[10px] mr-1 text-gray-400"></i>{{ $tp->candidates_count }} Peserta
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Restore Button -->
                                        <form action="{{ route('admin.periods.restore', $tp->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold rounded text-xs transition-colors" title="Pulihkan Periode ke List Aktif">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                        </form>

                                        <!-- Force Delete Button -->
                                        <form action="{{ route('admin.periods.force-delete', $tp->id) }}" method="POST" onsubmit="return confirm('PERINGATAN MANAJEMEN:\n\nApakah Anda yakin ingin menghapus PERMANEN periode ini?\nData yang dihapus permanen tidak dapat dikembalikan lagi!');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold rounded text-xs shadow-sm transition-colors" title="Hapus Permanen">
                                                <i class="fa-solid fa-skull"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400 font-medium">Trash / Sampah kosong. Tidak ada periode yang terhapus.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Edit Periode -->
<div id="modal-edit-period" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="bg-yellow-600 px-5 py-4 text-white flex items-center justify-between sticky top-0 z-10">
            <h3 class="font-black text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Periode Sumpah & Token Akses
            </h3>
            <button type="button" onclick="closeModal('modal-edit-period')" class="text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="form-edit-period" method="POST" enctype="multipart/form-data" class="p-5 space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" id="edit-period-name" name="name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                <input type="date" id="edit-period-date" name="event_date" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Siklus Triwulan <span class="text-red-500">*</span></label>
                <select id="edit-period-quarter" name="quarter_code" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-bold">
                    <option value="Q1">Q1 (Triwulan 1)</option>
                    <option value="Q2">Q2 (Triwulan 2)</option>
                    <option value="Q3">Q3 (Triwulan 3)</option>
                    <option value="Q4">Q4 (Triwulan 4)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Token Akses Mahasiswa</label>
                <input type="text" id="edit-period-token" name="access_token" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs font-mono font-bold text-[#4e73df] uppercase">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Link Google Drive (Upload PPT)</label>
                <input type="url" id="edit-period-drive" name="drive_url" placeholder="https://drive.google.com/..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-period')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-xs rounded shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
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

    function openEditPeriodModal(id, name, date, quarter, token, drive, status) {
        document.getElementById('form-edit-period').action = "{{ url('admin/periods') }}/" + id;
        document.getElementById('edit-period-name').value = name;
        document.getElementById('edit-period-date').value = date;
        document.getElementById('edit-period-quarter').value = quarter;
        document.getElementById('edit-period-token').value = token;
        document.getElementById('edit-period-drive').value = drive;
        document.getElementById('edit-period-status').value = status;

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
            activeBtn.className = "px-3 py-1.5 rounded-lg font-bold text-xs bg-[#4e73df] text-white shadow-sm flex items-center gap-1.5 transition-all";
            trashBtn.className = "px-3 py-1.5 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 flex items-center gap-1.5 transition-all";
        } else {
            activeView.classList.add('hidden');
            trashView.classList.remove('hidden');
            activeBtn.className = "px-3 py-1.5 rounded-lg font-bold text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 flex items-center gap-1.5 transition-all";
            trashBtn.className = "px-3 py-1.5 rounded-lg font-bold text-xs bg-red-600 text-white shadow-sm flex items-center gap-1.5 transition-all";
        }
    }
</script>
@endpush
@endsection
