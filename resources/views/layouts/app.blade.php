<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'E-BK SMAN 1 Lohbener')
    </title>

    {{-- =========================================================
         VITE / TAILWIND CSS
         ========================================================= --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- =========================================================
         LUCIDE ICON
         ========================================================= --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- =========================================================
         ALPINE JS
         Digunakan untuk notifikasi / interaksi UI
         ========================================================= --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>


<body class="bg-[#FDFCF8] text-[#333D32] min-h-screen">

    <div class="min-h-screen flex flex-col">


        <!-- =====================================================
             HEADER NAVBAR
             ===================================================== -->

        <header class="bg-white border-b border-[#E6E2D3] sticky top-0 z-40 shadow-sm">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="flex items-center justify-between h-16">


                    <!-- =================================================
                         BRAND LOGO
                         ================================================= -->

                    <div class="flex items-center gap-3">

                        <a href="{{ route('home') }}" class="w-10 h-10 flex-shrink-0 flex items-center justify-center">
                            <img
                                src="{{ asset('images/logo-sman1lohbener.jpg') }}"
                                alt="Logo SMAN 1 Lohbener"
                                class="w-full h-full object-contain"
                            >
                        </a>

                        <div>

                            <h1 class="font-bold text-base text-[#5A6D58] leading-none">
                                E-BK SMAN 1 Lohbener
                            </h1>

                            <p class="text-[11px] text-[#A39B8B] mt-0.5">
                                Sistem Pelayanan Bimbingan Konseling Digital
                            </p>

                        </div>

                    </div>


                    <!-- =================================================
                         NAVIGATION
                         ================================================= -->

                    <nav class="hidden lg:flex items-center gap-1">

                        {{-- =============================================
                             MENU SISWA
                             ============================================= --}}

                        @if(Auth::check() && Auth::user()->role === 'siswa')

                            <a
                                href="{{ route('dass21.index') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('dass21.*')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Screening I-DASS-Y
                            </a>


                            <a
                                href="{{ route('booking.index') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('booking.*')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Ajukan Konseling
                            </a>

                        @endif


                        {{-- =============================================
                             CHAT & AI
                             SISWA + GURU BK
                             ============================================= --}}

                        @if(
                            Auth::check() &&
                            in_array(Auth::user()->role, ['siswa', 'guru_bk'])
                        )

                            <a
                                href="{{ route('chat.index') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('chat.*')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Chat Privat
                            </a>

                        @endif


                        {{-- =============================================
                             MENU GURU BK
                             ============================================= --}}

                        @if(Auth::check() && Auth::user()->role === 'guru_bk')

                            <a
                                href="{{ route('booking.index') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('booking.*')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Jadwal Konseling
                            </a>


                            <a
                                href="{{ route('counselor.dashboard') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('counselor.dashboard')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Dashboard BK
                            </a>


                            <a
                                href="{{ route('counselor.siswa') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('counselor.siswa')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Daftar Siswa
                            </a>

                        @endif


                        {{-- =============================================
                             MENU KEPALA SEKOLAH
                             ============================================= --}}

                        @if(Auth::check() && Auth::user()->role === 'kepala_sekolah')

                            <a
                                href="{{ route('principal.dashboard') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('principal.dashboard')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Dashboard Kepsek
                            </a>


                            <a
                                href="{{ route('principal.guru-bk') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('principal.guru-bk')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Daftar Guru BK
                            </a>


                            <a
                                href="{{ route('principal.siswa') }}"
                                class="px-3.5 py-2 rounded-xl text-[11px] font-bold
                                transition-all
                                {{ request()->routeIs('principal.siswa')
                                    ? 'bg-[#5A6D58] text-white shadow-sm'
                                    : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}"
                            >
                                Daftar Siswa
                            </a>

                        @endif

                    </nav>


                    <!-- =================================================
                         USER PROFILE + NOTIFICATION
                         ================================================= -->

                    <div class="flex items-center gap-3">

                        {{-- =============================================
                             HAMBURGER BUTTON (Mobile Only)
                             ============================================= --}}

                        @if(Auth::check())
                            <button
                                type="button"
                                id="mobileHamburgerBtn"
                                class="lg:hidden p-2 text-[#5C564B] hover:bg-[#F5F2EB] rounded-xl border border-transparent hover:border-[#E6E2D3] transition-all"
                                onclick="toggleMobileSidebar()"
                                aria-label="Toggle Menu"
                            >
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>
                        @endif

                        @if(Auth::check())

                            <div class="relative" id="notificationDropdownWrapper">
                                <button
                                    type="button"
                                    id="notificationDropdownBtn"
                                    onclick="toggleNotificationDropdown()"
                                    class="p-2 text-[#5C564B] hover:bg-[#F5F2EB] rounded-full relative transition-all shadow-sm border border-transparent hover:border-[#E6E2D3]"
                                    title="Notifikasi"
                                    aria-label="Notifikasi"
                                >
                                    <i data-lucide="bell" class="w-5 h-5"></i>

                                    @if(($unreadNotificationsCount ?? 0) > 0)
                                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-[#E07A5F] border-2 border-white rounded-full"></span>
                                    @endif
                                </button>

                                @include('partials.notifications-drawer')
                            </div>

                        @endif


                        {{-- =============================================
                             PROFILE DROPDOWN
                             ============================================= --}}

                        @if(Auth::check())

                            <div
                                class="relative"
                                id="profileDropdownWrapper"
                            >


                                <!-- PROFILE BUTTON -->

                                <button
                                    type="button"
                                    onclick="toggleProfileDropdown()"
                                    class="flex items-center gap-2
                                    px-3 py-1.5
                                    bg-[#F5F2EB]
                                    hover:bg-[#EAE6D9]
                                    text-[#5A6D58]
                                    rounded-xl
                                    border border-[#E6E2D3]
                                    transition-all"
                                >


                                    <!-- Avatar -->

                                    <div
                                        class="w-8 h-8 rounded-full
                                        bg-[#5A6D58]
                                        text-white
                                        flex items-center justify-center
                                        font-bold text-xs"
                                    >
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>


                                    <!-- Nama -->

                                    <div class="hidden sm:block text-left">

                                        <p class="text-xs font-bold leading-tight">
                                            {{ Auth::user()->name }}
                                        </p>


                                        <p class="text-[10px] text-[#A39B8B] leading-tight mt-0.5">

                                            @if(Auth::user()->role === 'siswa')

                                                Siswa

                                            @elseif(Auth::user()->role === 'guru_bk')

                                                Guru BK

                                            @elseif(Auth::user()->role === 'kepala_sekolah')

                                                Kepala Sekolah

                                            @else

                                                User

                                            @endif

                                        </p>

                                    </div>


                                    <!-- Arrow -->

                                    <i
                                        data-lucide="chevron-down"
                                        class="w-4 h-4"
                                    ></i>

                                </button>


                                <!-- =========================================
                                     DROPDOWN
                                     ========================================= -->

                                <div
                                    id="profileDropdown"
                                    class="hidden absolute right-0 mt-2
                                    w-64 bg-white
                                    border border-[#E6E2D3]
                                    rounded-2xl
                                    shadow-lg
                                    overflow-hidden
                                    z-50"
                                >


                                    <!-- USER INFORMATION -->

                                    <div
                                        class="p-4
                                        bg-[#FDFCF8]
                                        border-b border-[#E6E2D3]"
                                    >

                                        <div class="flex items-center gap-3">


                                            <!-- Avatar Besar -->

                                            <div
                                                class="w-11 h-11 rounded-full
                                                bg-[#5A6D58]
                                                text-white
                                                flex items-center justify-center
                                                font-bold"
                                            >
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>


                                            <!-- User Info -->

                                            <div class="min-w-0">

                                                <p
                                                    class="font-bold text-sm
                                                    text-[#333D32]
                                                    truncate"
                                                >
                                                    {{ Auth::user()->name }}
                                                </p>


                                                <p
                                                    class="text-xs text-[#A39B8B]
                                                    truncate"
                                                >
                                                    {{ Auth::user()->email }}
                                                </p>

                                            </div>

                                        </div>


                                        <!-- ROLE -->

                                        <div class="mt-3">

                                            @if(Auth::user()->role === 'siswa')

                                                <span
                                                    class="inline-flex px-2.5 py-1
                                                    rounded-full text-[10px]
                                                    font-bold
                                                    bg-[#E8F0E7]
                                                    text-[#5A6D58]
                                                    border border-[#8BA888]"
                                                >
                                                    Siswa
                                                </span>

                                            @elseif(Auth::user()->role === 'guru_bk')

                                                <span
                                                    class="inline-flex px-2.5 py-1
                                                    rounded-full text-[10px]
                                                    font-bold
                                                    bg-[#E8F0E7]
                                                    text-[#5A6D58]
                                                    border border-[#8BA888]"
                                                >
                                                    Guru BK
                                                </span>

                                            @elseif(Auth::user()->role === 'kepala_sekolah')

                                                <span
                                                    class="inline-flex px-2.5 py-1
                                                    rounded-full text-[10px]
                                                    font-bold
                                                    bg-[#E8F0E7]
                                                    text-[#5A6D58]
                                                    border border-[#8BA888]"
                                                >
                                                    Kepala Sekolah
                                                </span>

                                            @else

                                                <span
                                                    class="inline-flex px-2.5 py-1
                                                    rounded-full text-[10px]
                                                    font-bold
                                                    bg-gray-100
                                                    text-gray-600
                                                    border border-gray-300"
                                                >
                                                    User
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    <!-- MENU PROFILE -->

                                    <div class="p-2">


                                        {{-- PROFIL --}}

                                        <a
                                            href="{{ route('profile') }}"
                                            class="flex items-center gap-3
                                            px-3 py-2.5
                                            rounded-xl
                                            text-xs font-semibold
                                            text-[#5C564B]
                                            hover:bg-[#F5F2EB]
                                            transition-all"
                                        >

                                            <i
                                                data-lucide="user"
                                                class="w-4 h-4 text-[#8BA888]"
                                            ></i>

                                            Profil Saya

                                        </a>


                                        {{-- LOGOUT --}}

                                        <form
                                            action="{{ route('logout') }}"
                                            method="POST"
                                            class="mt-1"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="w-full flex items-center gap-3
                                                px-3 py-2.5
                                                rounded-xl
                                                text-xs font-bold
                                                text-red-600
                                                hover:bg-red-50
                                                transition-all"
                                            >

                                                <i
                                                    data-lucide="log-out"
                                                    class="w-4 h-4"
                                                ></i>

                                                Keluar / Logout

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </header>


        <!-- =====================================================
             MOBILE SIDEBAR OVERLAY
             ===================================================== -->

        @if(Auth::check())

            <style>
                @media (min-width: 1024px) {
                    #mobileSidebarOverlay,
                    #mobileSidebar,
                    #mobileHamburgerBtn {
                        display: none !important;
                    }
                }
            </style>

            {{-- Overlay Background --}}
            <div
                id="mobileSidebarOverlay"
                class="fixed inset-0 bg-black/50 z-50 hidden transition-opacity duration-300 opacity-0"
                style="backdrop-filter: blur(2px);"
                onclick="toggleMobileSidebar()"
            ></div>

            {{-- Sidebar Panel --}}
            <aside
                id="mobileSidebar"
                class="fixed top-0 left-0 h-full bg-white z-50 shadow-2xl transition-transform duration-300 ease-in-out overflow-y-auto"
                style="width: 280px; max-width: 85vw; transform: translateX(-100%);"
            >

                {{-- Header Sidebar --}}
                <div class="flex items-center justify-between p-4 border-b border-[#E6E2D3] bg-[#FDFCF8]">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center justify-center" style="width: 36px; height: 36px;">
                            <img src="{{ asset('images/logo-sman1lohbener.jpg') }}" alt="Logo" style="width: 36px; height: 36px; object-fit: contain;">
                        </a>
                        <div>
                            <h2 class="font-bold text-sm text-[#5A6D58] leading-none">E-BK SMAN 1 Lohbener</h2>
                            <p class="text-[10px] text-[#A39B8B] mt-0.5">Sistem Pelayanan BK Digital</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleMobileSidebar()" class="p-2 text-[#5C564B] hover:bg-[#F5F2EB] rounded-xl transition-all" aria-label="Tutup Menu">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- User Info --}}
                <div class="p-4 border-b border-[#E6E2D3] bg-[#FDFCF8]">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-[#5A6D58] text-white flex items-center justify-center font-bold text-sm flex-shrink-0" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-[#333D32] truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-[#A39B8B] truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        @if(Auth::user()->role === 'siswa')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#E8F0E7] text-[#5A6D58] border border-[#8BA888]">Siswa</span>
                        @elseif(Auth::user()->role === 'guru_bk')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#E8F0E7] text-[#5A6D58] border border-[#8BA888]">Guru BK</span>
                        @elseif(Auth::user()->role === 'kepala_sekolah')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#E8F0E7] text-[#5A6D58] border border-[#8BA888]">Kepala Sekolah</span>
                        @endif
                    </div>
                </div>

                {{-- Navigation Menu --}}
                <nav class="p-3 space-y-1">

                    <p class="px-3 pt-2 pb-1 text-[10px] font-bold text-[#A39B8B] uppercase tracking-wider">Menu Navigasi</p>

                    {{-- MENU SISWA --}}
                    @if(Auth::user()->role === 'siswa')

                        <a href="{{ route('dass21.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('dass21.*') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="clipboard-list" class="w-4 h-4 flex-shrink-0"></i>
                            Screening I-DASS-Y
                        </a>

                        <a href="{{ route('booking.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('booking.*') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="calendar-plus" class="w-4 h-4 flex-shrink-0"></i>
                            Ajukan Konseling
                        </a>

                        <a href="{{ route('chat.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('chat.*') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="message-circle" class="w-4 h-4 flex-shrink-0"></i>
                            Chat Privat
                        </a>

                    @endif

                    {{-- MENU GURU BK --}}
                    @if(Auth::user()->role === 'guru_bk')

                        <a href="{{ route('chat.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('chat.*') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="message-circle" class="w-4 h-4 flex-shrink-0"></i>
                            Chat Privat
                        </a>

                        <a href="{{ route('booking.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('booking.*') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="calendar-check" class="w-4 h-4 flex-shrink-0"></i>
                            Jadwal Konseling
                        </a>

                        <a href="{{ route('counselor.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('counselor.dashboard') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
                            Dashboard BK
                        </a>

                        <a href="{{ route('counselor.siswa') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('counselor.siswa') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                            Daftar Siswa
                        </a>

                    @endif

                    {{-- MENU KEPALA SEKOLAH --}}
                    @if(Auth::user()->role === 'kepala_sekolah')

                        <a href="{{ route('principal.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('principal.dashboard') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
                            Dashboard Kepsek
                        </a>

                        <a href="{{ route('principal.guru-bk') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('principal.guru-bk') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="user-check" class="w-4 h-4 flex-shrink-0"></i>
                            Daftar Guru BK
                        </a>

                        <a href="{{ route('principal.siswa') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all
                           {{ request()->routeIs('principal.siswa') ? 'bg-[#5A6D58] text-white shadow-sm' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                            Daftar Siswa
                        </a>

                    @endif

                </nav>

                {{-- Divider --}}
                <div class="border-t border-[#E6E2D3] mx-3"></div>

                {{-- Bottom Menu --}}
                <div class="p-3 space-y-1">

                    <p class="px-3 pt-2 pb-1 text-[10px] font-bold text-[#A39B8B] uppercase tracking-wider">Akun</p>

                    <a href="{{ route('profile') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-[#5C564B] hover:bg-[#F5F2EB] transition-all">
                        <i data-lucide="user" class="w-4 h-4 text-[#8BA888]"></i>
                        Profil Saya
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition-all">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Keluar / Logout
                        </button>
                    </form>

                </div>

            </aside>

        @endif


        <!-- =========================================================
             MAIN CONTENT
             ========================================================= -->

        <main
            class="flex-1
            max-w-7xl
            w-full
            mx-auto
            px-4
            sm:px-6
            lg:px-8
            py-6"
        >


            {{-- SUCCESS MESSAGE --}}

            @if(session('success'))

                <div
                    class="mb-4 p-4
                    rounded-2xl
                    bg-[#F5F8F5]
                    border border-[#8BA888]/40
                    text-[#333D32]
                    text-xs
                    font-bold"
                >
                    {{ session('success') }}
                </div>

            @endif


            {{-- ERROR MESSAGE --}}

            @if(session('error'))

                <div
                    class="mb-4 p-4
                    rounded-2xl
                    bg-red-50
                    border border-red-200
                    text-red-700
                    text-xs
                    font-bold"
                >
                    {{ session('error') }}
                </div>

            @endif


            {{-- VALIDATION ERROR --}}

            @if($errors->any())

                <div
                    class="mb-4 p-4
                    rounded-2xl
                    bg-red-50
                    border border-red-200
                    text-red-700
                    text-xs"
                >

                    <ul class="list-disc list-inside">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            @yield('content')

        </main>


        <!-- =========================================================
             FOOTER
             ========================================================= -->

        <footer
            class="bg-[#4E614C]
            text-[#E6E2D3]
            border-t border-[#3E4F3C]
            text-xs
            py-4
            px-4
            text-center"
        >

            <div
                class="max-w-7xl mx-auto
                flex flex-col
                sm:flex-row
                items-center
                justify-between
                gap-2"
            >

                <span>
                    © 2026
                    <strong>E-BK SMAN 1 Lohbener</strong>
                    (Blade Version)
                </span>


                <span class="text-[11px] text-[#C5BEAE]">
                    Penulis: Rifqi Fadli Rachman
                    (NRP: 223040136 - UNPAS)
                </span>

            </div>

        </footer>

    </div>


    <!-- =========================================================
         PROFILE DROPDOWN JAVASCRIPT
         ========================================================= -->

    <script>

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Tutup dropdown jika klik di luar
        document.addEventListener('click', function (event) {
            const profileWrapper = document.getElementById('profileDropdownWrapper');
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileWrapper && profileDropdown && !profileWrapper.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }

            const notifWrapper = document.getElementById('notificationDropdownWrapper');
            const notifDropdown = document.getElementById('notificationDropdown');
            if (notifWrapper && notifDropdown && !notifWrapper.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
        });


        // Aktifkan Lucide Icons

        document.addEventListener('DOMContentLoaded', function () {

            if (typeof lucide !== 'undefined') {

                lucide.createIcons();

            }

        });


        // =========================================================
        // MOBILE SIDEBAR TOGGLE
        // =========================================================

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');
            if (!sidebar || !overlay) return;

            const isOpen = sidebar.style.transform === 'translateX(0px)' || sidebar.style.transform === 'translateX(0%)';

            if (isOpen) {
                // Tutup
                sidebar.style.transform = 'translateX(-100%)';
                overlay.style.opacity = '0';
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            } else {
                // Buka
                overlay.classList.remove('hidden');
                requestAnimationFrame(() => {
                    overlay.style.opacity = '1';
                    sidebar.style.transform = 'translateX(0%)';
                });
                document.body.style.overflow = 'hidden';
            }
        }

    </script>


    @stack('scripts')

</body>

</html>