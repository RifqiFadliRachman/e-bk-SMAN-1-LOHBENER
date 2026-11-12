@extends('layouts.app')

@section('title', 'Profil Saya - E-BK SMAN 1 Lohbener')

@section('content')

<div class="max-w-2xl mx-auto" x-data="{ tab: '{{ session('tab', 'profil') }}', isEditing: false }">

    <!-- Header -->
    <div class="mb-6">

        <h2 class="text-xl font-bold text-[#333D32]">
            Pengaturan Akun
        </h2>

        <p class="text-sm text-[#8A8F86] mt-1">
            Kelola informasi profil dan keamanan akun Anda.
        </p>

    </div>


    <!-- Tabs -->
    <div class="flex items-center gap-2 mb-6 border-b border-[#E6E2D3] pb-px">

        <button
            @click="tab = 'profil'"
            :class="tab === 'profil' ? 'border-[#5A6D58] text-[#5A6D58] bg-[#F5F8F5]' : 'border-transparent text-[#8A8F86] hover:text-[#5A6D58] hover:bg-[#F5F2EB]'"
            class="px-4 py-2.5 text-sm font-bold border-b-2 rounded-t-xl transition flex items-center gap-2"
        >
            <i data-lucide="user" class="w-4 h-4"></i>
            Profil Saya
        </button>

        <button
            @click="tab = 'password'"
            :class="tab === 'password' ? 'border-[#5A6D58] text-[#5A6D58] bg-[#F5F8F5]' : 'border-transparent text-[#8A8F86] hover:text-[#5A6D58] hover:bg-[#F5F2EB]'"
            class="px-4 py-2.5 text-sm font-bold border-b-2 rounded-t-xl transition flex items-center gap-2"
        >
            <i data-lucide="key-round" class="w-4 h-4"></i>
            Ubah Password
        </button>

    </div>


    <!-- Profile Tab -->
    <div x-show="tab === 'profil'" x-transition.opacity>

        <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-sm overflow-hidden relative">

            <!-- Avatar Header -->
            <div class="bg-[#5A6D58] px-6 py-8 flex items-center justify-between">
                
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 rounded-full bg-white/20 text-white
                        flex items-center justify-center font-bold text-2xl
                        border-2 border-white/40 shadow"
                    >
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div>

                        <p class="text-white font-bold text-lg leading-tight">
                            {{ Auth::user()->name }}
                        </p>

                        <p class="text-white opacity-90 text-sm mt-0.5">
                            {{ Auth::user()->email }}
                        </p>

                        <span
                            class="inline-flex mt-2 px-3 py-1 rounded-full text-[11px]
                            font-bold bg-white/20 text-white border border-white/30"
                        >
                            @if(Auth::user()->role === 'siswa')
                                Siswa
                            @elseif(Auth::user()->role === 'guru_bk')
                                Guru BK
                            @elseif(Auth::user()->role === 'kepala_sekolah')
                                Kepala Sekolah
                            @else
                                {{ Auth::user()->role }}
                            @endif
                        </span>

                    </div>
                </div>

                <!-- Edit Toggle Button -->
                <button 
                    @click="isEditing = !isEditing"
                    class="bg-white/10 hover:bg-white/20 text-white border border-white/30 px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 backdrop-blur-sm shadow-sm"
                >
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                    <span x-text="isEditing ? 'Batal Edit' : 'Edit Profil'"></span>
                </button>

            </div>


            <!-- READ MODE -->
            <div x-show="!isEditing" class="p-6 space-y-4">

                <!-- Nama Lengkap -->
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                        <i data-lucide="user" class="w-4 h-4 text-[#5A6D58]"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Nama Lengkap</p>
                        <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->name }}</p>
                    </div>
                </div>

                <hr class="border-[#F0EDE6]">

                <!-- Email -->
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                        <i data-lucide="mail" class="w-4 h-4 text-[#5A6D58]"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Email</p>
                        <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Fields for Siswa only -->
                @if(Auth::user()->role === 'siswa')
                    <hr class="border-[#F0EDE6]">

                    <!-- Kelas -->
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="book-open" class="w-4 h-4 text-[#5A6D58]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Kelas</p>
                            <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->kelas ?? '-' }}</p>
                        </div>
                    </div>

                    <hr class="border-[#F0EDE6]">

                    <!-- Nama Wali Kelas -->
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="graduation-cap" class="w-4 h-4 text-[#5A6D58]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Nama Wali Kelas</p>
                            <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->nama_wali_kelas ?? '-' }}</p>
                        </div>
                    </div>

                    <hr class="border-[#F0EDE6]">

                    <!-- Nomor Telepon -->
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="phone" class="w-4 h-4 text-[#5A6D58]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Nomor Telepon</p>
                            <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->no_telepon ?? '-' }}</p>
                        </div>
                    </div>

                    <hr class="border-[#F0EDE6]">

                    <!-- Alamat Tinggal -->
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="home" class="w-4 h-4 text-[#5A6D58]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Alamat Tinggal</p>
                            <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->alamat ?? '-' }}</p>
                        </div>
                    </div>

                    <hr class="border-[#F0EDE6]">

                    <!-- Nama Orang Tua / Wali -->
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="users" class="w-4 h-4 text-[#5A6D58]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Nama Orang Tua / Wali</p>
                            <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->nama_orang_tua ?? '-' }}</p>
                        </div>
                    </div>

                    <hr class="border-[#F0EDE6]">

                    <!-- Kontak Darurat Orang Tua -->
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl bg-[#F0F4F0] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="phone-call" class="w-4 h-4 text-[#5A6D58]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-[#A39B8B] font-semibold uppercase tracking-wide">Kontak Darurat Orang Tua</p>
                            <p class="text-sm font-bold text-[#333D32] mt-0.5">{{ Auth::user()->kontak_darurat ?? '-' }}</p>
                        </div>
                    </div>
                @endif

            </div>


            <!-- EDIT MODE (FORM) -->
            <form x-show="isEditing" action="{{ route('profile.update') }}" method="POST" class="p-6 pb-8 space-y-5" style="display: none;">
                @csrf
                @method('PUT')

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                    </div>
                </div>

                @if(Auth::user()->role === 'siswa')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Kelas -->
                        <div>
                            <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Kelas</label>
                            <div class="relative">
                                <i data-lucide="book-open" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                                <input type="text" name="kelas" value="{{ old('kelas', Auth::user()->kelas) }}" required
                                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                            </div>
                        </div>

                        <!-- Wali Kelas -->
                        <div>
                            <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Nama Wali Kelas</label>
                            <div class="relative">
                                <i data-lucide="graduation-cap" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                                <input type="text" name="nama_wali_kelas" value="{{ old('nama_wali_kelas', Auth::user()->nama_wali_kelas) }}" required
                                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                            </div>
                        </div>
                    </div>

                    <!-- No Telepon -->
                    <div>
                        <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Nomor Telepon</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                            <input type="tel" name="no_telepon" value="{{ old('no_telepon', Auth::user()->no_telepon) }}"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                        </div>
                    </div>

                    <!-- Alamat Tinggal -->
                    <div>
                        <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Alamat Tinggal</label>
                        <div class="relative">
                            <i data-lucide="home" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                            <input type="text" name="alamat" value="{{ old('alamat', Auth::user()->alamat) }}"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                        </div>
                    </div>

                    <!-- Nama Orang Tua / Wali -->
                    <div>
                        <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Nama Orang Tua / Wali</label>
                        <div class="relative">
                            <i data-lucide="users" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                            <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', Auth::user()->nama_orang_tua) }}"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                        </div>
                    </div>

                    <div>
                        <!-- Kontak Darurat Orang Tua -->
                        <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Kontak Darurat Orang Tua</label>
                        <div class="relative">
                            <i data-lucide="phone-call" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                            <input type="tel" name="kontak_darurat" value="{{ old('kontak_darurat', Auth::user()->kontak_darurat) }}"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                        </div>
                    </div>
                @endif

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" @click="isEditing = false"
                        class="px-5 py-2.5 rounded-xl border border-[#E6E2D3] bg-white text-[#5C564B] font-bold text-sm tracking-wide hover:bg-[#F5F2EB] transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#5A6D58] text-white font-bold text-sm tracking-wide hover:bg-[#4E614C] transition flex items-center gap-2 shadow-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>

    </div>


    <!-- Password Tab -->
    <div x-show="tab === 'password'" x-transition.opacity style="display: none;">
        
        <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-sm p-7">

            <div class="mb-6">
                <h3 class="text-lg font-bold text-[#333D32] flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-[#5A6D58]"></i>
                    Ubah Password
                </h3>
                <p class="text-xs text-[#8A8F86] mt-1">Pastikan password baru Anda kuat dan sulit ditebak.</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Password Saat Ini</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                        <input type="password" name="current_password" required
                            class="w-full pl-10 pr-10 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-[#F0EDE6]">

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Password Baru</label>
                    <div class="relative">
                        <i data-lucide="key" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                        <input type="password" name="new_password" required
                            class="w-full pl-10 pr-10 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <i data-lucide="lock-keyhole" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#A39B8B]"></i>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full pl-10 pr-10 py-2.5 text-sm bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition">
                    </div>
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#5A6D58] text-white font-bold text-sm tracking-wide hover:bg-[#4E614C] transition flex items-center gap-2 shadow-sm">
                        <i data-lucide="key-round" class="w-4 h-4"></i>
                        Perbarui Password
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection
