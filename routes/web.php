<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BKController;

/*
|--------------------------------------------------------------------------
| Web Routes - E-BK SMAN 1 Lohbener (Laravel Blade Version)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dass21.index');
});

// Screening DASS-21 Routes
Route::get('/dass21', [BKController::class, 'showDass21'])->name('dass21.index');
Route::post('/dass21/store', [BKController::class, 'storeDass21'])->name('dass21.store');

// Booking Konseling Routes
Route::get('/booking', [BKController::class, 'showBooking'])->name('booking.index');
Route::post('/booking/store', [BKController::class, 'storeBooking'])->name('booking.store');

// Chat Privat Terenkripsi AES-256 Routes
Route::get('/chat', [BKController::class, 'showChat'])->name('chat.index');
Route::post('/chat/send', [BKController::class, 'sendChat'])->name('chat.send');

// Counselor BK Dashboard Routes
Route::get('/counselor/dashboard', [BKController::class, 'counselorDashboard'])->name('counselor.dashboard');

// AI Counselor Assistant Routes
Route::get('/counselor/ai-assistant', [BKController::class, 'showAiAssistant'])->name('counselor.ai-assistant');
Route::post('/counselor/ai-assistant/generate', [BKController::class, 'generateAiResponse'])->name('counselor.ai-assistant.generate');

// Student Profile (Counselor view & edit)
Route::get('/counselor/student/{id}', [BKController::class, 'showStudentProfile'])->name('counselor.student-profile');
Route::put('/counselor/student/{id}', [BKController::class, 'updateStudentProfile'])->name('counselor.student-profile.update');

// Principal Dashboard Routes
Route::get('/principal/dashboard', [BKController::class, 'principalDashboard'])->name('principal.dashboard');
Route::get('/principal/export', [BKController::class, 'exportPrincipalReport'])->name('principal.export');

// Admin / Architecture Viewer
Route::get('/architecture/blade-viewer', [BKController::class, 'showBladeViewer'])->name('admin.blade-viewer');

// Dummy routes for Notifikasi & Role Switcher
Route::post('/notifications/mark-read', function() { return back(); })->name('notifications.markAllRead');
Route::get('/demo/switch-role/{id}', function($id) { 
    Auth::loginUsingId($id); 
    return redirect('/'); 
})->name('demo.switch-role');
