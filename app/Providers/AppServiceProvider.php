<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'partials.notifications-drawer'], function ($view) {
            if (!Auth::check()) {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotificationsCount' => 0,
                ]);
                return;
            }

            try {
                $user = Auth::user();
                $lastReadAt = session('notifications_last_read_at');
                $notifications = collect();

                if ($user->role === 'guru_bk') {
                    // 1. Booking konseling yang menunggu approval
                    $pendingBookings = DB::table('bookings')
                        ->join('users', 'bookings.student_id', '=', 'users.id')
                        ->where('bookings.counselor_id', $user->id)
                        ->where('bookings.status', 'Menunggu Approval')
                        ->select('bookings.*', 'users.name as student_name', 'users.kelas')
                        ->orderByDesc('bookings.created_at')
                        ->limit(5)
                        ->get();

                    foreach ($pendingBookings as $b) {
                        $notifTime = Carbon::parse($b->created_at);
                        $notifications->push((object)[
                            'id' => 'booking_' . $b->id,
                            'type' => 'booking',
                            'badge_label' => 'Booking Baru',
                            'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'border_color' => 'border-l-blue-500',
                            'title' => 'Pengajuan Konseling Baru',
                            'message' => $b->student_name . ' (' . ($b->kelas ?? 'Siswa') . ') mengajukan konseling pada ' . Carbon::parse($b->tanggal_sesi)->translatedFormat('d M Y') . ' pk ' . substr($b->jam_sesi, 0, 5),
                            'created_at' => $notifTime,
                            'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                            'link' => route('booking.index'),
                        ]);
                    }

                    // 2. Chat masuk dari siswa
                    $incomingStudentChats = DB::table('chat_messages')
                        ->join('users', 'chat_messages.sender_id', '=', 'users.id')
                        ->where('chat_messages.receiver_id', $user->id)
                        ->where('users.role', 'siswa')
                        ->select('chat_messages.*', 'users.name as student_name', 'users.kelas')
                        ->orderByDesc('chat_messages.created_at')
                        ->limit(5)
                        ->get();

                    foreach ($incomingStudentChats as $chat) {
                        $notifTime = Carbon::parse($chat->created_at);
                        $snippet = 'Pesan baru diterima';
                        if (!empty($chat->encrypted_payload)) {
                            try {
                                $decrypted = Crypt::decryptString($chat->encrypted_payload);
                                $snippet = Str::limit($decrypted, 50);
                            } catch (\Throwable $e) {
                                $snippet = 'Pesan chat siswa';
                            }
                        }

                        $notifications->push((object)[
                            'id' => 'chat_' . $chat->id,
                            'type' => 'chat',
                            'badge_label' => 'Chat Siswa',
                            'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'border_color' => 'border-l-purple-500',
                            'title' => 'Pesan dari ' . $chat->student_name,
                            'message' => '"' . $snippet . '"',
                            'created_at' => $notifTime,
                            'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                            'link' => route('chat.index', ['recipient_id' => $chat->sender_id]),
                        ]);
                    }

                    // 3. Siswa risiko tinggi skrining I-DASS-Y
                    $highRiskScreenings = DB::table('dass_results')
                        ->join('users', 'dass_results.student_id', '=', 'users.id')
                        ->where(function ($q) {
                            $q->where('depression_score', '>=', 21)
                              ->orWhere('anxiety_score', '>=', 15)
                              ->orWhere('stress_score', '>=', 26);
                        })
                        ->select('dass_results.*', 'users.name as student_name', 'users.kelas')
                        ->orderByDesc('dass_results.created_at')
                        ->limit(5)
                        ->get();

                    foreach ($highRiskScreenings as $s) {
                        $notifTime = Carbon::parse($s->created_at);
                        $notifications->push((object)[
                            'id' => 'screening_' . $s->id,
                            'type' => 'urgent',
                            'badge_label' => 'Risiko Tinggi',
                            'badge_color' => 'bg-red-100 text-red-800 border-red-200',
                            'border_color' => 'border-l-red-500',
                            'title' => 'Skrining Risiko Tinggi',
                            'message' => $s->student_name . ' (' . ($s->kelas ?? 'Siswa') . ') terindikasi risiko tinggi pada I-DASS-Y.',
                            'created_at' => $notifTime,
                            'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                            'link' => route('counselor.dashboard'),
                        ]);
                    }
                } elseif ($user->role === 'siswa') {
                    // 1. NOTIFIKASI CHAT DARI GURU BK
                    $counselorChats = DB::table('chat_messages')
                        ->join('users', 'chat_messages.sender_id', '=', 'users.id')
                        ->where('chat_messages.receiver_id', $user->id)
                        ->where('users.role', 'guru_bk')
                        ->select('chat_messages.*', 'users.name as counselor_name')
                        ->orderByDesc('chat_messages.created_at')
                        ->limit(5)
                        ->get();

                    foreach ($counselorChats as $chat) {
                        $notifTime = Carbon::parse($chat->created_at);
                        $snippet = 'Pesan baru diterima';
                        if (!empty($chat->encrypted_payload)) {
                            try {
                                $decrypted = Crypt::decryptString($chat->encrypted_payload);
                                $snippet = Str::limit($decrypted, 50);
                            } catch (\Throwable $e) {
                                $snippet = 'Pesan dari konselor BK';
                            }
                        }

                        $notifications->push((object)[
                            'id' => 'chat_' . $chat->id,
                            'type' => 'chat',
                            'badge_label' => 'Chat Guru BK',
                            'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'border_color' => 'border-l-purple-500',
                            'title' => 'Chat dari ' . $chat->counselor_name,
                            'message' => '"' . $snippet . '"',
                            'created_at' => $notifTime,
                            'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                            'link' => route('chat.index', ['recipient_id' => $chat->sender_id]),
                        ]);
                    }

                    // 2. NOTIFIKASI PENJADWALAN & PERUBAHAN JADWAL KONSELING
                    $myBookings = DB::table('bookings')
                        ->join('users', 'bookings.counselor_id', '=', 'users.id')
                        ->where('bookings.student_id', $user->id)
                        ->select('bookings.*', 'users.name as counselor_name')
                        ->orderByDesc('bookings.updated_at')
                        ->limit(7)
                        ->get();

                    foreach ($myBookings as $b) {
                        $notifTime = Carbon::parse($b->updated_at ?? $b->created_at);
                        $formattedDate = Carbon::parse($b->tanggal_sesi)->translatedFormat('d M Y');
                        $formattedTime = substr($b->jam_sesi, 0, 5);

                        // PERBEDAAN JELAS: PERUBAHAN JADWAL vs JADWAL DISETUJUI
                        if ($b->status === 'Penjadwalan') {
                            // >>> KATEGORI 1: PERUBAHAN JADWAL KONSELING (Kuning/Oranye) <<<
                            $notifications->push((object)[
                                'id' => 'booking_reschedule_' . $b->id,
                                'type' => 'reschedule',
                                'badge_label' => 'Perubahan Jadwal',
                                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 font-bold',
                                'border_color' => 'border-l-amber-500',
                                'title' => 'Perubahan Jadwal Konseling',
                                'message' => 'Guru BK ' . $b->counselor_name . ' telah mengubah jadwal sesi konseling Anda menjadi ' . $formattedDate . ' pukul ' . $formattedTime . ' WIB.',
                                'created_at' => $notifTime,
                                'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                                'link' => route('booking.index'),
                            ]);
                        } elseif ($b->status === 'Diterima') {
                            // >>> KATEGORI 2: JADWAL KONSELING DISETUJUI (Hijau) <<<
                            $notifications->push((object)[
                                'id' => 'booking_accepted_' . $b->id,
                                'type' => 'approved',
                                'badge_label' => 'Jadwal Disetujui',
                                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
                                'border_color' => 'border-l-emerald-500',
                                'title' => 'Jadwal Konseling Disetujui',
                                'message' => 'Pengajuan konseling Anda telah disetujui oleh ' . $b->counselor_name . ' untuk ' . $formattedDate . ' pukul ' . $formattedTime . ' WIB.',
                                'created_at' => $notifTime,
                                'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                                'link' => route('booking.index'),
                            ]);
                        } elseif ($b->status === 'Menunggu Approval') {
                            // KATEGORI: PENGAJUAN BARU MENUNGGU APPROVAL
                            $notifications->push((object)[
                                'id' => 'booking_pending_' . $b->id,
                                'type' => 'booking',
                                'badge_label' => 'Menunggu Respon',
                                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'border_color' => 'border-l-blue-400',
                                'title' => 'Pengajuan Konseling Terkirim',
                                'message' => 'Pengajuan konseling tanggal ' . $formattedDate . ' pk ' . $formattedTime . ' sedang menunggu konfirmasi dari ' . $b->counselor_name . '.',
                                'created_at' => $notifTime,
                                'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                                'link' => route('booking.index'),
                            ]);
                        } elseif ($b->status === 'Ditolak') {
                            // KATEGORI: PENGAJUAN DITOLAK
                            $notifications->push((object)[
                                'id' => 'booking_rejected_' . $b->id,
                                'type' => 'urgent',
                                'badge_label' => 'Pengajuan Ditolak',
                                'badge_color' => 'bg-red-100 text-red-800 border-red-200',
                                'border_color' => 'border-l-red-500',
                                'title' => 'Jadwal Konseling Ditolak',
                                'message' => 'Pengajuan konseling tanggal ' . $formattedDate . ' belum dapat disetujui oleh ' . $b->counselor_name . '.',
                                'created_at' => $notifTime,
                                'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                                'link' => route('booking.index'),
                            ]);
                        } elseif ($b->status === 'Selesai') {
                            // KATEGORI: SESI SELESAI
                            $notifications->push((object)[
                                'id' => 'booking_done_' . $b->id,
                                'type' => 'done',
                                'badge_label' => 'Sesi Selesai',
                                'badge_color' => 'bg-[#E8F0E7] text-[#5A6D58] border-[#8BA888]/40',
                                'border_color' => 'border-l-[#5A6D58]',
                                'title' => 'Sesi Konseling Selesai',
                                'message' => 'Sesi konseling bersama ' . $b->counselor_name . ' pada ' . $formattedDate . ' telah selesai dilaksanakan.',
                                'created_at' => $notifTime,
                                'is_read' => $lastReadAt && $notifTime->lte(Carbon::parse($lastReadAt)),
                                'link' => route('booking.index'),
                            ]);
                        }
                    }
                } elseif ($user->role === 'kepala_sekolah') {
                    $highRiskCount = DB::table('dass_results')
                        ->where(function ($q) {
                            $q->where('depression_score', '>=', 21)
                              ->orWhere('anxiety_score', '>=', 15)
                              ->orWhere('stress_score', '>=', 26);
                        })
                        ->count();

                    if ($highRiskCount > 0) {
                        $notifications->push((object)[
                            'id' => 'kepsek_high_risk',
                            'type' => 'urgent',
                            'badge_label' => 'Perhatian Khusus',
                            'badge_color' => 'bg-red-100 text-red-800 border-red-200',
                            'border_color' => 'border-l-red-500',
                            'title' => 'Monitoring Siswa Risiko Tinggi',
                            'message' => 'Terdapat ' . $highRiskCount . ' hasil screening I-DASS-Y dengan kategori risiko tinggi.',
                            'created_at' => now(),
                            'is_read' => $lastReadAt ? true : false,
                            'link' => route('principal.dashboard'),
                        ]);
                    }
                }

                $notifications = $notifications->sortByDesc('created_at')->values();
                $unreadCount = $notifications->where('is_read', false)->count();

                $view->with([
                    'notifications' => $notifications,
                    'unreadNotificationsCount' => $unreadCount,
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotificationsCount' => 0,
                ]);
            }
        });
    }
}
