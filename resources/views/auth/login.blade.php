@extends('layouts.app')

@section('title', 'Masuk Sistem')

@section('content')
<div class="min-h-[75vh] flex flex-col justify-center py-8 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex p-3 rounded-2xl bg-[#4e73df] text-white font-black text-2xl shadow mb-2">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Sumpah Profesi FKIK</h2>
        <p class="mt-1 text-xs font-semibold text-gray-500">Silakan masuk menggunakan akun Anda</p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        <!-- SB Admin 2 Card -->
        <div class="bg-white py-8 px-6 shadow-md rounded-xl border border-gray-200 sm:px-10">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Alamat Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition-all"
                        placeholder="nama@domain.ac.id">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Kata Sandi</label>
                    <input id="password" name="password" type="password" required 
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#4e73df] focus:bg-white transition-all"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#4e73df] focus:ring-[#4e73df]">
                        <span class="ml-2 font-semibold">Ingat Saya</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-extrabold text-xs rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-sign-in-alt"></i> Masuk Sekarang
                    </button>
                </div>
            </form>

            <!-- Quick Demo Accounts SB Admin 2 Card -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="text-[11px] font-black text-[#4e73df] uppercase tracking-wider text-center mb-3">
                    ⚡ Akses Uji Coba Cepat (1-Click Demo Login)
                </div>

                @php
                    $demoAccounts = \App\Models\User::with(['roles', 'studyProgram'])->get();
                @endphp

                <div class="space-y-2">
                    @foreach($demoAccounts as $account)
                        <a href="{{ route('demo.login-as', $account->id) }}" class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 border border-gray-200 hover:border-[#4e73df] hover:bg-blue-50 transition-all group">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-md flex items-center justify-center font-bold text-xs
                                    @if($account->hasRole('Superadmin')) bg-red-100 text-red-700
                                    @elseif($account->hasRole('Admin Prodi')) bg-emerald-100 text-emerald-700
                                    @else bg-blue-100 text-blue-700 @endif">
                                    <i class="fa-solid 
                                        @if($account->hasRole('Superadmin')) fa-user-shield
                                        @elseif($account->hasRole('Admin Prodi')) fa-user-tie
                                        @else fa-user-graduate @endif"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-gray-800 group-hover:text-[#4e73df]">{{ $account->name }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $account->email }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border
                                @if($account->hasRole('Superadmin')) bg-red-50 text-red-700 border-red-200
                                @elseif($account->hasRole('Admin Prodi')) bg-emerald-50 text-emerald-700 border-emerald-200
                                @else bg-blue-50 text-blue-700 border-blue-200 @endif">
                                {{ $account->roles->first()?->name }} {{ $account->studyProgram ? "({$account->studyProgram->code})" : '' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
