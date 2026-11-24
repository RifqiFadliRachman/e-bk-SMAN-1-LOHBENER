<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Daftar Akun - E-BK SMAN 1 Lohbener</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen bg-[#F5F2EB]">

    <div class="min-h-screen flex items-center justify-center px-4 py-10">

        <div class="w-full max-w-md">

            <!-- Logo / Header -->
            <div class="text-center mb-8">

                <div class="mx-auto w-20 h-20 mb-4 flex items-center justify-center">
                    <img
                        src="{{ asset('images/logo-sman1lohbener.jpg') }}"
                        alt="Logo SMAN 1 Lohbener"
                        class="w-full h-full object-contain drop-shadow-md"
                    >
                </div>

                <h1 class="text-2xl font-bold text-[#333D32]">
                    E-BK
                </h1>

                <p class="text-sm text-[#6B7567] mt-1">
                    SMAN 1 Lohbener
                </p>

                <p class="text-xs text-[#A39B8B] mt-2">
                    Sistem Informasi Bimbingan dan Konseling
                </p>

            </div>


            <!-- Register Card -->
            <div
                class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xl p-7"
            >

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-[#333D32]">
                        Daftar Akun
                    </h2>

                    <p class="text-sm text-[#8A8F86] mt-1">
                        Buat akun siswa baru Anda.
                    </p>

                </div>


                <!-- Error -->
                @if(session('error'))

                    <div
                        class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex gap-3"
                    >

                        <i
                            data-lucide="circle-alert"
                            class="w-5 h-5 flex-shrink-0"
                        ></i>

                        <span>
                            {{ session('error') }}
                        </span>

                    </div>

                @endif


                <!-- Validation Errors -->
                @if($errors->any())

                    <div
                        class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm"
                    >

                        <ul class="list-disc list-inside space-y-1">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- Register Form -->
                <form
                    action="{{ route('register.process') }}"
                    method="POST"
                    class="space-y-5"
                >

                    @csrf


                    <!-- Nama Lengkap -->
                    <div>

                        <label
                            for="name"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Nama Lengkap
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="user"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                        </div>

                    </div>


                    <!-- Email -->
                    <div>

                        <label
                            for="email"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Email
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="mail"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                autocomplete="email"
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                        </div>

                    </div>


                    <!-- Kelas -->
                    <div>

                        <label
                            for="kelas"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Kelas
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="book-open"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="text"
                                id="kelas"
                                name="kelas"
                                value="{{ old('kelas') }}"
                                placeholder="Contoh: X IPA 1"
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                        </div>

                    </div>


                    <!-- Nama Wali Kelas -->
                    <div>

                        <label
                            for="nama_wali_kelas"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Nama Wali Kelas
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="graduation-cap"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="text"
                                id="nama_wali_kelas"
                                name="nama_wali_kelas"
                                value="{{ old('nama_wali_kelas') }}"
                                placeholder="Masukkan nama wali kelas"
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                        </div>

                    </div>


                    <!-- Nomor Telepon -->
                    <div>

                        <label
                            for="no_telepon"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Nomor Telepon
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="phone"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="tel"
                                id="no_telepon"
                                name="no_telepon"
                                value="{{ old('no_telepon') }}"
                                placeholder="Contoh: 08123456789"
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                        </div>

                    </div>


                    <!-- Password -->
                    <div>

                        <label
                            for="password"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Password
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="lock"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                required
                                class="w-full pl-12 pr-12 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('password', 'passwordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#A39B8B] hover:text-[#5A6D58]"
                            >

                                <i
                                    id="passwordIcon"
                                    data-lucide="eye"
                                    class="w-5 h-5"
                                ></i>

                            </button>

                        </div>

                    </div>


                    <!-- Konfirmasi Password -->
                    <div>

                        <label
                            for="password_confirmation"
                            class="block text-sm font-bold text-[#5A6D58] mb-2"
                        >
                            Konfirmasi Password
                        </label>

                        <div class="relative">

                            <i
                                data-lucide="lock-keyhole"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                required
                                class="w-full pl-12 pr-12 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('password_confirmation', 'confirmIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#A39B8B] hover:text-[#5A6D58]"
                            >

                                <i
                                    id="confirmIcon"
                                    data-lucide="eye"
                                    class="w-5 h-5"
                                ></i>

                            </button>

                        </div>

                    </div>


                    <!-- Submit -->
                    <button
                        type="submit"
                        class="w-full bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                    >

                        <i
                            data-lucide="user-plus"
                            class="w-5 h-5"
                        ></i>

                        Buat Akun

                    </button>

                </form>


                <!-- Link ke Login -->
                <p class="mt-6 text-center text-sm text-[#8A8F86]">

                    Sudah punya akun?

                    <a
                        href="{{ route('login') }}"
                        class="font-bold text-[#5A6D58] hover:text-[#4E614C] transition"
                    >
                        Masuk di sini
                    </a>

                </p>

            </div>


            <!-- Footer -->
            <div class="text-center mt-6">

                <p class="text-xs text-[#A39B8B]">
                    © {{ date('Y') }} E-BK SMAN 1 Lohbener
                </p>

                <p class="text-[10px] text-[#B5AEA0] mt-1">
                    Sistem Bimbingan dan Konseling Digital
                </p>

            </div>

        </div>

    </div>


    <script>

        lucide.createIcons();

        function togglePassword(inputId, iconId) {

            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);

            if (input.type === 'password') {

                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');

            } else {

                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');

            }

            lucide.createIcons();
        }

    </script>

</body>

</html>
