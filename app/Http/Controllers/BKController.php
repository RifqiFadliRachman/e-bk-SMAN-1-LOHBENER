<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BKController extends Controller
{
    /**
     * Tampilkan Halaman Utama / Screening DASS-21
     */
    public function showDass21()
    {
        $questions = DB::table('dass_questions')->get();
        return view('dass21.index', compact('questions'));
    }

    /**
     * Simpan Hasil Jawaban Kuesioner DASS-21
     */
    public function storeDass21(Request $request)
    {
        $answers = $request->input('answers', []);

        $depressionScore = 0;
        $anxietyScore = 0;
        $stressScore = 0;

        foreach ($answers as $qId => $val) {

            if (in_array($qId, [3,5,10,13,16,17,21])) {

                $depressionScore += ($val * 2);

            } elseif (in_array($qId, [2,4,7,9,15,19,20])) {

                $anxietyScore += ($val * 2);

            } else {

                $stressScore += ($val * 2);

            }
        }

        DB::table('dass_results')->insert([
            'student_id' => Auth::id() ?? 1,
            'depression_score' => $depressionScore,
            'anxiety_score' => $anxietyScore,
            'stress_score' => $stressScore,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('dass21.index')
            ->with('success', 'Hasil Screening DASS-21 berhasil disimpan!');
    }

    /**
     * Halaman Booking
     */
    public function showBooking()
    {
        $bookings = DB::table('bookings')
            ->join('users as student', 'bookings.student_id', '=', 'student.id')
            ->join('users as counselor', 'bookings.counselor_id', '=', 'counselor.id')
            ->select(
                'bookings.*',
                'student.name as student_name',
                'student.kelas',
                'counselor.name as counselor_name'
            )
            ->get();

        $counselors = DB::table('users')->where('role', 'guru_bk')->get();

        return view('booking.index', compact('bookings', 'counselors'));
    }

    /**
     * Simpan Booking
     */
    public function storeBooking(Request $request)
    {
        DB::table('bookings')->insert([
            'student_id' => Auth::id() ?? 1,
            'counselor_id' => $request->counselor_id,
            'tanggal_sesi' => $request->tanggal_sesi,
            'jam_sesi' => $request->jam_sesi,
            'jenis_sesi' => $request->jenis_sesi,
            'topik' => $request->topik,
            'status' => 'Menunggu Approval',
            'created_at' => now(),
        ]);

        return redirect()
            ->route('booking.index')
            ->with('success', 'Booking berhasil dibuat.');
    }

    /**
     * Chat
     */
    public function showChat(Request $request)
    {
        $selectedRecipientId = $request->get('recipient_id', 2);

        $contacts = DB::table('users')
            ->where('id', '!=', Auth::id() ?? 1)
            ->get();

        $activeRecipient = DB::table('users')
            ->where('id', $selectedRecipientId)
            ->first();

        $messages = DB::table('chat_messages')
            ->where(function ($q) use ($selectedRecipientId) {
                $q->where('sender_id', Auth::id() ?? 1)
                  ->where('receiver_id', $selectedRecipientId);
            })
            ->orWhere(function ($q) use ($selectedRecipientId) {
                $q->where('sender_id', $selectedRecipientId)
                  ->where('receiver_id', Auth::id() ?? 1);
            })
            ->orderBy('created_at')
            ->get();

        return view('chat.index', compact(
            'contacts',
            'activeRecipient',
            'messages',
            'selectedRecipientId'
        ));
    }

    /**
     * Kirim Chat
     */
    public function sendChat(Request $request)
    {
        DB::table('chat_messages')->insert([
            'sender_id' => Auth::id() ?? 1,
            'receiver_id' => $request->receiver_id,
            'encrypted_payload' => Crypt::encryptString($request->message),
            'created_at' => now(),
        ]);

        return redirect()->route('chat.index', [
            'recipient_id' => $request->receiver_id
        ]);
    }

    /**
     * Dashboard Guru BK
     */
    public function counselorDashboard()
    {
        $totalStudents = DB::table('users')->where('role', 'siswa')->count();
        $totalAssessments = DB::table('dass_results')->count();
        $pendingBookingsCount = DB::table('bookings')->where('status', 'Menunggu Approval')->count();

        $studentsResults = DB::table('dass_results')
            ->join('users', 'dass_results.student_id', '=', 'users.id')
            ->select(
                'users.name as student_name',
                'users.kelas',
                'dass_results.*'
            )
            ->get();

        $highRiskStudentsCount = 0;

        foreach ($studentsResults as $row) {

            // DEPRESI
            if ($row->depression_score <= 9) {
                $row->depression_category = 'Normal';
            } elseif ($row->depression_score <= 13) {
                $row->depression_category = 'Ringan';
            } elseif ($row->depression_score <= 20) {
                $row->depression_category = 'Sedang';
            } elseif ($row->depression_score <= 27) {
                $row->depression_category = 'Parah';
            } else {
                $row->depression_category = 'Sangat Parah';
            }

            // ANXIETY
            if ($row->anxiety_score <= 7) {
                $row->anxiety_category = 'Normal';
            } elseif ($row->anxiety_score <= 9) {
                $row->anxiety_category = 'Ringan';
            } elseif ($row->anxiety_score <= 14) {
                $row->anxiety_category = 'Sedang';
            } elseif ($row->anxiety_score <= 19) {
                $row->anxiety_category = 'Parah';
            } else {
                $row->anxiety_category = 'Sangat Parah';
            }

            // STRESS
            if ($row->stress_score <= 14) {
                $row->stress_category = 'Normal';
            } elseif ($row->stress_score <= 18) {
                $row->stress_category = 'Ringan';
            } elseif ($row->stress_score <= 25) {
                $row->stress_category = 'Sedang';
            } elseif ($row->stress_score <= 33) {
                $row->stress_category = 'Parah';
            } else {
                $row->stress_category = 'Sangat Parah';
            }

            // OVERALL RISK
            $priority = [
                'Normal' => 1,
                'Ringan' => 2,
                'Sedang' => 3,
                'Parah' => 4,
                'Sangat Parah' => 5,
            ];

            $highest = max(
                $priority[$row->depression_category],
                $priority[$row->anxiety_category],
                $priority[$row->stress_category]
            );

            $row->overall_risk = array_search($highest, $priority);

            if (in_array($row->overall_risk, ['Parah', 'Sangat Parah'])) {
                $highRiskStudentsCount++;
            }
        }

        return view('counselor.dashboard', compact(
            'studentsResults',
            'totalStudents',
            'totalAssessments',
            'pendingBookingsCount',
            'highRiskStudentsCount'
        ));
    }

    /**
     * Halaman AI Counselor Assistant
     */
    public function showAiAssistant()
    {
        $userRole = Auth::check() && Auth::user()->role === 'guru_bk' ? 'guru_bk' : 'siswa';
        $isCounselor = $userRole === 'guru_bk';

        // Ambil skor DASS-21 terbaru milik user (atau null jika belum ada)
        $latestDass = DB::table('dass_results')
            ->where('student_id', Auth::id() ?? 1)
            ->orderByDesc('created_at')
            ->first();

        $dassScores = $latestDass
            ? [
                'depression' => $latestDass->depression_score,
                'anxiety'    => $latestDass->anxiety_score,
                'stress'     => $latestDass->stress_score,
            ]
            : null;

        return view('counselor.ai-assistant', compact('isCounselor', 'userRole', 'dassScores'));
    }

    /**
     * Generate AI Response (POST API — server-side proxy ke Gemini)
     */
    public function generateAiResponse(Request $request)
    {
        $request->validate([
            'userPrompt' => 'required|string|max:2000',
            'role'       => 'required|string|in:guru_bk,siswa',
        ]);

        $userPrompt = $request->input('userPrompt');
        $role        = $request->input('role');
        $dassScores  = $request->input('dassScores');

        // Bangun system instruction sesuai peran
        if ($role === 'guru_bk') {
            $systemInstruction = "Kamu adalah AI Advisor untuk Guru Bimbingan Konseling (BK) di SMAN 1 Lohbener. "
                . "Bantu merumuskan strategi pendekatan intervensi psikopedagogis berdasarkan skor DASS-21 dan situasi siswa. "
                . "Berikan saran profesional, langkah-langkah intervensi, dan rekomendasi tindak lanjut. "
                . "Jawab dalam Bahasa Indonesia yang formal dan profesional.";
        } else {
            $systemInstruction = "Kamu adalah teman refleksi diri AI yang empatik untuk siswa di SMAN 1 Lohbener. "
                . "Bantu siswa mengekspresikan pikiran, mengurai emosi, dan menyajikan latihan relaksasi mandiri. "
                . "Bersikap hangat, suportif, dan tidak menghakimi. "
                . "Jawab dalam Bahasa Indonesia yang ramah dan mudah dipahami.";
        }

        // Sertakan skor DASS jika tersedia
        $dassContext = '';
        if ($dassScores) {
            $dassContext = "\n\n[Konteks Skor DASS-21 terbaru: "
                . "Depresi={$dassScores['depression']}, "
                . "Kecemasan={$dassScores['anxiety']}, "
                . "Stres={$dassScores['stress']}]";
        }

        $fullPrompt = $systemInstruction . $dassContext . "\n\nPertanyaan/Skenario:\n" . $userPrompt;

        try {
            $apiKey = env('GEMINI_API_KEY', '');

            if (empty($apiKey)) {
                return response()->json([
                    'aiResponse' => 'API Key Gemini belum dikonfigurasi. Silakan tambahkan GEMINI_API_KEY di file .env.',
                ], 200);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $fullPrompt],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respon dari AI.';

                return response()->json(['aiResponse' => $aiText]);
            }

            return response()->json([
                'aiResponse' => 'Gagal mendapatkan respon dari Gemini API. Status: ' . $response->status(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'aiResponse' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dashboard Kepala Sekolah (Principal)
     */
    public function principalDashboard()
    {
        $totalStudents = DB::table('users')->where('role', 'siswa')->count();
        $totalAssessments = DB::table('dass_results')->count();
        $totalSessionsCompleted = DB::table('bookings')->where('status', 'Selesai')->count();

        // Data dummy distribusi tingkat keparahan untuk Chart
        $severityDistribution = [28, 14, 8, 4, 1]; // Normal, Ringan, Sedang, Parah, Sangat Parah
        $classLabels = ['X MIPA 1','X IPS 1','XI MIPA 1','XI MIPA 2','XI IPS 1','XII MIPA 1','XII MIPA 3'];
        $classStudentCounts = [32, 28, 30, 31, 29, 33, 30];
        $classHighRiskCounts = [2, 1, 3, 2, 4, 1, 5];

        return view('principal.dashboard', compact(
            'totalStudents', 
            'totalAssessments', 
            'totalSessionsCompleted',
            'severityDistribution',
            'classLabels',
            'classStudentCounts',
            'classHighRiskCounts'
        ));
    }

    /**
     * Cetak Laporan Kepala Sekolah
     */
    public function exportPrincipalReport()
    {
        return redirect()->route('principal.dashboard')->with('success', 'Laporan berhasil diunduh (Simulasi).');
    }

    /**
     * Profil Siswa (Untuk Konselor)
     */
    public function showStudentProfile($id)
    {
        $student = DB::table('users')->find($id);
        
        if (!$student || $student->role !== 'siswa') {
            abort(404, 'Siswa tidak ditemukan.');
        }

        $latestDass = DB::table('dass_results')
            ->where('student_id', $id)
            ->orderByDesc('created_at')
            ->first();

        // Enkripsi Dummy untuk catatan medis/psikologis
        $rawNote = $student->catatan_sensitif ?? '';
        $decryptedNote = $rawNote ? Crypt::decryptString($rawNote) : null;
        $encryptedNote = $rawNote ?: 'Belum ada catatan khusus.';
        
        // Masking kontak
        $kontak = $student->kontak_darurat ?? '';
        $maskedKontak = strlen($kontak) > 4 ? substr_replace($kontak, str_repeat('*', strlen($kontak) - 4), 0, -4) : '-';

        return view('counselor.student-profile', compact('student', 'latestDass', 'decryptedNote', 'encryptedNote', 'maskedKontak'));
    }

    /**
     * Update Profil & Catatan Siswa
     */
    public function updateStudentProfile(Request $request, $id)
    {
        $encryptedNote = $request->catatan_sensitif ? Crypt::encryptString($request->catatan_sensitif) : null;

        DB::table('users')->where('id', $id)->update([
            'alamat' => $request->alamat,
            'nama_orang_tua' => $request->nama_orang_tua,
            'kontak_darurat' => $request->kontak_darurat,
            'catatan_sensitif' => $encryptedNote
        ]);

        return redirect()->route('counselor.student-profile', $id)->with('success', 'Profil dan Catatan berhasil diperbarui dan dienkripsi AES-256.');
    }

    /**
     * Kode Sumber Laravel Blade Architecture Viewer
     */
    public function showBladeViewer()
    {
        $bladeFiles = [
            'layout' => [
                'title' => 'Layout Utama (Blade Template)',
                'path' => 'resources/views/layouts/app.blade.php',
                'code' => file_get_contents(resource_path('views/layouts/app.blade.php')),
            ],
            'dass21' => [
                'title' => 'DASS-21 View (Blade Template)',
                'path' => 'resources/views/dass21/index.blade.php',
                'code' => file_get_contents(resource_path('views/dass21/index.blade.php')),
            ],
            'booking' => [
                'title' => 'Booking Konseling View (Blade Template)',
                'path' => 'resources/views/booking/index.blade.php',
                'code' => file_get_contents(resource_path('views/booking/index.blade.php')),
            ],
            'chat' => [
                'title' => 'Chat Privat E2EE View',
                'path' => 'resources/views/chat/index.blade.php',
                'code' => file_get_contents(resource_path('views/chat/index.blade.php')),
            ],
            'controller' => [
                'title' => 'Laravel BKController PHP',
                'path' => 'app/Http/Controllers/BKController.php',
                'code' => file_get_contents(app_path('Http/Controllers/BKController.php')),
            ]
        ];

        return view('admin.blade-viewer', compact('bladeFiles'));
    }
}