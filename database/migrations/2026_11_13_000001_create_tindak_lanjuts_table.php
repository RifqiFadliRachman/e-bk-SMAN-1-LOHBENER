<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tindak_lanjuts', function (Blueprint $table) {
            $table->id();

            // Siswa (Identitas siswa yang dibatasi aksesnya)
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Guru BK (Petugas yang menangani)
            $table->foreignId('counselor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tanggal tindak lanjut (Waktu penanganan)
            $table->dateTime('tanggal_tindak_lanjut');

            // Tindakan (Konseling atau rujukan)
            $table->string('tindakan', 100);

            // Status (Belum ditangani / Proses / Selesai)
            $table->string('status', 50)->default('Belum ditangani');

            // Catatan (Catatan tindak lanjut rahasia - ciphertext AES-256)
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('student_id');
            $table->index('counselor_id');
            $table->index('tanggal_tindak_lanjut');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjuts');
    }
};

