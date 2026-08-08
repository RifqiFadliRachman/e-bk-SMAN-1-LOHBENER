<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-BK SMAN 1 Lohbener')</title>
    <!-- Tailwind CSS & Lucide Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FDFCF8] text-[#333D32] min-h-screen flex flex-col">

    <!-- Header Navbar -->
    <header class="bg-white border-b border-[#E6E2D3] sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#5A6D58] text-[#FDFCF8] flex items-center justify-center font-bold text-lg shadow-xs border border-[#4A5D48]">
                        BK
                    </div>
                    <div>
                        <h1 class="font-bold text-base text-[#5A6D58] leading-none">E-BK SMAN 1 Lohbener</h1>
                        <p class="text-[11px] text-[#A39B8B] mt-0.5">Sistem Pelayanan Bimbingan Konseling Digital</p>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <nav class="hidden lg:flex items-center gap-1">

                    {{-- Menu Akses Semua Role (Siswa, BK, Kepsek punya kepentingan berbeda tapi bisa disatukan atau dibatasi) --}}
                    @if((Auth::user()->role ?? 'siswa') === 'siswa')
                        <a href="{{ route('dass21.index') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all {{ request()->routeIs('dass21.*') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            Screening DASS-21
                        </a>
                        <a href="{{ route('booking.index') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all {{ request()->routeIs('booking.*') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            Booking
                        </a>
                    @endif

                    {{-- Chat Privat & AI (Bisa diakses Siswa maupun Guru BK) --}}
                    @if(in_array((Auth::user()->role ?? 'siswa'), ['siswa', 'guru_bk']))
                        <a href="{{ route('chat.index') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all {{ request()->routeIs('chat.*') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            Chat Privat
                        </a>
                        <a href="{{ route('counselor.ai-assistant') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all flex items-center gap-1 {{ request()->routeIs('counselor.ai-assistant') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            <i data-lucide="bot" class="w-3.5 h-3.5"></i> AI Asisten
                        </a>
                    @endif

                    {{-- Menu Khusus Guru BK --}}
                    @if((Auth::user()->role ?? 'siswa') === 'guru_bk')
                        <a href="{{ route('booking.index') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all {{ request()->routeIs('booking.*') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            Jadwal Booking
                        </a>
                        <a href="{{ route('counselor.dashboard') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all {{ request()->routeIs('counselor.dash*') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            Dashboard BK
                        </a>
                    @endif

                    {{-- Menu Khusus Kepala Sekolah / Admin --}}
                    @if((Auth::user()->role ?? 'siswa') === 'kepala_sekolah')
                        <a href="{{ route('principal.dashboard') }}" class="px-3.5 py-2 rounded-xl text-[11px] font-bold transition-all {{ request()->routeIs('principal.*') ? 'bg-[#5A6D58] text-white shadow-xs' : 'text-[#5C564B] hover:bg-[#F5F2EB]' }}">
                            Dashboard Kepsek
                        </a>
                    @endif
                </nav>

                <!-- User Profile & Notifications -->
                <div class="flex items-center gap-3">
                    <button x-data @click="$dispatch('open-notifications')" class="p-2 text-[#5C564B] hover:bg-[#F5F2EB] rounded-full relative transition-all shadow-sm border border-transparent hover:border-[#E6E2D3]">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-[#E07A5F] border-2 border-white rounded-full"></span>
                    </button>
                    <span class="text-xs font-bold bg-[#F5F2EB] text-[#5A6D58] px-3 py-1.5 rounded-xl border border-[#E6E2D3]">
                        {{ Auth::user()->name ?? 'Ahmad Rizky' }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-[#F5F8F5] border border-[#8BA888]/40 text-[#333D32] text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Notifications Drawer Include -->
    @include('partials.notifications-drawer')

    <!-- Footer -->
    <footer class="bg-[#4E614C] text-[#E6E2D3] border-t border-[#3E4F3C] text-xs py-4 px-4 text-center">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>© 2026 <strong>E-BK SMAN 1 Lohbener</strong> (Blade Version)</span>
            <span class="text-[11px] text-[#C5BEAE]">Penulis: Rifqi Fadli Rachman (NRP: 223040136 - UNPAS)</span>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
