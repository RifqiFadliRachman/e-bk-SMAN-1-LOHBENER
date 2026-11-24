<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BKController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | I-DASS-Y
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan halaman I-DASS-Y.
     */
    public function showDass21()
    {
        $questions = DB::table('dass_questions')
            ->orderBy('question_number')
            ->get();

        $latestResult = null;
        $screeningHistory = collect();

        if (Auth::check() && Auth::user()->role === 'siswa') {

            /*
            |----------------------------------------------------------
            | Hasil screening terakhir
            |----------------------------------------------------------
            */

            $latestResult = DB::table('dass_results')
                ->where('student_id', Auth::id())
                ->orderByDesc('created_at')
                ->first();

            if ($latestResult) {
                $latestResult->depression_category =
                    $this->getDepressionCategory(
                        (int) $latestResult->depression_score
                    );

                $latestResult->anxiety_category =
                    $this->getAnxietyCategory(
                        (int) $latestResult->anxiety_score
                    );

                $latestResult->stress_category =
                    $this->getStressCategory(
                        (int) $latestResult->stress_score
                    );

                $latestResult->overall_risk =
                    $this->getOverallRisk(
                        $latestResult->depression_category,
                        $latestResult->anxiety_category,
                        $latestResult->stress_category
                    );
            }

            /*
            |----------------------------------------------------------
            | Riwayat seluruh screening siswa
            |----------------------------------------------------------
            */

            $screeningHistory = DB::table('dass_results')
                ->where('student_id', Auth::id())
                ->orderByDesc('created_at')
                ->get();

            foreach ($screeningHistory as $row) {
                $row->depression_category =
                    $this->getDepressionCategory(
                        (int) $row->depression_score
                    );

                $row->anxiety_category =
                    $this->getAnxietyCategory(
                        (int) $row->anxiety_score
                    );

                $row->stress_category =
                    $this->getStressCategory(
                        (int) $row->stress_score
                    );

                $row->overall_risk =
                    $this->getOverallRisk(
                        $row->depression_category,
                        $row->anxiety_category,
                        $row->stress_category
                    );
            }
        }

        return view(
            'dass21.index',
            compact(
                'questions',
                'latestResult',
                'screeningHistory'
            )
        );
    }


    /**
     * Simpan hasil I-DASS-Y.
     */
    public function storeDass21(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login sebagai siswa terlebih dahulu.'
                );
        }

        if (Auth::user()->role !== 'siswa') {
            return redirect('/')
                ->with(
                    'error',
                    'Hanya siswa yang dapat mengisi screening DASS-21.'
                );
        }

        $request->validate([
            'answers' => [
                'required',
                'array'
            ],

            'answers.*' => [
                'required',
                'integer',
                'between:0,3'
            ],
        ]);

        $answers = $request->input('answers', []);

        $questions = DB::table('dass_questions')
            ->orderBy('question_number')
            ->get();

        if ($questions->count() !== 21) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Data pertanyaan DASS-21 belum lengkap. Pastikan tersedia 21 pertanyaan.'
                );
        }

        $depressionScore = 0;
        $anxietyScore = 0;
        $stressScore = 0;

        foreach ($questions as $question) {

            $questionNumber =
                (int) $question->question_number;

            /*
            |--------------------------------------------------------------------------
            | Ambil jawaban
            |--------------------------------------------------------------------------
            |
            | Mendukung:
            | answers[id]
            | maupun
            | answers[question_number]
            |
            */

            $value =
                $answers[$question->id]
                ?? $answers[$questionNumber]
                ?? null;

            if ($value === null) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Jawaban pertanyaan nomor {$questionNumber} belum diisi."
                    );
            }

            $value = (int) $value;

            switch ($question->dimension) {

                case 'depression':

                    $depressionScore +=
                        $value * 2;

                    break;


                case 'anxiety':

                    $anxietyScore +=
                        $value * 2;

                    break;


                case 'stress':

                    $stressScore +=
                        $value * 2;

                    break;


                default:

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "Dimensi pertanyaan nomor {$questionNumber} tidak valid."
                        );
            }
        }


        DB::table('dass_results')->insert([

            'student_id' =>
                Auth::id(),

            'depression_score' =>
                $depressionScore,

            'anxiety_score' =>
                $anxietyScore,

            'stress_score' =>
                $stressScore,

            'created_at' =>
                now(),

            'updated_at' =>
                now(),
        ]);


        return redirect()
            ->route('dass21.index')
            ->with(
                'success',
                'Hasil Screening I-DASS-Y berhasil disimpan!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BOOKING KONSELING
    |--------------------------------------------------------------------------
    */

    /**
     * Halaman Booking Konseling.
     */
    public function showBooking()
    {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }

        $userId =
            Auth::id();

        $userRole =
            Auth::user()->role;


        $query = DB::table('bookings')
            ->join(
                'users as student',
                'bookings.student_id',
                '=',
                'student.id'
            )
            ->join(
                'users as counselor',
                'bookings.counselor_id',
                '=',
                'counselor.id'
            )
            ->select(
                'bookings.*',
                'student.name as student_name',
                'student.kelas',
                'counselor.name as counselor_name'
            )
            ->orderByDesc(
                'bookings.created_at'
            );


        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */

        if ($userRole === 'siswa') {

            $query->where(
                'bookings.student_id',
                $userId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Guru BK
        |--------------------------------------------------------------------------
        */

        if ($userRole === 'guru_bk') {

            $query->where(
                'bookings.counselor_id',
                $userId
            );
        }


        $bookings =
            $query->get();


        /*
        |--------------------------------------------------------------------------
        | Daftar Guru BK
        |--------------------------------------------------------------------------
        */

        $counselors =
            DB::table('users')
                ->where(
                    'role',
                    'guru_bk'
                )
                ->orderBy('name')
                ->get();


        return view(
            'booking.index',
            compact(
                'bookings',
                'counselors'
            )
        );
    }


    /**
     * Simpan Booking Konseling.
     */
    public function storeBooking(
        Request $request
    ) {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login sebagai siswa terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !== 'siswa'
        ) {
            return back()
                ->with(
                    'error',
                    'Hanya siswa yang dapat membuat booking konseling.'
                );
        }


        $validated =
            $request->validate([

                'counselor_id' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],

                'tanggal_sesi' => [
                    'required',
                    'date',
                ],

                'jam_sesi' => [
                    'required',
                    'date_format:H:i',
                ],

                'jenis_sesi' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'topik' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Pastikan Guru BK valid
        |--------------------------------------------------------------------------
        */

        $counselor =
            DB::table('users')
                ->where(
                    'id',
                    $validated['counselor_id']
                )
                ->where(
                    'role',
                    'guru_bk'
                )
                ->first();


        if (!$counselor) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Guru BK yang dipilih tidak valid.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cek jadwal bentrok
        |--------------------------------------------------------------------------
        */

        $existingBooking =
            DB::table('bookings')
                ->where(
                    'counselor_id',
                    $validated['counselor_id']
                )
                ->where(
                    'tanggal_sesi',
                    $validated['tanggal_sesi']
                )
                ->where(
                    'jam_sesi',
                    $validated['jam_sesi']
                )
                ->whereIn(
                    'status',
                    [
                        'Menunggu Approval',
                        'Diterima',
                        'Penjadwalan',
                    ]
                )
                ->exists();


        if ($existingBooking) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Jadwal tersebut sudah digunakan oleh siswa lain.'
                );
        }


        DB::table('bookings')->insert([

            'student_id' =>
                Auth::id(),

            'counselor_id' =>
                (int) $validated['counselor_id'],

            'tanggal_sesi' =>
                $validated['tanggal_sesi'],

            'jam_sesi' =>
                $validated['jam_sesi'],

            'jenis_sesi' =>
                $validated['jenis_sesi'],

            'topik' =>
                $validated['topik'] ?? null,

            'status' =>
                'Menunggu Approval',

            'created_at' =>
                now(),

            'updated_at' =>
                now(),
        ]);


        return redirect()
            ->route('booking.index')
            ->with(
                'success',
                'Booking berhasil dibuat dan menunggu persetujuan Guru BK.'
            );
    }


    /**
     * Terima Booking.
     */
    public function acceptBooking($id)
    {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !== 'guru_bk'
        ) {
            abort(
                403,
                'Anda tidak memiliki akses untuk menerima booking.'
            );
        }


        $booking =
            DB::table('bookings')
                ->where(
                    'id',
                    $id
                )
                ->where(
                    'counselor_id',
                    Auth::id()
                )
                ->first();


        if (!$booking) {

            return back()
                ->with(
                    'error',
                    'Booking tidak ditemukan atau bukan milik Anda.'
                );
        }


        if (
            $booking->status !==
            'Menunggu Approval'
        ) {

            return back()
                ->with(
                    'error',
                    'Booking ini sudah diproses sebelumnya.'
                );
        }


        DB::table('bookings')
            ->where(
                'id',
                $id
            )
            ->update([

                'status' =>
                    'Diterima',

                'updated_at' =>
                    now(),
            ]);


        return back()
            ->with(
                'success',
                'Booking berhasil diterima.'
            );
    }


    /**
     * Tolak Booking.
     */
    public function rejectBooking($id)
    {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !== 'guru_bk'
        ) {
            abort(
                403,
                'Anda tidak memiliki akses untuk menolak booking.'
            );
        }


        $booking =
            DB::table('bookings')
                ->where(
                    'id',
                    $id
                )
                ->where(
                    'counselor_id',
                    Auth::id()
                )
                ->first();


        if (!$booking) {

            return back()
                ->with(
                    'error',
                    'Booking tidak ditemukan atau bukan milik Anda.'
                );
        }


        if (
            $booking->status !==
            'Menunggu Approval'
        ) {

            return back()
                ->with(
                    'error',
                    'Booking ini sudah diproses sebelumnya.'
                );
        }


        DB::table('bookings')
            ->where(
                'id',
                $id
            )
            ->update([

                'status' =>
                    'Ditolak',

                'updated_at' =>
                    now(),
            ]);


        return back()
            ->with(
                'success',
                'Booking berhasil ditolak.'
            );
    }


    /**
     * Penjadwalan / Ubah Jadwal.
     */
    public function rescheduleBooking(
        Request $request,
        $id
    ) {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !== 'guru_bk'
        ) {
            abort(
                403,
                'Anda tidak memiliki akses untuk menjadwalkan booking.'
            );
        }


        $validated =
            $request->validate([

                'tanggal_sesi' => [
                    'required',
                    'date',
                ],

                'jam_sesi' => [
                    'required',
                    'date_format:H:i',
                ],
            ]);


        $booking =
            DB::table('bookings')
                ->where(
                    'id',
                    $id
                )
                ->where(
                    'counselor_id',
                    Auth::id()
                )
                ->first();


        if (!$booking) {

            return back()
                ->with(
                    'error',
                    'Booking tidak ditemukan atau bukan milik Anda.'
                );
        }


        if (
            in_array(
                $booking->status,
                [
                    'Ditolak',
                    'Selesai',
                ],
                true
            )
        ) {

            return back()
                ->with(
                    'error',
                    'Booking dengan status tersebut tidak dapat dijadwalkan ulang.'
                );
        }


        $existingBooking =
            DB::table('bookings')
                ->where(
                    'counselor_id',
                    Auth::id()
                )
                ->where(
                    'tanggal_sesi',
                    $validated['tanggal_sesi']
                )
                ->where(
                    'jam_sesi',
                    $validated['jam_sesi']
                )
                ->where(
                    'id',
                    '!=',
                    $id
                )
                ->whereIn(
                    'status',
                    [
                        'Menunggu Approval',
                        'Diterima',
                        'Penjadwalan',
                    ]
                )
                ->exists();


        if ($existingBooking) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Jadwal baru tersebut sudah digunakan oleh booking lain.'
                );
        }


        DB::table('bookings')
            ->where(
                'id',
                $id
            )
            ->update([

                'tanggal_sesi' =>
                    $validated['tanggal_sesi'],

                'jam_sesi' =>
                    $validated['jam_sesi'],

                'status' =>
                    'Penjadwalan',

                'updated_at' =>
                    now(),
            ]);


        return redirect()
            ->route('booking.index')
            ->with(
                'success',
                'Jadwal konseling berhasil diperbarui dan status menjadi Penjadwalan.'
            );
    }


    /**
     * Update Status Booking.
     */
    public function updateBookingStatus(
        Request $request,
        $id
    ) {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !== 'guru_bk'
        ) {
            abort(
                403,
                'Anda tidak memiliki akses untuk mengubah status booking.'
            );
        }


        $validated =
            $request->validate([

                'status' => [
                    'required',
                    'string',
                    'in:Diterima,Ditolak,Selesai',
                ],
            ]);


        $booking =
            DB::table('bookings')
                ->where(
                    'id',
                    $id
                )
                ->where(
                    'counselor_id',
                    Auth::id()
                )
                ->first();


        if (!$booking) {

            return back()
                ->with(
                    'error',
                    'Booking tidak ditemukan atau bukan milik Anda.'
                );
        }


        if (
            in_array(
                $booking->status,
                [
                    'Ditolak',
                    'Selesai',
                ],
                true
            )
        ) {

            return back()
                ->with(
                    'error',
                    'Booking ini sudah berada pada status final.'
                );
        }


        DB::table('bookings')
            ->where(
                'id',
                $id
            )
            ->update([

                'status' =>
                    $validated['status'],

                'updated_at' =>
                    now(),
            ]);


        $message =
            match ($validated['status']) {

                'Diterima' =>
                    'Booking berhasil diterima.',

                'Ditolak' =>
                    'Booking berhasil ditolak.',

                'Selesai' =>
                    'Booking berhasil ditandai sebagai selesai.',

                default =>
                    'Status booking berhasil diperbarui.',
            };


        return redirect()
            ->route('booking.index')
            ->with(
                'success',
                $message
            );
    }

    /**
     * Hapus Riwayat Sesi Konseling Selesai (Khusus Guru BK).
     */
    public function destroyBooking($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'guru_bk') {
            abort(403, 'Akses terbatas untuk Guru BK.');
        }

        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('counselor_id', Auth::id())
            ->first();

        if (!$booking) {
            return redirect()
                ->route('booking.index')
                ->with('error', 'Data jadwal konseling tidak ditemukan atau bukan milik Anda.');
        }

        if ($booking->status !== 'Selesai') {
            return redirect()
                ->route('booking.index')
                ->with('error', 'Hanya sesi konseling yang sudah selesai yang dapat dihapus.');
        }

        DB::table('bookings')->where('id', $id)->delete();

        return redirect()
            ->route('booking.index')
            ->with('success', 'Riwayat sesi konseling selesai berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | CHAT
    |--------------------------------------------------------------------------
    */

    /**
     * Halaman Chat.
     */
    public function showChat(
        Request $request
    ) {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        $currentUserId =
            Auth::id();


        $selectedRecipientId =
            $request->integer(
                'recipient_id'
            );


        $contactsQuery = DB::table('users')
            ->where('id', '!=', $currentUserId);

        if (Auth::user()->role === 'siswa') {
            $contactsQuery->where('role', 'guru_bk')
                ->whereExists(function ($query) use ($currentUserId) {
                    $query->select(DB::raw(1))
                        ->from('bookings')
                        ->whereColumn('bookings.counselor_id', 'users.id')
                        ->where('bookings.student_id', $currentUserId)
                        ->where('bookings.jenis_sesi', 'Chat Online')
                        ->whereIn('bookings.status', ['Diterima', 'Penjadwalan', 'Selesai']);
                });
        } elseif (Auth::user()->role === 'guru_bk') {
            $contactsQuery->where('role', 'siswa')
                ->whereExists(function ($query) use ($currentUserId) {
                    $query->select(DB::raw(1))
                        ->from('bookings')
                        ->whereColumn('bookings.student_id', 'users.id')
                        ->where('bookings.counselor_id', $currentUserId)
                        ->where('bookings.jenis_sesi', 'Chat Online')
                        ->whereIn('bookings.status', ['Diterima', 'Penjadwalan', 'Selesai']);
                });
        } else {
            $contactsQuery->whereIn('role', ['siswa', 'guru_bk']);
        }

        $contacts = $contactsQuery->orderBy('name')->get();


        if (!$selectedRecipientId) {

            $selectedRecipientId =
                $contacts->first()?->id;
        }


        $activeRecipient =
            null;

        $messages =
            collect();


        if ($selectedRecipientId) {

            // Cek apakah recipient_id valid bersadarkan daftar contacts yang telah difilter
            $activeRecipient = $contacts->firstWhere('id', $selectedRecipientId);


            if ($activeRecipient) {

                $messages =
                    DB::table('chat_messages')
                        ->where(
                            function ($query)
                            use (
                                $currentUserId,
                                $selectedRecipientId
                            ) {

                                $query
                                    ->where(
                                        'sender_id',
                                        $currentUserId
                                    )
                                    ->where(
                                        'receiver_id',
                                        $selectedRecipientId
                                    );
                            }
                        )
                        ->orWhere(
                            function ($query)
                            use (
                                $currentUserId,
                                $selectedRecipientId
                            ) {

                                $query
                                    ->where(
                                        'sender_id',
                                        $selectedRecipientId
                                    )
                                    ->where(
                                        'receiver_id',
                                        $currentUserId
                                    );
                            }
                        )
                        ->orderBy(
                            'created_at'
                        )
                        ->get();


                foreach ($messages as $message) {

                    try {

                        $message->message =
                            Crypt::decryptString(
                                $message->encrypted_payload
                            );

                    } catch (\Throwable $e) {

                        $message->message =
                            '[Pesan tidak dapat dibaca]';
                    }
                }
            }
        }


        return view(
            'chat.index',
            compact(
                'contacts',
                'activeRecipient',
                'messages',
                'selectedRecipientId'
            )
        );
    }


    /**
     * Kirim Chat.
     */
    public function sendChat(
        Request $request
    ) {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        $validated =
            $request->validate([

                'receiver_id' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],

                'message' => [
                    'required',
                    'string',
                    'max:5000',
                ],
            ]);


        if (
            (int) $validated['receiver_id']
            === (int) Auth::id()
        ) {

            return back()
                ->with(
                    'error',
                    'Anda tidak dapat mengirim pesan kepada diri sendiri.'
                );
        }


        $receiver =
            DB::table('users')
                ->where(
                    'id',
                    $validated['receiver_id']
                )
                ->whereIn(
                    'role',
                    [
                        'siswa',
                        'guru_bk',
                    ]
                )
                ->first();


        if (!$receiver) {
            return back()
                ->with(
                    'error',
                    'Penerima pesan tidak valid.'
                );
        }

        // Pengecekan Izin Chat Berdasar Booking Konseling
        $currentUserId = Auth::id();
        $isSiswa = Auth::user()->role === 'siswa';
        $isGuruBK = Auth::user()->role === 'guru_bk';

        if ($isSiswa || $isGuruBK) {
            $studentId = $isSiswa ? $currentUserId : $receiver->id;
            $counselorId = $isGuruBK ? $currentUserId : $receiver->id;

            $hasValidBooking = DB::table('bookings')
                ->where('student_id', $studentId)
                ->where('counselor_id', $counselorId)
                ->where('jenis_sesi', 'Chat Online')
                ->whereIn('status', ['Diterima', 'Penjadwalan', 'Selesai'])
                ->exists();

            if (!$hasValidBooking) {
                return back()->with('error', 'Anda tidak dapat mengirim pesan kepada kontak ini karena belum ada jadwal konseling online yang disetujui.');
            }
        }


        DB::table('chat_messages')->insert([

            'sender_id' =>
                Auth::id(),

            'receiver_id' =>
                (int) $validated['receiver_id'],

            'encrypted_payload' =>
                Crypt::encryptString(
                    $validated['message']
                ),

            'created_at' =>
                now(),
        ]);


        return redirect()
            ->route(
                'chat.index',
                [
                    'recipient_id' =>
                        $validated['receiver_id'],
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD GURU BK
    |--------------------------------------------------------------------------
    */

    /**
     * Dashboard Guru BK.
     */
    public function counselorDashboard()
    {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !== 'guru_bk'
        ) {
            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Guru BK.'
            );
        }


        $totalStudents =
            DB::table('users')
                ->where(
                    'role',
                    'siswa'
                )
                ->count();


        $totalAssessments =
            DB::table('dass_results')
                ->count();


        $pendingBookingsCount =
            DB::table('bookings')
                ->where(
                    'counselor_id',
                    Auth::id()
                )
                ->where(
                    'status',
                    'Menunggu Approval'
                )
                ->count();


        // Ambil semua data tindak lanjut per siswa untuk mencocokkan waktu screening
        $allTindakLanjuts = DB::table('tindak_lanjuts')
            ->select('id', 'student_id', 'tanggal_tindak_lanjut', 'created_at')
            ->get()
            ->groupBy('student_id');

        $studentsResults =
            DB::table('dass_results')
                ->join(
                    'users',
                    'dass_results.student_id',
                    '=',
                    'users.id'
                )
                ->select(
                    'dass_results.*',
                    'users.name as student_name',
                    'users.kelas'
                )
                ->orderByDesc(
                    'dass_results.created_at'
                )
                ->get();

        // Tandai setiap baris screening: HANYA berstatus sudah ditindak lanjuti
        // jika ADA tindak lanjut yang dilakukan pada atau setelah tanggal screening tersebut.
        $studentsWithPendingScreening = [];

        foreach ($studentsResults as $row) {
            $studentTindak = $allTindakLanjuts->get($row->student_id, collect());
            $screeningTime = \Carbon\Carbon::parse($row->created_at);

            $hasFollowUp = $studentTindak->contains(function ($tl) use ($screeningTime) {
                $tlDate = \Carbon\Carbon::parse($tl->tanggal_tindak_lanjut ?? $tl->created_at);
                $tlCreated = \Carbon\Carbon::parse($tl->created_at);
                $threshold = $screeningTime->copy()->subMinute();

                return $tlDate->greaterThanOrEqualTo($threshold)
                    || $tlCreated->greaterThanOrEqualTo($threshold);
            });

            $row->has_tindak_lanjut = $hasFollowUp;

            if (!$hasFollowUp) {
                $studentsWithPendingScreening[$row->student_id] = true;
            }
        }


        $highRiskStudentsCount =
            0;


        foreach (
            $studentsResults as $row
        ) {

            $row->depression_category =
                $this->getDepressionCategory(
                    (int) $row->depression_score
                );


            $row->anxiety_category =
                $this->getAnxietyCategory(
                    (int) $row->anxiety_score
                );


            $row->stress_category =
                $this->getStressCategory(
                    (int) $row->stress_score
                );


            $row->overall_risk =
                $this->getOverallRisk(
                    $row->depression_category,
                    $row->anxiety_category,
                    $row->stress_category
                );


            if (
                in_array(
                    $row->overall_risk,
                    [
                        'Parah',
                        'Sangat Parah',
                    ],
                    true
                )
            ) {

                $highRiskStudentsCount++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PENCATATAN TINDAK LANJUT GURU BK
        |--------------------------------------------------------------------------
        */

        $tindakLanjuts = DB::table('tindak_lanjuts')
            ->join('users as student', 'tindak_lanjuts.student_id', '=', 'student.id')
            ->join('users as counselor', 'tindak_lanjuts.counselor_id', '=', 'counselor.id')
            ->select(
                'tindak_lanjuts.*',
                'student.name as student_name',
                'student.kelas as student_kelas',
                'counselor.name as counselor_name'
            )
            ->orderByDesc('tindak_lanjuts.tanggal_tindak_lanjut')
            ->get();

        foreach ($tindakLanjuts as $row) {
            $row->formatted_tanggal = \Carbon\Carbon::parse($row->tanggal_tindak_lanjut)->translatedFormat('d M Y, H:i');
            $row->decrypted_catatan = null;
            if ($row->catatan) {
                try {
                    $row->decrypted_catatan = Crypt::decryptString($row->catatan);
                } catch (\Throwable $e) {
                    $row->decrypted_catatan = $row->catatan;
                }
            }
        }

        $totalTindakLanjut = $tindakLanjuts->count();
        $tindakLanjutBelum = $tindakLanjuts->where('status', 'Belum ditangani')->count();
        $tindakLanjutProses = $tindakLanjuts->where('status', 'Proses')->count();
        $tindakLanjutSelesai = $tindakLanjuts->where('status', 'Selesai')->count();

        // Siswa yang dikecualikan dari modal adalah siswa yang sudah pernah ditindaklanjuti
        // DAN TIDAK MEMILIKI screening baru yang berstatus belum ditindaklanjuti.
        // Jika siswa melakukan screening baru, mereka akan otomatis muncul kembali di daftar pilihan.
        $fullyResolvedStudentIds = [];
        foreach ($allTindakLanjuts->keys() as $sId) {
            if (!isset($studentsWithPendingScreening[$sId])) {
                $fullyResolvedStudentIds[] = $sId;
            }
        }

        $studentsList = DB::table('users')
            ->where('role', 'siswa')
            ->whereNotIn('id', $fullyResolvedStudentIds)
            ->select('id', 'name', 'kelas')
            ->orderBy('name')
            ->get();

        return view(
            'counselor.dashboard',
            compact(
                'studentsResults',
                'totalStudents',
                'totalAssessments',
                'pendingBookingsCount',
                'highRiskStudentsCount',
                'tindakLanjuts',
                'totalTindakLanjut',
                'tindakLanjutBelum',
                'tindakLanjutProses',
                'tindakLanjutSelesai',
                'studentsList'
            )
        );
    }

    /**
     * Simpan Pencatatan Tindak Lanjut Guru BK.
     */
    public function storeTindakLanjut(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'guru_bk') {
            abort(403, 'Akses terbatas untuk Guru BK.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'tanggal_tindak_lanjut' => ['required'],
            'tindakan' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:Belum ditangani,Proses,Selesai'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        $encryptedCatatan = null;
        if (!empty($validated['catatan'])) {
            $encryptedCatatan = Crypt::encryptString($validated['catatan']);
        }

        $tanggal = date('Y-m-d H:i:s', strtotime($validated['tanggal_tindak_lanjut']));

        DB::table('tindak_lanjuts')->insert([
            'student_id' => (int) $validated['student_id'],
            'counselor_id' => Auth::id(),
            'tanggal_tindak_lanjut' => $tanggal,
            'tindakan' => $validated['tindakan'],
            'status' => $validated['status'],
            'catatan' => $encryptedCatatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('counselor.dashboard')
            ->with('success', 'Catatan tindak lanjut berhasil disimpan.');
    }

    /**
     * Update Pencatatan Tindak Lanjut Guru BK.
     */
    public function updateTindakLanjut(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'guru_bk') {
            abort(403, 'Akses terbatas untuk Guru BK.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'tanggal_tindak_lanjut' => ['required'],
            'tindakan' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:Belum ditangani,Proses,Selesai'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        $record = DB::table('tindak_lanjuts')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Data tindak lanjut tidak ditemukan.');
        }

        $encryptedCatatan = null;
        if (!empty($validated['catatan'])) {
            $encryptedCatatan = Crypt::encryptString($validated['catatan']);
        }

        $tanggal = date('Y-m-d H:i:s', strtotime($validated['tanggal_tindak_lanjut']));

        DB::table('tindak_lanjuts')
            ->where('id', $id)
            ->update([
                'student_id' => (int) $validated['student_id'],
                'tanggal_tindak_lanjut' => $tanggal,
                'tindakan' => $validated['tindakan'],
                'status' => $validated['status'],
                'catatan' => $encryptedCatatan,
                'updated_at' => now(),
            ]);

        return redirect()->route('counselor.dashboard')
            ->with('success', 'Catatan tindak lanjut berhasil diperbarui.');
    }

    /**
     * Update Status Cepat Tindak Lanjut.
     */
    public function updateStatusTindakLanjut(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'guru_bk') {
            abort(403, 'Akses terbatas untuk Guru BK.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Belum ditangani,Proses,Selesai'],
        ]);

        DB::table('tindak_lanjuts')
            ->where('id', $id)
            ->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Status tindak lanjut berhasil diubah menjadi: ' . $validated['status']);
    }

    /**
     * Hapus Pencatatan Tindak Lanjut.
     */
    public function destroyTindakLanjut($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'guru_bk') {
            abort(403, 'Akses terbatas untuk Guru BK.');
        }

        DB::table('tindak_lanjuts')->where('id', $id)->delete();

        return redirect()->route('counselor.dashboard')
            ->with('success', 'Catatan tindak lanjut berhasil dihapus.');
    }

    /**
     * Hapus Hasil Screening I-DASS-Y Siswa (hanya yang sudah ditindak lanjuti).
     */
    public function destroyDassResult($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'guru_bk') {
            abort(403, 'Akses terbatas untuk Guru BK.');
        }

        $result = DB::table('dass_results')->where('id', $id)->first();

        if (!$result) {
            return redirect()->route('counselor.dashboard')
                ->with('error', 'Data screening tidak ditemukan.');
        }

        // Pastikan screening spesifik ini sudah memiliki tindak lanjut setelah tanggal screening sebelum boleh dihapus
        $screeningTime = \Carbon\Carbon::parse($result->created_at)->subMinute();
        $hasTindakLanjut = DB::table('tindak_lanjuts')
            ->where('student_id', $result->student_id)
            ->where(function ($q) use ($screeningTime) {
                $q->where('tanggal_tindak_lanjut', '>=', $screeningTime)
                  ->orWhere('created_at', '>=', $screeningTime);
            })
            ->exists();

        if (!$hasTindakLanjut) {
            return redirect()->route('counselor.dashboard')
                ->with('error', 'Hanya hasil screening yang sudah ditindak lanjuti yang dapat dihapus.');
        }

        DB::table('dass_results')->where('id', $id)->delete();

        return redirect()->route('counselor.dashboard')
            ->with('success', 'Hasil screening I-DASS-Y berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | KATEGORI DASS-21
    |--------------------------------------------------------------------------
    */

    private function getDepressionCategory(
        int $score
    ): string {

        return match (true) {

            $score <= 9 =>
                'Normal',

            $score <= 13 =>
                'Ringan',

            $score <= 20 =>
                'Sedang',

            $score <= 27 =>
                'Parah',

            default =>
                'Sangat Parah',
        };
    }


    private function getAnxietyCategory(
        int $score
    ): string {

        return match (true) {

            $score <= 7 =>
                'Normal',

            $score <= 9 =>
                'Ringan',

            $score <= 14 =>
                'Sedang',

            $score <= 19 =>
                'Parah',

            default =>
                'Sangat Parah',
        };
    }


    private function getStressCategory(
        int $score
    ): string {

        return match (true) {

            $score <= 14 =>
                'Normal',

            $score <= 18 =>
                'Ringan',

            $score <= 25 =>
                'Sedang',

            $score <= 33 =>
                'Parah',

            default =>
                'Sangat Parah',
        };
    }


    /**
     * Menentukan risiko keseluruhan.
     */
    private function getOverallRisk(
        string $depression,
        string $anxiety,
        string $stress
    ): string {

        $priority = [

            'Normal' =>
                1,

            'Ringan' =>
                2,

            'Sedang' =>
                3,

            'Parah' =>
                4,

            'Sangat Parah' =>
                5,
        ];


        $categories = [
            $depression,
            $anxiety,
            $stress,
        ];


        $highest =
            max(
                array_map(
                    fn ($category) =>
                        $priority[$category],
                    $categories
                )
            );


        return array_search(
            $highest,
            $priority,
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AI ASSISTANT
    |--------------------------------------------------------------------------
    */

    /**
     * Halaman AI Counselor Assistant.
     */
    public function showAiAssistant()
    {
        if (!Auth::check()) {
            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        $userRole =
            Auth::user()->role === 'guru_bk'
                ? 'guru_bk'
                : 'siswa';


        $isCounselor =
            $userRole === 'guru_bk';


        $latestDass = null;


        if (
            $userRole === 'siswa'
        ) {

            $latestDass =
                DB::table('dass_results')
                    ->where(
                        'student_id',
                        Auth::id()
                    )
                    ->orderByDesc(
                        'created_at'
                    )
                    ->first();
        }


        $dassScores =
            $latestDass
                ? [

                    'depression' =>
                        $latestDass->depression_score,

                    'anxiety' =>
                        $latestDass->anxiety_score,

                    'stress' =>
                        $latestDass->stress_score,

                ]
                : null;


        return view(
            'counselor.ai-assistant',
            compact(
                'isCounselor',
                'userRole',
                'dassScores'
            )
        );
    }


    /**
     * Generate AI Response.
     */
    public function generateAiResponse(
        Request $request
    ) {

        $validated =
            $request->validate([

                'userPrompt' => [
                    'required',
                    'string',
                    'max:2000',
                ],

                'role' => [
                    'required',
                    'string',
                    'in:guru_bk,siswa',
                ],

                'dassScores' => [
                    'nullable',
                    'array',
                ],
            ]);


        $userPrompt =
            $validated['userPrompt'];

        $role =
            $validated['role'];

        $dassScores =
            $validated['dassScores']
            ?? null;


        if (
            $role === 'guru_bk'
        ) {

            $systemInstruction =
                'Kamu adalah AI Advisor untuk Guru '
                . 'Bimbingan Konseling (BK) di SMAN 1 Lohbener. '
                . 'Bantu merumuskan strategi pendekatan '
                . 'intervensi psikopedagogis berdasarkan '
                . 'skor DASS-21 dan situasi siswa. '
                . 'Berikan saran profesional, langkah-langkah '
                . 'intervensi, dan rekomendasi tindak lanjut. '
                . 'Jawab dalam Bahasa Indonesia yang formal '
                . 'dan profesional.';

        } else {

            $systemInstruction =
                'Kamu adalah teman refleksi diri AI '
                . 'yang empatik untuk siswa di SMAN 1 Lohbener. '
                . 'Bantu siswa mengekspresikan pikiran, '
                . 'mengurai emosi, dan menyajikan latihan '
                . 'relaksasi mandiri. '
                . 'Bersikap hangat, suportif, dan tidak '
                . 'menghakimi. '
                . 'Jawab dalam Bahasa Indonesia yang ramah '
                . 'dan mudah dipahami.';
        }


        $dassContext = '';


        if (
            is_array($dassScores)
            && isset(
                $dassScores['depression'],
                $dassScores['anxiety'],
                $dassScores['stress']
            )
        ) {

            $dassContext =
                "\n\n[Konteks Skor DASS-21 terbaru: "
                . "Depresi="
                . $dassScores['depression']
                . ", Kecemasan="
                . $dassScores['anxiety']
                . ", Stres="
                . $dassScores['stress']
                . "]";
        }


        $fullPrompt =
            $systemInstruction
            . $dassContext
            . "\n\nPertanyaan/Skenario:\n"
            . $userPrompt;


        try {

            $apiKey =
                env(
                    'GEMINI_API_KEY',
                    ''
                );


            if (
                empty($apiKey)
            ) {

                return response()->json([
                    'aiResponse' =>
                        'API Key Gemini belum dikonfigurasi. '
                        . 'Silakan tambahkan GEMINI_API_KEY di file .env.',
                ]);
            }


            $response =
                Http::timeout(30)
                    ->withHeaders([
                        'Content-Type' =>
                            'application/json',
                    ])
                    ->post(
                        'https://generativelanguage.googleapis.com/'
                        . 'v1beta/models/'
                        . 'gemini-2.0-flash:generateContent'
                        . '?key='
                        . $apiKey,
                        [

                            'contents' => [

                                [

                                    'parts' => [

                                        [
                                            'text' =>
                                                $fullPrompt,
                                        ],

                                    ],

                                ],

                            ],

                        ]
                    );


            if (
                $response->successful()
            ) {

                $data =
                    $response->json();


                $aiText =
                    $data['candidates'][0]
                        ['content']
                        ['parts'][0]
                        ['text']
                    ?? 'Tidak ada respon dari AI.';


                return response()->json([
                    'aiResponse' =>
                        $aiText,
                ]);
            }


            return response()->json([
                'aiResponse' =>
                    'Gagal mendapatkan respon dari Gemini API. Status: '
                    . $response->status(),
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' =>
                    false,

                'aiResponse' =>
                    'Terjadi kesalahan saat menghubungi layanan AI.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD KEPALA SEKOLAH
    |--------------------------------------------------------------------------
    */

    /**
     * Dashboard Kepala Sekolah.
     *
     * Role database yang digunakan:
     * kepala_sekolah
     */
    public function principalDashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KEPALA SEKOLAH
        |--------------------------------------------------------------------------
        |
        | PENTING:
        | Akun Kepala Sekolah Anda menggunakan:
        |
        | kepala_sekolah
        |
        | BUKAN:
        |
        | kepala
        |
        */

        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Kepala Sekolah.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL SISWA
        |--------------------------------------------------------------------------
        */

        $totalStudents =
            DB::table('users')
                ->where(
                    'role',
                    'siswa'
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL SCREENING DASS-21
        |--------------------------------------------------------------------------
        */

        $totalAssessments =
            DB::table('dass_results')
                ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL SESI KONSELING SELESAI
        |--------------------------------------------------------------------------
        */

        $totalSessionsCompleted =
            DB::table('bookings')
                ->where(
                    'status',
                    'Selesai'
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | HASIL DASS-21 TERBARU SETIAP SISWA
        |--------------------------------------------------------------------------
        */

        $latestResults =
            DB::table('dass_results as dr')
                ->join(
                    'users as u',
                    'dr.student_id',
                    '=',
                    'u.id'
                )
                ->select(
                    'dr.*',
                    'u.name as student_name',
                    'u.kelas'
                )
                ->where(
                    'u.role',
                    'siswa'
                )
                ->whereRaw(
                    'dr.id = (
                        SELECT MAX(dr2.id)
                        FROM dass_results dr2
                        WHERE dr2.student_id = dr.student_id
                    )'
                )
                ->orderBy(
                    'u.kelas'
                )
                ->orderBy(
                    'u.name'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI TINGKAT RISIKO
        |--------------------------------------------------------------------------
        |
        | Index:
        |
        | 0 = Normal
        | 1 = Ringan
        | 2 = Sedang
        | 3 = Parah
        | 4 = Sangat Parah
        |
        */

        $severityDistribution = [
            0,
            0,
            0,
            0,
            0,
        ];


        /*
        |--------------------------------------------------------------------------
        | STATISTIK SISWA
        |--------------------------------------------------------------------------
        */

        $normalStudentsCount =
            0;

        $mildStudentsCount =
            0;

        $moderateStudentsCount =
            0;

        $highRiskStudentsCount =
            0;


        /*
        |--------------------------------------------------------------------------
        | HITUNG RISIKO
        |--------------------------------------------------------------------------
        */

        foreach (
            $latestResults as $result
        ) {

            $depressionCategory =
                $this->getDepressionCategory(
                    (int)
                    $result->depression_score
                );


            $anxietyCategory =
                $this->getAnxietyCategory(
                    (int)
                    $result->anxiety_score
                );


            $stressCategory =
                $this->getStressCategory(
                    (int)
                    $result->stress_score
                );


            $overallRisk =
                $this->getOverallRisk(
                    $depressionCategory,
                    $anxietyCategory,
                    $stressCategory
                );


            /*
            |--------------------------------------------------------------------------
            | Simpan kategori ke object
            |--------------------------------------------------------------------------
            */

            $result->depression_category =
                $depressionCategory;

            $result->anxiety_category =
                $anxietyCategory;

            $result->stress_category =
                $stressCategory;

            $result->overall_risk =
                $overallRisk;


            /*
            |--------------------------------------------------------------------------
            | Distribusi
            |--------------------------------------------------------------------------
            */

            switch (
                $overallRisk
            ) {

                case 'Normal':

                    $normalStudentsCount++;

                    $severityDistribution[0]++;

                    break;


                case 'Ringan':

                    $mildStudentsCount++;

                    $severityDistribution[1]++;

                    break;


                case 'Sedang':

                    $moderateStudentsCount++;

                    $severityDistribution[2]++;

                    break;


                case 'Parah':

                    $highRiskStudentsCount++;

                    $severityDistribution[3]++;

                    break;


                case 'Sangat Parah':

                    $highRiskStudentsCount++;

                    $severityDistribution[4]++;

                    break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DATA KELAS
        |--------------------------------------------------------------------------
        */

        $classLabels =
            DB::table('users')
                ->where(
                    'role',
                    'siswa'
                )
                ->whereNotNull(
                    'kelas'
                )
                ->select(
                    'kelas'
                )
                ->distinct()
                ->orderBy(
                    'kelas'
                )
                ->pluck(
                    'kelas'
                )
                ->values()
                ->toArray();


        $classStudentCounts =
            [];

        $classHighRiskCounts =
            [];


        /*
        |--------------------------------------------------------------------------
        | STATISTIK PER KELAS
        |--------------------------------------------------------------------------
        */

        foreach (
            $classLabels as $kelas
        ) {

            $studentIds =
                DB::table('users')
                    ->where(
                        'role',
                        'siswa'
                    )
                    ->where(
                        'kelas',
                        $kelas
                    )
                    ->pluck(
                        'id'
                    );


            $classStudentCounts[] =
                $studentIds->count();


            /*
            |--------------------------------------------------------------------------
            | Ambil hasil DASS siswa kelas tersebut
            |--------------------------------------------------------------------------
            */

            $classResults =
                DB::table('dass_results')
                    ->whereIn(
                        'student_id',
                        $studentIds
                    )
                    ->get();


            $highRiskCount =
                0;


            foreach (
                $classResults as $result
            ) {

                $depressionCategory =
                    $this->getDepressionCategory(
                        (int)
                        $result->depression_score
                    );


                $anxietyCategory =
                    $this->getAnxietyCategory(
                        (int)
                        $result->anxiety_score
                    );


                $stressCategory =
                    $this->getStressCategory(
                        (int)
                        $result->stress_score
                    );


                $overallRisk =
                    $this->getOverallRisk(
                        $depressionCategory,
                        $anxietyCategory,
                        $stressCategory
                    );


                if (
                    in_array(
                        $overallRisk,
                        [
                            'Parah',
                            'Sangat Parah',
                        ],
                        true
                    )
                ) {

                    $highRiskCount++;
                }
            }


            $classHighRiskCounts[] =
                $highRiskCount;
        }


        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'principal.dashboard',
            compact(

                'totalStudents',

                'totalAssessments',

                'totalSessionsCompleted',

                'severityDistribution',

                'classLabels',

                'classStudentCounts',

                'classHighRiskCounts',

                'normalStudentsCount',

                'mildStudentsCount',

                'moderateStudentsCount',

                'highRiskStudentsCount',

                'latestResults'
            )
        );
    }


    /**
     * Export laporan Kepala Sekolah.
     */
    public function exportPrincipalReport()
    {
        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Anda tidak memiliki akses.'
            );
        }


        return redirect()
            ->route(
                'principal.dashboard'
            )
            ->with(
                'success',
                'Laporan berhasil disiapkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN GURU BK
    |--------------------------------------------------------------------------
    |
    | Digunakan oleh Kepala Sekolah untuk:
    |
    | - Melihat daftar Guru BK
    | - Menambahkan Guru BK baru
    | - Menghapus Guru BK
    |
    */


    /**
     * Daftar Guru BK.
     *
     * Hanya Kepala Sekolah yang dapat mengakses.
     */
    public function principalGuruBKList()
    {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KEPALA SEKOLAH
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Kepala Sekolah.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA GURU BK
        |--------------------------------------------------------------------------
        */

        $guruBKList =
            DB::table('users')
                ->where(
                    'role',
                    'guru_bk'
                )
                ->orderBy(
                    'name'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'principal.guru-bk',
            compact(
                'guruBKList'
            )
        );
    }


    /**
     * Tambah Guru BK baru.
     *
     * Hanya Kepala Sekolah yang dapat mengakses.
     */
    public function principalGuruBKStore(
        Request $request
    ) {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KEPALA SEKOLAH
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Kepala Sekolah.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI INPUT
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ],
            [
                'name.required' =>
                    'Nama guru BK wajib diisi.',

                'email.required' =>
                    'Email wajib diisi.',

                'email.email' =>
                    'Format email tidak valid.',

                'email.unique' =>
                    'Email sudah terdaftar.',

                'password.required' =>
                    'Password wajib diisi.',

                'password.min' =>
                    'Password minimal 8 karakter.',

                'password.confirmed' =>
                    'Konfirmasi password tidak cocok.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | BUAT AKUN GURU BK
        |--------------------------------------------------------------------------
        */

        DB::table('users')->insert([

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'password' =>
                bcrypt($validated['password']),

            'role' =>
                'guru_bk',

            'created_at' =>
                now(),

            'updated_at' =>
                now(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'principal.guru-bk'
            )
            ->with(
                'success',
                'Guru BK "' .
                $validated['name'] .
                '" berhasil ditambahkan.'
            );
    }


    /**
     * Hapus Guru BK.
     *
     * Hanya Kepala Sekolah yang dapat mengakses.
     */
    public function principalGuruBKDestroy(
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KEPALA SEKOLAH
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Kepala Sekolah.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK GURU BK ADA
        |--------------------------------------------------------------------------
        */

        $guru = DB::table('users')
            ->where('id', $id)
            ->where('role', 'guru_bk')
            ->first();


        if (!$guru) {

            return redirect()
                ->route(
                    'principal.guru-bk'
                )
                ->with(
                    'error',
                    'Guru BK tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | HAPUS
        |--------------------------------------------------------------------------
        */

        DB::table('users')
            ->where('id', $id)
            ->where('role', 'guru_bk')
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'principal.guru-bk'
            )
            ->with(
                'success',
                'Guru BK "' .
                $guru->name .
                '" berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DAFTAR SISWA
    |--------------------------------------------------------------------------
    |
    | Digunakan oleh Guru BK dan Kepala Sekolah untuk:
    |
    | - Melihat daftar siswa terdaftar
    | - Filter berdasarkan kelas
    | - Menghapus akun siswa
    |
    */


    /**
     * Daftar Siswa (Guru BK).
     */
    public function counselorStudentList(
        Request $request
    ) {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE GURU BK
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !== 'guru_bk'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Guru BK.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DAFTAR KELAS
        |--------------------------------------------------------------------------
        */

        $kelasList =
            DB::table('users')
                ->where('role', 'siswa')
                ->whereNotNull('kelas')
                ->where('kelas', '!=', '')
                ->select('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas');


        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS & SEARCH
        |--------------------------------------------------------------------------
        */

        $filterKelas =
            $request->query('kelas');

        $search =
            $request->query('search');


        /*
        |--------------------------------------------------------------------------
        | QUERY SISWA
        |--------------------------------------------------------------------------
        */

        $query =
            DB::table('users')
                ->where('role', 'siswa')
                ->orderBy('kelas')
                ->orderBy('name');


        if (
            $filterKelas &&
            $filterKelas !== 'semua'
        ) {

            $query->where(
                'kelas',
                $filterKelas
            );
        }


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }


        $siswaList =
            $query->get();


        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        $deleteRouteName =
            'counselor.siswa.destroy';

        $pageTitle =
            'Daftar Siswa - Dashboard BK';

        $backRoute =
            'counselor.dashboard';

        $backLabel =
            '← Kembali ke Dashboard BK';

        $userRole =
            'guru_bk';


        return view(
            'siswa.daftar-siswa',
            compact(
                'siswaList',
                'kelasList',
                'filterKelas',
                'search',
                'deleteRouteName',
                'pageTitle',
                'backRoute',
                'backLabel',
                'userRole'
            )
        );
    }


    /**
     * Hapus Siswa (Guru BK).
     */
    public function counselorStudentDestroy(
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE GURU BK
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !== 'guru_bk'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Guru BK.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK SISWA ADA
        |--------------------------------------------------------------------------
        */

        $siswa =
            DB::table('users')
                ->where('id', $id)
                ->where('role', 'siswa')
                ->first();


        if (!$siswa) {

            return redirect()
                ->route(
                    'counselor.siswa'
                )
                ->with(
                    'error',
                    'Siswa tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | HAPUS DATA TERKAIT
        |--------------------------------------------------------------------------
        */

        DB::table('dass_results')
            ->where('student_id', $id)
            ->delete();

        DB::table('bookings')
            ->where('student_id', $id)
            ->delete();

        DB::table('chats')
            ->where('sender_id', $id)
            ->orWhere('receiver_id', $id)
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | HAPUS SISWA
        |--------------------------------------------------------------------------
        */

        DB::table('users')
            ->where('id', $id)
            ->where('role', 'siswa')
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'counselor.siswa'
            )
            ->with(
                'success',
                'Siswa "' .
                $siswa->name .
                '" berhasil dihapus.'
            );
    }


    /**
     * Daftar Siswa (Kepala Sekolah).
     */
    public function principalStudentList(
        Request $request
    ) {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KEPALA SEKOLAH
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Kepala Sekolah.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DAFTAR KELAS
        |--------------------------------------------------------------------------
        */

        $kelasList =
            DB::table('users')
                ->where('role', 'siswa')
                ->whereNotNull('kelas')
                ->where('kelas', '!=', '')
                ->select('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas');


        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS & SEARCH
        |--------------------------------------------------------------------------
        */

        $filterKelas =
            $request->query('kelas');

        $search =
            $request->query('search');


        /*
        |--------------------------------------------------------------------------
        | QUERY SISWA
        |--------------------------------------------------------------------------
        */

        $query =
            DB::table('users')
                ->where('role', 'siswa')
                ->orderBy('kelas')
                ->orderBy('name');


        if (
            $filterKelas &&
            $filterKelas !== 'semua'
        ) {

            $query->where(
                'kelas',
                $filterKelas
            );
        }


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }


        $siswaList =
            $query->get();


        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        $deleteRouteName =
            'principal.siswa.destroy';

        $pageTitle =
            'Daftar Siswa - Dashboard Kepala Sekolah';

        $backRoute =
            'principal.dashboard';

        $backLabel =
            '← Kembali ke Dashboard Kepsek';

        $userRole =
            'kepala_sekolah';


        return view(
            'siswa.daftar-siswa',
            compact(
                'siswaList',
                'kelasList',
                'filterKelas',
                'search',
                'deleteRouteName',
                'pageTitle',
                'backRoute',
                'backLabel',
                'userRole'
            )
        );
    }


    /**
     * Hapus Siswa (Kepala Sekolah).
     */
    public function principalStudentDestroy(
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KEPALA SEKOLAH
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'kepala_sekolah'
        ) {

            abort(
                403,
                'Halaman ini hanya dapat diakses oleh Kepala Sekolah.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK SISWA ADA
        |--------------------------------------------------------------------------
        */

        $siswa =
            DB::table('users')
                ->where('id', $id)
                ->where('role', 'siswa')
                ->first();


        if (!$siswa) {

            return redirect()
                ->route(
                    'principal.siswa'
                )
                ->with(
                    'error',
                    'Siswa tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | HAPUS DATA TERKAIT
        |--------------------------------------------------------------------------
        */

        DB::table('dass_results')
            ->where('student_id', $id)
            ->delete();

        DB::table('bookings')
            ->where('student_id', $id)
            ->delete();

        DB::table('chats')
            ->where('sender_id', $id)
            ->orWhere('receiver_id', $id)
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | HAPUS SISWA
        |--------------------------------------------------------------------------
        */

        DB::table('users')
            ->where('id', $id)
            ->where('role', 'siswa')
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'principal.siswa'
            )
            ->with(
                'success',
                'Siswa "' .
                $siswa->name .
                '" berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIL SISWA
    |--------------------------------------------------------------------------
    */

    /**
     * Profil siswa untuk Guru BK.
     */
    public function showStudentProfile(
        $id
    ) {

        if (!Auth::check()) {

            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Hanya Guru BK
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'guru_bk'
        ) {

            abort(
                403,
                'Hanya Guru BK yang dapat melihat profil siswa.'
            );
        }


        $student =
            DB::table('users')
                ->where(
                    'id',
                    $id
                )
                ->where(
                    'role',
                    'siswa'
                )
                ->first();


        if (!$student) {

            abort(
                404,
                'Siswa tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DASS terbaru
        |--------------------------------------------------------------------------
        */

        $latestDass =
            DB::table('dass_results')
                ->where(
                    'student_id',
                    $id
                )
                ->orderByDesc(
                    'created_at'
                )
                ->first();

        if ($latestDass) {
            $latestDass->depression_category = $this->getDepressionCategory($latestDass->depression_score);
            $latestDass->anxiety_category = $this->getAnxietyCategory($latestDass->anxiety_score);
            $latestDass->stress_category = $this->getStressCategory($latestDass->stress_score);
        }


        /*
        |--------------------------------------------------------------------------
        | Dekripsi catatan sensitif
        |--------------------------------------------------------------------------
        */

        $rawNote =
            $student->catatan_sensitif
            ?? '';


        $decryptedNote =
            null;


        if ($rawNote) {

            try {

                $decryptedNote =
                    Crypt::decryptString(
                        $rawNote
                    );

            } catch (\Throwable $e) {

                $decryptedNote =
                    null;
            }
        }


        $encryptedNote =
            $rawNote
            ?: 'Belum ada catatan khusus.';


        /*
        |--------------------------------------------------------------------------
        | Masking kontak darurat
        |--------------------------------------------------------------------------
        */

        $kontak =
            $student->kontak_darurat
            ?? '';


        $maskedKontak =
            '-';


        if (
            strlen($kontak) > 4
        ) {

            $maskedKontak =
                substr_replace(
                    $kontak,
                    str_repeat(
                        '*',
                        strlen($kontak) - 4
                    ),
                    0,
                    strlen($kontak) - 4
                );
        }


        return view(
            'counselor.student-profile',
            compact(
                'student',
                'latestDass',
                'decryptedNote',
                'encryptedNote',
                'maskedKontak'
            )
        );
    }


    /**
     * Update profil siswa.
     */
    public function updateStudentProfile(
        Request $request,
        $id
    ) {

        if (!Auth::check()) {

            return redirect('/')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Hanya Guru BK
        |--------------------------------------------------------------------------
        */

        if (
            Auth::user()->role !==
            'guru_bk'
        ) {

            abort(
                403,
                'Hanya Guru BK yang dapat memperbarui catatan khusus siswa.'
            );
        }

        $request->validate([
            'catatan_sensitif' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $student =
            DB::table('users')
                ->where(
                    'id',
                    $id
                )
                ->where(
                    'role',
                    'siswa'
                )
                ->first();

        if (!$student) {
            abort(
                404,
                'Siswa tidak ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Enkripsi Catatan Khusus & Riwayat Psikologis (AES-256)
        | Guru BK hanya berwenang merubah catatan khusus, bukan profil siswa.
        |--------------------------------------------------------------------------
        */

        $encryptedNote = null;

        if ($request->filled('catatan_sensitif')) {
            $encryptedNote = Crypt::encryptString(
                $request->catatan_sensitif
            );
        }

        DB::table('users')
            ->where(
                'id',
                $id
            )
            ->update([
                'catatan_sensitif' => $encryptedNote,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route(
                'counselor.student-profile',
                $id
            )
            ->with(
                'success',
                'Catatan khusus dan riwayat psikologis siswa berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BLADE VIEWER
    |--------------------------------------------------------------------------
    */

    /**
     * Laravel Blade Architecture Viewer.
     */
    public function showBladeViewer()
    {
        $bladeFiles = [

            'layout' => [

                'title' =>
                    'Layout Utama (Blade Template)',

                'path' =>
                    'resources/views/layouts/app.blade.php',

                'code' =>
                    file_get_contents(
                        resource_path(
                            'views/layouts/app.blade.php'
                        )
                    ),
            ],


            'dass21' => [

                'title' =>
                    'DASS-21 View (Blade Template)',

                'path' =>
                    'resources/views/dass21/index.blade.php',

                'code' =>
                    file_get_contents(
                        resource_path(
                            'views/dass21/index.blade.php'
                        )
                    ),
            ],


            'booking' => [

                'title' =>
                    'Booking Konseling View (Blade Template)',

                'path' =>
                    'resources/views/booking/index.blade.php',

                'code' =>
                    file_get_contents(
                        resource_path(
                            'views/booking/index.blade.php'
                        )
                    ),
            ],


            'chat' => [

                'title' =>
                    'Chat Privat E2EE View',

                'path' =>
                    'resources/views/chat/index.blade.php',

                'code' =>
                    file_get_contents(
                        resource_path(
                            'views/chat/index.blade.php'
                        )
                    ),
            ],


            'controller' => [

                'title' =>
                    'Laravel BKController PHP',

                'path' =>
                    'app/Http/Controllers/BKController.php',

                'code' =>
                    file_get_contents(
                        app_path(
                            'Http/Controllers/BKController.php'
                        )
                    ),
            ],
        ];


        return view(
            'admin.blade-viewer',
            compact(
                'bladeFiles'
            )
        );
    }
}