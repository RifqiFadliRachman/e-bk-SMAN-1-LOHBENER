<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BKController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes - E-BK SMAN 1 Lohbener
|--------------------------------------------------------------------------
|
| Sistem Informasi Bimbingan dan Konseling Digital
|
| Role:
| - siswa
| - guru_bk
| - kepala
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;

    return match ($role) {

        'siswa' => redirect()->route('dass21.index'),

        'guru_bk' => redirect()->route('counselor.dashboard'),

        'kepala' => redirect()->route('principal.dashboard'),

        default => redirect()->route('login')
            ->with('error', 'Role akun tidak dikenali.'),

    };

})->name('home');


/*
|--------------------------------------------------------------------------
| AUTHENTICATION / LOGIN
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Halaman Login
|--------------------------------------------------------------------------
*/

Route::get('/login', [
    AuthController::class,
    'showLogin'
])->name('login');


/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/

Route::post('/login', [
    AuthController::class,
    'login'
])->name('login.process');


/*
|--------------------------------------------------------------------------
| Halaman Registrasi Siswa
|--------------------------------------------------------------------------
*/

Route::get('/register', [
    AuthController::class,
    'showRegister'
])->name('register');


/*
|--------------------------------------------------------------------------
| Proses Registrasi Siswa
|--------------------------------------------------------------------------
*/

Route::post('/register', [
    AuthController::class,
    'register'
])->name('register.process');


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [
    AuthController::class,
    'logout'
])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| FORCE LOGOUT
|--------------------------------------------------------------------------
|
| Digunakan ketika session/login bermasalah.
|
*/

Route::get('/force-logout', function () {

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect()->route('login');

})->name('force.logout');


