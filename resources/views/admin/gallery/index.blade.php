@extends('layouts.app')

@section('title', 'Kurator Galeri & Video Dokumentasi')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Kurator Galeri Foto Pasca-Acara & Video YouTube</h1>
    <p class="text-xs font-semibold text-gray-500">Unggah 10 foto kurasi terbaik dan input link rekaman YouTube live streaming.</p>
</div>

<!-- Select Period Form -->
<div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6 max-w-xl">
    <form method="GET" action="{{ route('admin.gallery.index') }}" class="flex items-center gap-3">
        <div class="flex-grow">
            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Pilih Periode Sumpah</label>
            <select name="period_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900 font-semibold focus:outline-none">
                @foreach($periods as $per)
                    <option value="{{ $per->id }}" {{ $selectedPeriodId == $per->id ? 'selected' : '' }}>
                        {{ $per->name }} ({{ $per->status }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if($period)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- YouTube URL & Upload Photo Forms -->
        <div class="space-y-6">
            
            <!-- YouTube URL -->
            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-brands fa-youtube text-red-600"></i> Video Live Stream YouTube
                </h2>
                <form action="{{ route('admin.gallery.update-youtube', $period->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tautan URL Video YouTube</label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url', $period->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                    </div>
                    <button type="submit" class="w-full py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded shadow-sm">
                        Simpan Tautan YouTube
                    </button>
                </form>
            </div>

            <!-- Upload 10 Photos Form -->
            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-cloud-upload-alt text-[#4e73df]"></i> Unggah Foto Terbaik
                    </h2>
                    <span class="px-2.5 py-0.5 rounded text-xs font-black border
                        @if($photos->count() >= 10) bg-red-100 text-red-700 border-red-200
                        @else bg-emerald-100 text-emerald-700 border-emerald-200 @endif">
                        {{ $photos->count() }} / 10 Foto
                    </span>
                </div>

                @if($photos->count() >= 10)
                    <div class="p-3 bg-red-50 border border-red-200 text-red-800 text-xs font-bold text-center rounded">
                        Batas maksimal 10 foto kurasi terbaik telah tercapai!
                    </div>
                @else
                    <form action="{{ route('admin.gallery.store-photo') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="period_id" value="{{ $period->id }}">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Berkas Foto (.jpg, .png)</label>
                            <input type="file" name="photo" required accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi / Caption Foto</label>
                            <input type="text" name="caption" placeholder="Deskripsi momen foto..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-xs text-gray-900">
                        </div>
                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow-sm">
                            Tambah Foto Kurasi
                        </button>
                    </form>
                @endif
            </div>

        </div>

        <!-- Photos Grid List -->
        <div class="lg:col-span-2 bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <h2 class="text-sm font-bold text-gray-900 mb-4">Galeri 10 Foto Terbaik Periode Ini</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($photos as $photo)
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 relative group">
                        <div class="aspect-video w-full overflow-hidden rounded bg-gray-200 mb-2">
                            <img src="{{ Str::startsWith($photo->photo_path, 'http') ? $photo->photo_path : asset('storage/' . $photo->photo_path) }}" alt="Foto" class="w-full h-full object-cover">
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#4e73df]">Urutan #{{ $photo->sort_order }}</span>
                            <form action="{{ route('admin.gallery.destroy-photo', $photo->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                        @if($photo->caption)
                            <p class="text-[11px] text-gray-600 mt-1 truncate">{{ $photo->caption }}</p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full p-6 text-center text-xs text-gray-400">
                        Belum ada foto kurasi yang diunggah.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endif
@endsection
