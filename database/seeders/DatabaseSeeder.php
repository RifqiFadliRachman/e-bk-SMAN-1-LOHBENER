<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder pertanyaan DASS-21
        $this->call([
            DassQuestionSeeder::class,
        ]);

        // Akun siswa untuk testing
        User::updateOrCreate(
            ['email' => 'siswa@example.com'],
            [
                'name' => 'Siswa Demo',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'kelas' => 'XII IPA 1',
                'alamat' => 'Alamat Demo',
                'nama_orang_tua' => 'Orang Tua Demo',
                'kontak_darurat' => '0800000000',
            ]
        );

        // Akun Guru BK untuk testing
        User::updateOrCreate(
            ['email' => 'bk@example.com'],
            [
                'name' => 'Guru BK Demo',
                'password' => Hash::make('password'),
                'role' => 'guru_bk',
                'kelas' => null,
            ]
        );

        // Akun Kepala Sekolah untuk testing
        User::updateOrCreate(
            ['email' => 'kepala@example.com'],
            [
                'name' => 'Kepala Sekolah Demo',
                'password' => Hash::make('password'),
                'role' => 'kepala_sekolah',
                'kelas' => null,
            ]
        );
    }
}