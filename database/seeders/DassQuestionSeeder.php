<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DassQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [

            [
                'question_number' => 1,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa sulit untuk tenang.',
            ],

            [
                'question_number' => 2,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa mulut saya terasa kering.',
            ],

            [
                'question_number' => 3,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya tidak dapat merasakan perasaan positif sama sekali.',
            ],

            [
                'question_number' => 4,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya mengalami kesulitan bernapas.',
            ],

            [
                'question_number' => 5,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya merasa sulit untuk memulai melakukan sesuatu.',
            ],

            [
                'question_number' => 6,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya cenderung bereaksi berlebihan terhadap situasi.',
            ],

            [
                'question_number' => 7,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa gemetar, misalnya pada tangan.',
            ],

            [
                'question_number' => 8,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa menghabiskan banyak energi untuk merasa gugup.',
            ],

            [
                'question_number' => 9,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya khawatir terhadap situasi yang membuat saya panik atau terlihat bodoh.',
            ],

            [
                'question_number' => 10,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya merasa tidak ada yang dapat saya harapkan.',
            ],

            [
                'question_number' => 11,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa gelisah.',
            ],

            [
                'question_number' => 12,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa sulit untuk bersantai.',
            ],

            [
                'question_number' => 13,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya merasa sedih dan tertekan.',
            ],

            [
                'question_number' => 14,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya tidak dapat menoleransi gangguan terhadap apa yang sedang saya lakukan.',
            ],

            [
                'question_number' => 15,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa hampir panik.',
            ],

            [
                'question_number' => 16,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya tidak merasa antusias terhadap sesuatu.',
            ],

            [
                'question_number' => 17,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya merasa tidak berharga sebagai seorang individu.',
            ],

            [
                'question_number' => 18,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa mudah tersinggung.',
            ],

            [
                'question_number' => 19,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya menyadari kerja jantung saya meskipun tidak sedang melakukan aktivitas fisik.',
            ],

            [
                'question_number' => 20,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa takut tanpa alasan yang jelas.',
            ],

            [
                'question_number' => 21,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya merasa hidup ini tidak berarti.',
            ],
        ];

        // Hapus data pertanyaan sebelumnya
        DB::table('dass_questions')->delete();

        $now = now();

        foreach ($questions as &$question) {
            $question['created_at'] = $now;
            $question['updated_at'] = $now;
        }

        DB::table('dass_questions')->insert($questions);

        $this->command->info(
            'Berhasil memasukkan 21 pertanyaan DASS-21.'
        );
    }
}