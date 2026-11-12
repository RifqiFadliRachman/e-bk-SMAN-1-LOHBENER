<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - E-BK SMAN 1 Lohbener</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen bg-[#F5F2EB]">

    <div class="min-h-screen flex items-center justify-center px-4 py-8">

        <div class="w-full max-w-md">

            <!-- Logo / Header -->
            <div class="text-center mb-8">

                <div
                    class="mx-auto w-16 h-16 bg-[#5A6D58] rounded-2xl flex items-center justify-center shadow-lg mb-4"
                >
                    <i
                        data-lucide="heart-handshake"
                        class="w-8 h-8 text-white"
                    ></i>
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


            <!-- Login Card -->
            <div
                class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xl p-7"
            >

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-[#333D32]">
                        Selamat Datang
                    </h2>

                    <p class="text-sm text-[#8A8F86] mt-1">
                        Silakan masuk menggunakan akun Anda.
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


                <!-- Success -->
                @if(session('success'))

                    <div
                        class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex gap-3"
                    >

                        <i
                            data-lucide="circle-check"
                            class="w-5 h-5 flex-shrink-0"
                        ></i>

                        <span>
                            {{ session('success') }}
                        </span>

                    </div>

                @endif


                <!-- Validation Error -->
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


                <!-- Login Form -->
                <form
                    action="{{ route('login.process') }}"
                    method="POST"
                    class="space-y-5"
                >

                    @csrf


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


                    <!-- Password -->
                    <div>

                        <div class="flex items-center justify-between mb-2">

                            <label
                                for="password"
                                class="block text-sm font-bold text-[#5A6D58]"
                            >
                                Password
                            </label>

                        </div>

                        <div class="relative">

                            <i
                                data-lucide="lock"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#A39B8B]"
                            ></i>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                                class="w-full pl-12 pr-12 py-3.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32] placeholder-[#A39B8B] focus:border-[#8BA888] focus:ring-2 focus:ring-[#8BA888]/20 transition"
                            >

                            <button
                                type="button"
                                onclick="togglePassword()"
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


                    <!-- Submit -->
                    <button
                        type="submit"
                        class="w-full bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                    >

                        <i
                            data-lucide="log-in"
                            class="w-5 h-5"
                        ></i>

                        Masuk ke Sistem

                    </button>

                </form>


                <!-- Link ke Registrasi -->
                <p class="mt-6 text-center text-sm text-[#8A8F86]">

                    Belum punya akun?

                    <a
                        href="{{ route('register') }}"
                        class="font-bold text-[#5A6D58] hover:text-[#4E614C] transition"
                    >
                        Daftar di sini
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

        function togglePassword() {

            const password =
                document.getElementById('password');

            const icon =
                document.getElementById('passwordIcon');

            if (password.type === 'password') {

                password.type = 'text';

                icon.setAttribute('data-lucide', 'eye-off');

            } else {

                password.type = 'password';

                icon.setAttribute('data-lucide', 'eye');

            }

            lucide.createIcons();
        }

    </script>

</body>

</html>