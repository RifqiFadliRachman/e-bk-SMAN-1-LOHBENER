<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('siswa')->after('password');
            $table->string('kelas')->nullable()->after('role');
            $table->text('alamat')->nullable()->after('kelas');
            $table->string('nama_orang_tua')->nullable()->after('alamat');
            $table->string('kontak_darurat')->nullable()->after('nama_orang_tua');
            $table->text('catatan_sensitif')->nullable()->after('kontak_darurat');

            $table->index('role');
            $table->index('kelas');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_kelas_index');

            $table->dropColumn([
                'role',
                'kelas',
                'alamat',
                'nama_orang_tua',
                'kontak_darurat',
                'catatan_sensitif',
            ]);
        });
    }
};