/*
|--------------------------------------------------------------------------
| ROUTE UNTUK USER YANG SUDAH LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | PROFIL PENGGUNA
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        AuthController::class,
        'showProfile'
    ])->name('profile');

    Route::put('/profile', [
        AuthController::class,
        'updateProfile'
    ])->name('profile.update');

    Route::put('/profile/password', [
        AuthController::class,
        'updatePassword'
    ])->name('profile.password');



    /*
    |--------------------------------------------------------------------------
    | DASS-21
    |--------------------------------------------------------------------------
    |
    | Screening kesehatan mental siswa.
    |
    */



    /*
    |--------------------------------------------------------------------------
    | Halaman Screening DASS-21
    |--------------------------------------------------------------------------
    */

    Route::get('/dass21', [
        BKController::class,
        'showDass21'
    ])->name('dass21.index');


    /*
    |--------------------------------------------------------------------------
    | Simpan hasil DASS-21
    |--------------------------------------------------------------------------
    */

    Route::post('/dass21/store', [
        BKController::class,
        'storeDass21'
    ])->name('dass21.store');


    /*
    |--------------------------------------------------------------------------
    | BOOKING KONSELING
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Halaman Booking
    |--------------------------------------------------------------------------
    */

    Route::get('/booking', [
        BKController::class,
        'showBooking'
    ])->name('booking.index');


    /*
    |--------------------------------------------------------------------------
    | Siswa Membuat Booking
    |--------------------------------------------------------------------------
    */

    Route::post('/booking/store', [
        BKController::class,
        'storeBooking'
    ])->name('booking.store');


    /*
    |--------------------------------------------------------------------------
    | TERIMA BOOKING
    |--------------------------------------------------------------------------
    |
    | Digunakan oleh Guru BK.
    |
    | Menunggu Approval
    |          ↓
    |       Disetujui
    |
    */

    Route::patch('/booking/{id}/accept', [
        BKController::class,
        'acceptBooking'
    ])->name('booking.accept');


    /*
    |--------------------------------------------------------------------------
    | TOLAK BOOKING
    |--------------------------------------------------------------------------
    |
    | Digunakan oleh Guru BK.
    |
    | Menunggu Approval
    |          ↓
    |        Ditolak
    |
    */

    Route::patch('/booking/{id}/reject', [
        BKController::class,
        'rejectBooking'
    ])->name('booking.reject');


    /*
    |--------------------------------------------------------------------------
    | PENJADWALAN / UBAH JADWAL
    |--------------------------------------------------------------------------
    |
    | Guru BK dapat mengubah:
    |
    | - tanggal_sesi
    | - jam_sesi
    |
    | Status:
    |
    | Penjadwalan
    |
    */

    Route::patch('/booking/{id}/schedule', [
        BKController::class,
        'rescheduleBooking'
    ])->name('booking.schedule');


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS BOOKING
    |--------------------------------------------------------------------------
    |
    | Status yang diperbolehkan:
    |
    | - Disetujui
    | - Ditolak
    | - Penjadwalan
    | - Selesai
    |
    */

    Route::patch('/booking/{id}/status', [
        BKController::class,
        'updateBookingStatus'
    ])->name('booking.status.update');


    /*
    |--------------------------------------------------------------------------
    | ALIAS STATUS BOOKING
    |--------------------------------------------------------------------------
    |
    | Dipertahankan agar Blade versi lama tetap kompatibel.
    |
    */

    Route::patch('/booking/{id}/status-update', [
        BKController::class,
        'updateBookingStatus'
    ])->name('booking.status');

    /*
    |--------------------------------------------------------------------------
    | HAPUS BOOKING KONSELING SELESAI
    |--------------------------------------------------------------------------
    */

    Route::delete('/booking/{id}', [
        BKController::class,
        'destroyBooking'
    ])->name('booking.destroy');


    /*
    |--------------------------------------------------------------------------
    | CHAT PRIVAT
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Halaman Chat
    |--------------------------------------------------------------------------
    */

    Route::get('/chat', [
        BKController::class,
        'showChat'
    ])->name('chat.index');


    /*
    |--------------------------------------------------------------------------
    | Kirim Chat
    |--------------------------------------------------------------------------
    */

    Route::post('/chat/send', [
        BKController::class,
        'sendChat'
    ])->name('chat.send');


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD GURU BK
    |--------------------------------------------------------------------------
    */


    Route::get('/counselor/dashboard', [
        BKController::class,
        'counselorDashboard'
    ])->name('counselor.dashboard');

    /*
    |--------------------------------------------------------------------------
    | PENCATATAN TINDAK LANJUT GURU BK
    |--------------------------------------------------------------------------
    */

    Route::post('/counselor/tindak-lanjut', [
        BKController::class,
        'storeTindakLanjut'
    ])->name('counselor.tindak-lanjut.store');

    Route::put('/counselor/tindak-lanjut/{id}', [
        BKController::class,
        'updateTindakLanjut'
    ])->name('counselor.tindak-lanjut.update');

    Route::patch('/counselor/tindak-lanjut/{id}/status', [
        BKController::class,
        'updateStatusTindakLanjut'
    ])->name('counselor.tindak-lanjut.status');

    Route::delete('/counselor/tindak-lanjut/{id}', [
        BKController::class,
        'destroyTindakLanjut'
    ])->name('counselor.tindak-lanjut.destroy');

    Route::delete('/counselor/dass-result/{id}', [
        BKController::class,
        'destroyDassResult'
    ])->name('counselor.dass-result.destroy');


    /*
    |--------------------------------------------------------------------------
    | AI COUNSELOR ASSISTANT
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Halaman AI Assistant
    |--------------------------------------------------------------------------
    */

    Route::get('/counselor/ai-assistant', [
        BKController::class,
        'showAiAssistant'
    ])->name('counselor.ai-assistant');


    /*
    |--------------------------------------------------------------------------
    | Generate AI Response
    |--------------------------------------------------------------------------
    */

    Route::post('/counselor/ai-assistant/generate', [
        BKController::class,
        'generateAiResponse'
    ])->name('counselor.ai-assistant.generate');


    /*
    |--------------------------------------------------------------------------
    | PROFIL SISWA
    |--------------------------------------------------------------------------
    |
    | Digunakan Guru BK untuk melihat profil siswa.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Lihat Profil Siswa
    |--------------------------------------------------------------------------
    */

    Route::get('/counselor/student/{id}', [
        BKController::class,
        'showStudentProfile'
    ])->name('counselor.student-profile');


    /*
    |--------------------------------------------------------------------------
    | Daftar Siswa (Guru BK)
    |--------------------------------------------------------------------------
    */

    Route::get('/counselor/siswa', [
        BKController::class,
        'counselorStudentList'
    ])->name('counselor.siswa');

    Route::delete('/counselor/siswa/{id}', [
        BKController::class,
        'counselorStudentDestroy'
    ])->name('counselor.siswa.destroy');


    /*
    |--------------------------------------------------------------------------
    | Update Profil Siswa
    |--------------------------------------------------------------------------
    */

    Route::put('/counselor/student/{id}', [
        BKController::class,
        'updateStudentProfile'
    ])->name('counselor.student-profile.update');


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD KEPALA SEKOLAH
    |--------------------------------------------------------------------------
    |
    | Kepala Sekolah dapat melihat:
    |
    | - Total siswa
    | - Total screening DASS-21
    | - Total sesi konseling selesai
    | - Distribusi tingkat risiko
    | - Jumlah siswa berdasarkan kelas
    | - Jumlah siswa berisiko tinggi berdasarkan kelas
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Dashboard Kepala Sekolah
    |--------------------------------------------------------------------------
    */

    Route::get('/principal/dashboard', [
        BKController::class,
        'principalDashboard'
    ])->name('principal.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Export Laporan Kepala Sekolah
    |--------------------------------------------------------------------------
    */

    Route::get('/principal/export', [
        BKController::class,
        'exportPrincipalReport'
    ])->name('principal.export');


    /*
    |--------------------------------------------------------------------------
    | Manajemen Guru BK
    |--------------------------------------------------------------------------
    */

    Route::get('/principal/guru-bk', [
        BKController::class,
        'principalGuruBKList'
    ])->name('principal.guru-bk');

    Route::post('/principal/guru-bk', [
        BKController::class,
        'principalGuruBKStore'
    ])->name('principal.guru-bk.store');

    Route::delete('/principal/guru-bk/{id}', [
        BKController::class,
        'principalGuruBKDestroy'
    ])->name('principal.guru-bk.destroy');


    /*
    |--------------------------------------------------------------------------
    | Daftar Siswa (Kepala Sekolah)
    |--------------------------------------------------------------------------
    */

    Route::get('/principal/siswa', [
        BKController::class,
        'principalStudentList'
    ])->name('principal.siswa');

    Route::delete('/principal/siswa/{id}', [
        BKController::class,
        'principalStudentDestroy'
    ])->name('principal.siswa.destroy');


    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI
    |--------------------------------------------------------------------------
    */


    Route::post('/notifications/mark-read', function () {
        session(['notifications_last_read_at' => now()->toDateTimeString()]);
        return back();
    })->name('notifications.markAllRead');


    /*
    |--------------------------------------------------------------------------
    | ADMIN / ARCHITECTURE VIEWER
    |--------------------------------------------------------------------------
    |
    | Untuk melihat struktur Blade/View aplikasi.
    |
    */

    Route::get('/architecture/blade-viewer', [
        BKController::class,
        'showBladeViewer'
    ])->name('admin.blade-viewer');

});


