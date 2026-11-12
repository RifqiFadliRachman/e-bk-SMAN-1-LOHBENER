<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        // Jika user sudah login,
        // langsung arahkan sesuai role.
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * Tampilkan halaman registrasi siswa.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.register');
    }

    /**
     * Tampilkan halaman profil pengguna saat ini.
     */
    public function showProfile()
    {
        return view('profile.index');
    }

    /**
     * Update data profil pengguna (nama, email, kelas, wali kelas, no telepon).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'unique:users,email,' . $user->id],
            'no_telepon'      => ['nullable', 'string', 'max:20'],
        ];

        $messages = [
            'name.required'    => 'Nama lengkap wajib diisi.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email sudah digunakan akun lain.',
        ];

        // Field tambahan khusus siswa
        if ($user->role === 'siswa') {
            $rules['kelas']           = ['required', 'string', 'max:50'];
            $rules['nama_wali_kelas'] = ['required', 'string', 'max:255'];
            $rules['alamat']          = ['nullable', 'string', 'max:1000'];
            $rules['nama_orang_tua']  = ['nullable', 'string', 'max:255'];
            $rules['kontak_darurat']  = ['nullable', 'string', 'max:50'];

            $messages['kelas.required']           = 'Kelas wajib diisi.';
            $messages['nama_wali_kelas.required'] = 'Nama wali kelas wajib diisi.';
        }

        $validated = $request->validate($rules, $messages);

        $updateData = [
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'no_telepon' => $validated['no_telepon'] ?? null,
        ];

        if ($user->role === 'siswa') {
            $updateData['kelas']           = $validated['kelas'];
            $updateData['nama_wali_kelas'] = $validated['nama_wali_kelas'];
            $updateData['alamat']          = $validated['alamat'] ?? null;
            $updateData['nama_orang_tua']  = $validated['nama_orang_tua'] ?? null;
            $updateData['kontak_darurat']  = $validated['kontak_darurat'] ?? null;
        }

        $user->update($updateData);

        return redirect()->route('profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'current_password'      => ['required', 'string'],
                'new_password'          => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'current_password.required' => 'Password saat ini wajib diisi.',
                'new_password.required'     => 'Password baru wajib diisi.',
                'new_password.min'          => 'Password baru minimal 8 karakter.',
                'new_password.confirmed'    => 'Konfirmasi password baru tidak cocok.',
            ]
        );

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini salah.'])
                ->with('tab', 'password');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile')
            ->with('success', 'Password berhasil diubah.');
    }

    /**
     * Proses pendaftaran siswa baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name'            => ['required', 'string', 'max:255'],
                'email'           => ['required', 'email', 'unique:users,email'],
                'password'        => ['required', 'string', 'min:8', 'confirmed'],
                'kelas'           => ['required', 'string', 'max:50'],
                'nama_wali_kelas' => ['required', 'string', 'max:255'],
                'no_telepon'      => ['required', 'string', 'max:20'],
            ],
            [
                'name.required'            => 'Nama lengkap wajib diisi.',
                'email.required'           => 'Email wajib diisi.',
                'email.email'              => 'Format email tidak valid.',
                'email.unique'             => 'Email sudah terdaftar.',
                'password.required'        => 'Password wajib diisi.',
                'password.min'             => 'Password minimal 8 karakter.',
                'password.confirmed'       => 'Konfirmasi password tidak cocok.',
                'kelas.required'           => 'Kelas wajib diisi.',
                'nama_wali_kelas.required' => 'Nama wali kelas wajib diisi.',
                'no_telepon.required'      => 'Nomor telepon wajib diisi.',
            ]
        );

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'role'            => 'siswa',
            'kelas'           => $validated['kelas'],
            'nama_wali_kelas' => $validated['nama_wali_kelas'],
            'no_telepon'      => $validated['no_telepon'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dass21.index')
            ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '.');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        // Validasi form login
        $credentials = $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                ],

                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Cari user
        |--------------------------------------------------------------------------
        */

        $user = DB::table('users')
            ->where('email', $credentials['email'])
            ->first();

        // Jika email tidak ditemukan
        if (!$user) {
            return back()
                ->withInput(
                    $request->only('email')
                )
                ->with(
                    'error',
                    'Email atau password salah.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cek password
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $credentials['password'],
            $user->password
        )) {
            return back()
                ->withInput(
                    $request->only('email')
                )
                ->with(
                    'error',
                    'Email atau password salah.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Login user
        |--------------------------------------------------------------------------
        */

        Auth::loginUsingId($user->id);

        /*
        |--------------------------------------------------------------------------
        | Regenerate session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        return $this->redirectByRole($user->role);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session
        $request->session()->invalidate();

        // Buat CSRF token baru
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Anda berhasil logout.'
            );
    }

    /**
     * Redirect berdasarkan role user.
     */
    private function redirectByRole($role)
    {
        /*
        |--------------------------------------------------------------------------
        | Normalisasi role
        |--------------------------------------------------------------------------
        |
        | Untuk mengantisipasi jika database lama masih menggunakan:
        |
        | kepala
        |
        | maka akan dianggap sebagai:
        |
        | kepala_sekolah
        |
        */

        if ($role === 'kepala') {
            $role = 'kepala_sekolah';
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return match ($role) {

            // Siswa
            'siswa' => redirect()
                ->route('dass21.index'),

            // Guru BK
            'guru_bk' => redirect()
                ->route('counselor.dashboard'),

            // Kepala Sekolah
            'kepala_sekolah' => redirect()
                ->route('principal.dashboard'),

            // Role tidak dikenal
            default => $this->logoutInvalidRole(),
        };
    }

    /**
     * Logout jika role tidak valid.
     */
    private function logoutInvalidRole()
    {
        Auth::logout();

        return redirect()
            ->route('login')
            ->with(
                'error',
                'Role akun tidak dikenali.'
            );
    }
}