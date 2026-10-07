@extends('layouts.app')

@section('title', 'Superadmin Panel FKIK')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Superadmin Control Panel</h1>
        <p class="text-xs font-semibold text-gray-500">Pengelolaan Master Program Studi, Akun Pengguna, Spatie Roles, dan Meja Bantuan IT</p>
    </div>
</div>

<!-- SB Admin 2 Stat Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border-l-4 border-[#4e73df] p-5 flex items-center justify-between border border-gray-200">
        <div>
            <div class="text-[10px] font-black text-[#4e73df] uppercase tracking-wider mb-1">Master Program Studi</div>
            <div class="text-2xl font-black text-gray-800">{{ $totalProdi }}</div>
            <div class="text-[10px] text-gray-400 font-semibold mt-1">Multi-Prodi Engine</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-50 text-[#4e73df] flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-university"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border-l-4 border-cyan-500 p-5 flex items-center justify-between border border-gray-200">
        <div>
            <div class="text-[10px] font-black text-cyan-600 uppercase tracking-wider mb-1">Total Akun Pengguna</div>
            <div class="text-2xl font-black text-gray-800">{{ $totalUsers }}</div>
            <div class="text-[10px] text-gray-400 font-semibold mt-1">Spatie Roles & Permissions</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-users-cog"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border-l-4 border-emerald-500 p-5 flex items-center justify-between border border-gray-200">
        <div>
            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-wider mb-1">Narahubung IT Fakultas</div>
            <div class="text-2xl font-black text-gray-800">{{ $totalITContacts }}</div>
            <div class="text-[10px] text-gray-400 font-semibold mt-1">Meja Bantuan IT</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-headset"></i>
        </div>
    </div>
</div>

<!-- Quick Navigation Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <a href="{{ route('superadmin.study-programs.index') }}" class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="w-10 h-10 rounded-lg bg-blue-100 text-[#4e73df] flex items-center justify-center text-lg font-bold mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-900 group-hover:text-[#4e73df]">Master Program Studi</h3>
        <p class="text-xs text-gray-500 mt-1">Tambah prodi kesehatan baru (Dokter, Apoteker, Ners, Gizi, Bidan, dll.)</p>
    </a>

    <a href="{{ route('superadmin.users.index') }}" class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center text-lg font-bold mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-900 group-hover:text-cyan-700">Manajemen Pengguna & Peran</h3>
        <p class="text-xs text-gray-500 mt-1">Kelola hak akses Spatie Role (Superadmin, Admin Prodi, Peserta)</p>
    </a>

    <a href="{{ route('superadmin.it-contacts.index') }}" class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow group">
        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-phone-alt"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-900 group-hover:text-emerald-700">Narahubung IT Fakultas</h3>
        <p class="text-xs text-gray-500 mt-1">Kelola personil Meja Bantuan IT untuk kendala reset akun & upload PPT</p>
    </a>
</div>
@endsection
