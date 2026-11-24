<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DassQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menggunakan instrumen I-DASS-Y (Depression Anxiety Stress Scales for Youth versi Bahasa Indonesia)
     * Adaptasi resmi untuk remaja / siswa sekolah.
     */
    public function run(): void
    {
        $questions = [
            [
                'question_number' => 1,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa kesal dengan hal-hal kecil.',
            ],
            [
                'question_number' => 2,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa pusing, seperti akan pingsan.',
            ],
            [
                'question_number' => 3,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya tidak menikmati apapun.',
            ],
            [
                'question_number' => 4,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya kesulitan bernapas (misalnya bernapas dengan sangat cepat), meskipun tidak sedang berolahraga dan tidak sedang sakit.',
            ],
            [
                'question_number' => 5,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya membenci hidup saya.',
            ],
            [
                'question_number' => 6,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya bereaksi berlebihan dalam menghadapi situasi.',
            ],
            [
                'question_number' => 7,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Tangan saya terasa gemetar.',
            ],
            [
                'question_number' => 8,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa khawatir tentang banyak hal.',
            ],
            [
                'question_number' => 9,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa sangat ketakutan.',
            ],
            [
                'question_number' => 10,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Tidak ada hal baik yang bisa saya tunggu-tunggu.',
            ],
            [
                'question_number' => 11,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya mudah merasa jengkel atau kesal.',
            ],
            [
                'question_number' => 12,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa sulit untuk merasa santai.',
            ],
            [
                'question_number' => 13,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya tidak bisa berhenti merasa sedih.',
            ],
            [
                'question_number' => 14,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya merasa kesal ketika ada yang menyela atau mengganggu apa yang sedang saya lakukan.',
            ],
            [
                'question_number' => 15,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya merasa seperti akan panik.',
            ],
            [
                'question_number' => 16,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya membenci diri saya sendiri.',
            ],
            [
                'question_number' => 17,
                'category' => 'depression',
                'dimension' => 'depression',
                'text' => 'Saya merasa diri saya tidak berguna atau tidak baik.',
            ],
            [
                'question_number' => 18,
                'category' => 'stress',
                'dimension' => 'stress',
                'text' => 'Saya mudah merasa terusik atau terganggu.',
            ],
            [
                'question_number' => 19,
                'category' => 'anxiety',
                'dimension' => 'anxiety',
                'text' => 'Saya bisa merasakan jantung saya berdebar sangat cepat, meskipun tidak sedang berolahraga berat.',
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
                'text' => 'Saya merasa hidup ini sangat buruk.',
            ],
        ];

        foreach ($questions as $question) {
            DB::table('dass_questions')->updateOrInsert(
                ['question_number' => $question['question_number']],
                [
                    'category' => $question['category'],
                    'dimension' => $question['dimension'],
                    'text' => $question['text'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        if (isset($this->command)) {
            $this->command->info(
                'Berhasil memperbarui 21 pertanyaan dengan instrumen I-DASS-Y (DASS for Youth).'
            );
        }
    }
}