/*
|--------------------------------------------------------------------------
| DEMO SWITCH ROLE
|--------------------------------------------------------------------------
|
| Digunakan hanya untuk testing aplikasi.
|
| Contoh:
|
| /demo/switch-role/1
| /demo/switch-role/2
| /demo/switch-role/3
|
| Berdasarkan data demo:
|
| 1 = Siswa Demo
| 2 = Guru BK Demo
| 3 = Kepala Sekolah Demo
|
|--------------------------------------------------------------------------
|
| JANGAN digunakan pada production.
|
|--------------------------------------------------------------------------
*/

Route::get('/demo/switch-role/{id}', function ($id) {

    $user = \App\Models\User::find($id);

    if (!$user) {

        return redirect()
            ->route('login')
            ->with(
                'error',
                'Akun demo tidak ditemukan.'
            );
    }

    Auth::login($user);

    request()->session()->regenerate();

    return redirect()->route('home');

})->name('demo.switch-role');


/*
|--------------------------------------------------------------------------
| FALLBACK 404
|--------------------------------------------------------------------------
|
| Jika route tidak ditemukan, kembali ke halaman login.
|
|--------------------------------------------------------------------------
*/

Route::fallback(function () {

    return redirect()
        ->route('home')
        ->with(
            'error',
            'Halaman yang Anda cari tidak ditemukan.'
        );

});