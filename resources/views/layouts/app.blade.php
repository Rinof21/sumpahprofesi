<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Sumpah Profesi Kesehatan') - FKIK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sb: {
                            blue: '#4e73df',
                            darkblue: '#2e59d9',
                            navy: '#224abe',
                            sidebar: '#4e73df',
                            bg: '#f8f9fc',
                            card: '#ffffff'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Nunito Font (SB Admin 2 standard font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="h-full bg-[#f8f9fc] text-gray-800 antialiased flex flex-col min-h-screen">
@php
    $hasVerifiedToken = false;
    $sessId = session('verified_period_id');
    $sessExp = session('verified_period_expires_at');
    if ($sessId && $sessExp && now()->timestamp < $sessExp) {
        $hasVerifiedToken = true;
    } else {
        $cookieData = request()->cookie('verified_period_access');
        if ($cookieData) {
            $data = json_decode($cookieData, true);
            if (is_array($data) && isset($data['expires_at']) && now()->timestamp < $data['expires_at']) {
                $hasVerifiedToken = true;
            }
        }
    }
@endphp

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

    <!-- Mobile Sidebar Drawer (Sliding Drawer) -->
    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#4e73df] to-[#224abe] text-white transform -translate-x-full transition-transform duration-300 md:hidden shadow-2xl overflow-y-auto">
        <div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
            <div class="flex items-center gap-2 font-extrabold text-sm uppercase tracking-wider text-white">
                <i class="fa-solid fa-graduation-cap text-lg"></i> SUMPAH PROFESI
            </div>
            <button type="button" onclick="toggleMobileSidebar()" class="p-1.5 text-white/80 hover:text-white">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <div class="p-4 space-y-6">
            <!-- Section 1: PUBLIC PORTAL -->
            <div>
                <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Public Portal</div>
                <div class="space-y-1">
                    <a href="{{ route('public.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('public.index') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                        <i class="fa-solid fa-fw fa-house text-sm"></i>
                        <span>Portal Utama</span>
                    </a>
                    @if($hasVerifiedToken)
                        <a href="{{ route('token-access.portal') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('token-access.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                            <i class="fa-solid fa-fw fa-key text-sm"></i>
                            <span>Pendaftaran</span>
                        </a>
                    @endif
                    <a href="{{ route('public.helpdesk') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('public.helpdesk') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                        <i class="fa-solid fa-fw fa-headset text-sm"></i>
                        <span>Meja Bantuan</span>
                    </a>
                </div>
            </div>

            <!-- Section 2: ROLE BASED NAVIGATION -->
            @auth
                @role('Superadmin')
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Superadmin Menu</div>
                        <div class="space-y-1">
                            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-tachometer-alt text-sm"></i>
                                <span>Dashboard Admin</span>
                            </a>
                            <a href="{{ route('superadmin.study-programs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.study-programs.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-university text-sm"></i>
                                <span>Master Prodi</span>
                            </a>
                            <a href="{{ route('superadmin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.users.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-users-cog text-sm"></i>
                                <span>Pengguna & Spatie Role</span>
                            </a>
                            <a href="{{ route('superadmin.it-contacts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.it-contacts.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-phone-alt text-sm"></i>
                                <span>Narahubung IT</span>
                            </a>
                        </div>
                    </div>
                @endrole

                @role('Admin Prodi')
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Panitia Admin Prodi</div>
                        <div class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-tachometer-alt text-sm"></i>
                                <span>Dashboard Prodi</span>
                            </a>
                            <a href="{{ route('admin.periods.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.periods.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-calendar-alt text-sm"></i>
                                <span>Periode Sumpah</span>
                            </a>
                            <a href="{{ route('admin.candidates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.candidates.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-user-check text-sm"></i>
                                <span>Validasi Candidates</span>
                            </a>
                            <a href="{{ route('admin.photographers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.photographers.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-camera text-sm"></i>
                                <span>Fotografer (Maks 3)</span>
                            </a>
                            <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.gallery.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-images text-sm"></i>
                                <span>Kurasi Galeri</span>
                            </a>
                            <a href="{{ route('admin.contacts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.contacts.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-address-card text-sm"></i>
                                <span>Kontak Admin</span>
                            </a>
                        </div>
                    </div>
                @endrole

                @role('Peserta')
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Menu Peserta</div>
                        <div class="space-y-1">
                            <a href="{{ route('candidate.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('candidate.dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-user-graduate text-sm"></i>
                                <span>Dasbor Utama</span>
                            </a>
                            <a href="{{ route('candidate.oath-script') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('candidate.oath-script') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-scroll text-sm"></i>
                                <span>Naskah Lafal Sumpah</span>
                            </a>
                            <a href="{{ route('candidate.e-ticket') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('candidate.e-ticket') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-ticket-alt text-sm"></i>
                                <span>e-Ticket Undangan</span>
                            </a>
                        </div>
                    </div>
                @endrole
            @endauth
        </div>
    </aside>

    <div class="flex flex-1 min-h-screen">
        
        <!-- Desktop SB Admin 2 Sidebar (Stretches Full Height to Bottom) -->
        <aside class="w-64 bg-gradient-to-b from-[#4e73df] to-[#224abe] text-white shrink-0 hidden md:flex md:flex-col self-stretch min-h-full shadow-lg">
            
            <!-- Sidebar Brand -->
            <a href="{{ route('public.index') }}" class="h-16 flex items-center justify-center gap-3 px-4 border-b border-white/10 group">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-white font-extrabold text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="font-extrabold text-sm uppercase tracking-wider text-white">
                    SUMPAH PROFESI <span class="text-xs text-blue-200 block font-normal capitalize">FK UNTAN</span>
                </div>
            </a>

            <!-- Sidebar Navigation Menu -->
            <div class="p-4 space-y-6">
                
                <!-- Section 1: PUBLIC PORTAL -->
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Public Portal</div>
                    <div class="space-y-1">
                        <a href="{{ route('public.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('public.index') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                            <i class="fa-solid fa-fw fa-house text-sm"></i>
                            <span>Portal Utama</span>
                        </a>
                        @if($hasVerifiedToken)
                            <a href="{{ route('token-access.portal') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('token-access.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                <i class="fa-solid fa-fw fa-key text-sm"></i>
                                <span>Pendaftaran</span>
                            </a>
                        @endif
                        <a href="{{ route('public.helpdesk') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('public.helpdesk') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                            <i class="fa-solid fa-fw fa-headset text-sm"></i>
                            <span>Meja Bantuan</span>
                        </a>
                    </div>
                </div>

                <!-- Section 2: ROLE BASED NAVIGATION -->
                @auth
                    @role('Superadmin')
                        <div>
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Superadmin Menu</div>
                            <div class="space-y-1">
                                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-tachometer-alt text-sm"></i>
                                    <span>Dashboard Admin</span>
                                </a>
                                <a href="{{ route('superadmin.study-programs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.study-programs.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-university text-sm"></i>
                                    <span>Master Prodi</span>
                                </a>
                                <a href="{{ route('superadmin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.users.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-users-cog text-sm"></i>
                                    <span>Pengguna & Spatie Role</span>
                                </a>
                                <a href="{{ route('superadmin.it-contacts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('superadmin.it-contacts.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-phone-alt text-sm"></i>
                                    <span>Narahubung IT</span>
                                </a>
                            </div>
                        </div>
                    @endrole

                    @role('Admin Prodi')
                        <div>
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Panitia Admin Prodi</div>
                            <div class="space-y-1">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-tachometer-alt text-sm"></i>
                                    <span>Dashboard Prodi</span>
                                </a>
                                <a href="{{ route('admin.periods.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.periods.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-calendar-alt text-sm"></i>
                                    <span>Periode Sumpah</span>
                                </a>
                                <a href="{{ route('admin.candidates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.candidates.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-user-check text-sm"></i>
                                    <span>Validasi Candidates</span>
                                </a>
                                <a href="{{ route('admin.photographers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.photographers.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-camera text-sm"></i>
                                    <span>Fotografer (Maks 3)</span>
                                </a>
                                <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.gallery.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-images text-sm"></i>
                                    <span>Kurasi Galeri</span>
                                </a>
                                <a href="{{ route('admin.contacts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.contacts.*') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-address-card text-sm"></i>
                                    <span>Kontak Admin</span>
                                </a>
                            </div>
                        </div>
                    @endrole

                    @role('Peserta')
                        <div>
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200/70 mb-2 px-2">Menu Peserta</div>
                            <div class="space-y-1">
                                <a href="{{ route('candidate.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('candidate.dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-user-graduate text-sm"></i>
                                    <span>Dasbor Utama</span>
                                </a>
                                <a href="{{ route('candidate.oath-script') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('candidate.oath-script') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-scroll text-sm"></i>
                                    <span>Naskah Lafal Sumpah</span>
                                </a>
                                <a href="{{ route('candidate.e-ticket') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('candidate.e-ticket') ? 'bg-white/20 text-white shadow-sm' : 'text-blue-100 hover:bg-white/10' }}">
                                    <i class="fa-solid fa-fw fa-ticket-alt text-sm"></i>
                                    <span>e-Ticket Undangan</span>
                                </a>
                            </div>
                        </div>
                    @endrole
                @endauth

            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- SB Admin 2 Topbar (White Bar with Shadow) -->
            <header class="h-16 bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 flex items-center justify-between shrink-0 z-10">
                
                <!-- Mobile Toggle & Brand Header -->
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleMobileSidebar()" class="p-2 text-gray-700 hover:text-[#4e73df] md:hidden focus:outline-none" title="Toggle Sidebar Menu">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <a href="{{ route('public.index') }}" class="font-black text-sm text-[#4e73df] flex items-center gap-1.5 md:hidden">
                        <i class="fa-solid fa-graduation-cap text-base"></i> SUMPAH PROFESI
                    </a>
                </div>

                <div class="hidden md:block text-xs font-extrabold text-gray-700 uppercase tracking-wider">
                    SISTEM SUMPAH PROFESI FK UNTAN
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    @auth
                        <!-- Quick Role Switcher Dropdown (Dev Mode Helper) -->
                        <div class="relative group">
                            <button type="button" class="px-2.5 sm:px-3.5 py-1.5 sm:py-2 text-[11px] sm:text-xs font-extrabold rounded-lg bg-gray-100 border border-gray-300 hover:bg-gray-200 text-gray-800 flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-sync text-[#4e73df]"></i>
                                <span>Switch User</span>
                                <i class="fa-solid fa-chevron-down text-[9px]"></i>
                            </button>
                            <div class="absolute right-0 mt-1 w-64 bg-white border border-gray-200 rounded-xl shadow-xl py-2 hidden group-hover:block z-50">
                                <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Pilih Akun Demo</div>
                                @php
                                    $demoUsers = \App\Models\User::with(['roles', 'studyProgram'])->get();
                                @endphp
                                @foreach($demoUsers as $du)
                                    <a href="{{ route('demo.login-as', $du->id) }}" class="flex items-center justify-between px-3 py-2 text-xs text-gray-700 hover:bg-blue-50 hover:text-[#4e73df] transition-colors">
                                        <div class="truncate">
                                            <div class="font-bold">{{ $du->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $du->email }}</div>
                                        </div>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold 
                                            @if($du->hasRole('Superadmin')) bg-red-100 text-red-700 
                                            @elseif($du->hasRole('Admin Prodi')) bg-emerald-100 text-emerald-700 
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $du->roles->first()?->name }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- User Profile Badge -->
                        <div class="flex items-center space-x-2 sm:space-x-3 pl-2 sm:pl-3 border-l border-gray-200">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#4e73df] text-white flex items-center justify-center font-black text-xs shadow-sm">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-xs font-bold text-gray-900 leading-none">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-[#4e73df] font-extrabold mt-0.5">{{ Auth::user()->roles->first()?->name }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 sm:p-2 text-gray-400 hover:text-red-600 transition-colors" title="Keluar">
                                    <i class="fa-solid fa-sign-out-alt text-sm sm:text-base"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3 sm:px-4 py-1.5 sm:py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white text-xs font-extrabold rounded-lg shadow-sm transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-sign-in-alt"></i> Masuk Sistem
                        </a>
                    @endauth

                </div>
            </header>

            <!-- Notification Toast Alerts -->
            @if(session('success'))
                <div class="bg-emerald-50 border-b border-emerald-200 text-emerald-800 px-4 sm:px-6 py-3 shadow-sm" role="alert">
                    <div class="max-w-7xl mx-auto flex items-center justify-between text-xs font-bold">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-emerald-600 text-sm"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-b border-red-200 text-red-800 px-4 sm:px-6 py-3 shadow-sm" role="alert">
                    <div class="max-w-7xl mx-auto flex items-center justify-between text-xs font-bold">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-exclamation-triangle text-red-600 text-sm"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                    </div>
                </div>
            @endif

            <!-- Main Content Body -->
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <!-- Footer (SB Admin 2 Style) -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6 text-xs text-gray-500 mt-auto">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 font-semibold">
                    <div class="flex items-center gap-2 text-center sm:text-left">
                        <i class="fa-solid fa-graduation-cap text-[#4e73df] text-base"></i>
                        <span>Copyright &copy; {{ date('Y') }} <strong class="text-gray-800">Sistem Sumpah Profesi FK UNTAN</strong>. Hak Cipta Dilindungi Undang-Undang.</span>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-[11px] text-gray-500 font-bold">
                        <a href="{{ route('public.index') }}" class="hover:text-[#4e73df] transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-house"></i> Portal Utama
                        </a>
                        @if($hasVerifiedToken)
                            <span>&bull;</span>
                            <a href="{{ route('token-access.portal') }}" class="hover:text-[#4e73df] transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-key"></i> Pendaftaran
                            </a>
                        @endif
                        <span>&bull;</span>
                        <a href="{{ route('public.helpdesk') }}" class="hover:text-[#4e73df] transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-headset"></i> Meja Bantuan
                        </a>
                        <span>&bull;</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-[#4e73df] rounded text-[10px] border border-blue-200 font-mono font-bold">Laravel 12 & Spatie</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Toggle Mobile Sidebar Script -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
