<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('counselor_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('tanggal_sesi');
            $table->time('jam_sesi');

            $table->string('jenis_sesi', 50);
            $table->text('topik')->nullable();

            $table->string('status', 50)
                ->default('Menunggu Approval');

            $table->timestamps();

            $table->index('student_id');
            $table->index('counselor_id');
            $table->index('tanggal_sesi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};