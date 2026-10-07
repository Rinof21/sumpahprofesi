@extends('layouts.app')

@section('title', 'Kuota Fotografer Ruangan Resmi (Maks 3)')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Pembatasan Fotografer Ruangan Resmi (Maksimal 3 Orang)</h1>
    <p class="text-xs font-semibold text-gray-500">Kuota dibatasi tepat 3 orang per periode. Pendaftaran ke-4 ditolak otomatis oleh transaksi database.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Form Registration -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-[#4e73df]"></i> Daftarkan Fotografer
            </h2>
            <span class="px-2.5 py-0.5 rounded text-xs font-black border
                @if($photographers->count() >= 3) bg-red-100 text-red-700 border-red-200
                @else bg-emerald-100 text-emerald-700 border-emerald-200 @endif">
                {{ $photographers->count() }} / 3 Kuota
            </span>
        </div>

        @if($photographers->count() >= 3)
            <div class="p-3.5 rounded bg-red-50 border border-red-200 text-red-800 text-xs font-bold text-center">
                <i class="fa-solid fa-ban text-base mb-1 block"></i>
                KUOTA PENUH (MAKS. 3 FOTOGRAFER). Pendaftaran baru dikunci.
            </div>
        @else
            <form action="{{ route('admin.photographers.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="period_id" value="{{ $selectedPeriodId }}">

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Fotografer</label>
                    <input type="text" name="name" required placeholder="Nama Lengkap Fotografer" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Vendor / Agensi</label>
                    <input type="text" name="agency_name" required placeholder="Contoh: Lens Studio / Independen" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor WhatsApp</label>
                    <input type="text" name="phone_number" required placeholder="0812..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                </div>

                <button type="submit" class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">
                    Daftarkan Fotografer Resmi
                </button>
            </form>
        @endif
    </div>

    <!-- Photographers List & E-Badge Cards -->
    <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Daftar Juru Kamera / Fotografer Terverifikasi (Maks. 3)</h2>

        <div class="space-y-3">
            @forelse($photographers as $index => $photo)
                <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center font-black text-sm border border-blue-200">
                            #{{ $index + 1 }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $photo->name }}</div>
                            <div class="text-xs text-gray-600">Vendor: <strong class="text-gray-900">{{ $photo->agency_name }}</strong> &bull; WA: {{ $photo->phone_number }}</div>
                            <div class="text-[11px] font-mono text-[#4e73df] font-bold mt-0.5">Kode Badge: {{ $photo->badge_code }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.photographers.badge', $photo->id) }}" class="px-3 py-1.5 bg-[#4e73df] hover:bg-[#2e59d9] text-white text-xs font-bold rounded shadow-sm transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-id-card"></i> Cetak E-Badge
                        </a>
                        <form action="{{ route('admin.photographers.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Hapus fotografer ini dari daftar kuota?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-gray-400 border border-dashed border-gray-300 rounded-lg">
                    Belum ada fotografer resmi yang terdaftar untuk periode ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
