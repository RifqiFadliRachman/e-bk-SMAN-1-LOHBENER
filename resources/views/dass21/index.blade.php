@extends('layouts.app')

@section('title', 'Screening I-DASS-Y (DASS for Youth) - E-BK SMAN 1 Lohbener')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="bg-[#5A6D58] rounded-3xl p-6 md:p-8 text-white shadow-sm">

        <div class="flex items-center gap-2 mb-4">

            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold">

                <i data-lucide="activity" class="w-4 h-4"></i>

                INSTRUMEN STANDAR I-DASS-Y (DASS FOR YOUTH)

            </span>

        </div>

        <h1 class="text-2xl md:text-3xl font-bold">
            Deteksi Dini Kesehatan Mental Siswa (I-DASS-Y)
        </h1>

        <p class="text-sm md:text-base text-white/80 mt-3 leading-relaxed max-w-4xl">
            Kuesioner I-DASS-Y (Depression, Anxiety, and Stress Scales for Youth) versi Bahasa Indonesia
            yang dirancang dan divalidasi khusus untuk kelompok remaja dan siswa sekolah, terdiri dari
            21 pernyataan. Hasil penilaian digunakan sebagai informasi awal untuk membantu proses layanan Bimbingan dan Konseling.
        </p>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="bg-[#E8F0E7] border border-[#8BA888] text-[#5A6D58] rounded-2xl p-4">

            <div class="flex items-center gap-2">

                <i data-lucide="check-circle" class="w-5 h-5"></i>

                <span class="font-bold text-sm">
                    {{ session('success') }}
                </span>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERROR MESSAGE --}}
    {{-- ========================================================= --}}

    @if(session('error'))

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl p-4">

            <div class="flex items-center gap-2">

                <i data-lucide="alert-circle" class="w-5 h-5"></i>

                <span class="font-bold text-sm">
                    {{ session('error') }}
                </span>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- VALIDATION ERROR --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl p-4">

            <div class="flex items-start gap-3">

                <i
                    data-lucide="alert-triangle"
                    class="w-5 h-5 flex-shrink-0 mt-0.5"
                ></i>

                <div>

                    <p class="font-bold text-sm mb-2">
                        Terdapat kesalahan:
                    </p>

                    <ul class="list-disc list-inside space-y-1 text-xs">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- HASIL SCREENING TERAKHIR --}}
    {{-- ========================================================= --}}

    @if(isset($latestResult) && $latestResult)

        <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 md:p-8 shadow-sm space-y-6">

            {{-- Header Hasil --}}
            <div class="border-b border-[#F5F2EB] pb-4">

                <div class="flex items-center justify-between flex-wrap gap-3">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-[#5A6D58]">
                                Hasil Screening Terakhir
                            </h2>

                            {{-- 3. Tanggal screening --}}
                            <p class="text-xs text-[#A39B8B] mt-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 inline-block mr-1"></i>
                                {{ \Carbon\Carbon::parse($latestResult->created_at)->translatedFormat('l, d F Y • H:i') }} WIB
                            </p>
                        </div>

                    </div>

                    {{-- Risiko Keseluruhan --}}
                    @php
                        $riskColors = [
                            'Normal'       => 'bg-[#E8F0E7] text-[#5A6D58] border-[#8BA888]',
                            'Ringan'       => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Sedang'       => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'Parah'        => 'bg-orange-50 text-orange-700 border-orange-200',
                            'Sangat Parah' => 'bg-red-50 text-red-700 border-red-200',
                        ];
                        $riskClass = $riskColors[$latestResult->overall_risk] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                    @endphp

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $riskClass }}">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        Risiko Keseluruhan: {{ $latestResult->overall_risk }}
                    </span>

                </div>

            </div>


            {{-- 1. Skor masing-masing dimensi & 2. Kategori hasil --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- DEPRESI --}}
                @php
                    $depClass = $riskColors[$latestResult->depression_category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                @endphp
                <div class="rounded-2xl border border-[#E6E2D3] p-5 bg-[#FDFCF8] space-y-3">

                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#5A6D58] text-white flex items-center justify-center">
                            <i data-lucide="cloud-rain" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm font-bold text-[#333D32]">Depresi</span>
                    </div>

                    <div class="text-center py-2">
                        <p class="text-3xl font-black text-[#5A6D58]">
                            {{ $latestResult->depression_score }}
                        </p>
                        <p class="text-[10px] text-[#A39B8B] mt-1 uppercase tracking-wider">Skor</p>
                    </div>

                    <div class="text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $depClass }}">
                            {{ $latestResult->depression_category }}
                        </span>
                    </div>

                </div>


                {{-- KECEMASAN --}}
                @php
                    $anxClass = $riskColors[$latestResult->anxiety_category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                @endphp
                <div class="rounded-2xl border border-[#E6E2D3] p-5 bg-[#FDFCF8] space-y-3">

                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#5A6D58] text-white flex items-center justify-center">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm font-bold text-[#333D32]">Kecemasan</span>
                    </div>

                    <div class="text-center py-2">
                        <p class="text-3xl font-black text-[#5A6D58]">
                            {{ $latestResult->anxiety_score }}
                        </p>
                        <p class="text-[10px] text-[#A39B8B] mt-1 uppercase tracking-wider">Skor</p>
                    </div>

                    <div class="text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $anxClass }}">
                            {{ $latestResult->anxiety_category }}
                        </span>
                    </div>

                </div>


                {{-- STRES --}}
                @php
                    $strClass = $riskColors[$latestResult->stress_category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                @endphp
                <div class="rounded-2xl border border-[#E6E2D3] p-5 bg-[#FDFCF8] space-y-3">

                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#5A6D58] text-white flex items-center justify-center">
                            <i data-lucide="flame" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm font-bold text-[#333D32]">Stres</span>
                    </div>

                    <div class="text-center py-2">
                        <p class="text-3xl font-black text-[#5A6D58]">
                            {{ $latestResult->stress_score }}
                        </p>
                        <p class="text-[10px] text-[#A39B8B] mt-1 uppercase tracking-wider">Skor</p>
                    </div>

                    <div class="text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $strClass }}">
                            {{ $latestResult->stress_category }}
                        </span>
                    </div>

                </div>

            </div>


            {{-- 4. Disclaimer: Screening awal, bukan diagnosis --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">

                <div class="flex items-start gap-3">

                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>

                    <div>

                        <p class="text-sm font-bold text-amber-800">
                            Penting: Hasil Ini Bukan Diagnosis
                        </p>

                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                            Hasil I-DASS-Y merupakan <strong>screening awal</strong> yang bersifat
                            skrining mandiri dan <strong>bukan merupakan diagnosis klinis</strong>.
                            Hasil ini digunakan sebagai informasi pendukung untuk proses
                            layanan Bimbingan dan Konseling di sekolah. Untuk diagnosis resmi,
                            diperlukan asesmen lebih lanjut oleh psikolog atau psikiater profesional.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- RIWAYAT HASIL SCREENING --}}
    {{-- ========================================================= --}}

    @if(isset($screeningHistory) && $screeningHistory->count() > 1)

        <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 md:p-8 shadow-sm space-y-4">

            <div class="flex items-center gap-3 border-b border-[#F5F2EB] pb-4">

                <div class="w-10 h-10 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-[#5A6D58]">
                        Riwayat Screening
                    </h2>
                    <p class="text-xs text-[#A39B8B] mt-0.5">
                        Total {{ $screeningHistory->count() }} kali screening telah dilakukan.
                    </p>
                </div>

            </div>


            {{-- Tabel Riwayat --}}
            <div class="overflow-x-auto">

                <table class="w-full text-xs">

                    <thead>
                        <tr class="border-b border-[#E6E2D3]">
                            <th class="text-left py-3 px-3 font-bold text-[#5A6D58]">No</th>
                            <th class="text-left py-3 px-3 font-bold text-[#5A6D58]">Tanggal</th>
                            <th class="text-center py-3 px-3 font-bold text-[#5A6D58]">Depresi</th>
                            <th class="text-center py-3 px-3 font-bold text-[#5A6D58]">Kecemasan</th>
                            <th class="text-center py-3 px-3 font-bold text-[#5A6D58]">Stres</th>
                            <th class="text-center py-3 px-3 font-bold text-[#5A6D58]">Risiko</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($screeningHistory as $index => $history)

                            @php
                                $histRiskClass = $riskColors[$history->overall_risk] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                $histDepClass  = $riskColors[$history->depression_category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                $histAnxClass  = $riskColors[$history->anxiety_category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                $histStrClass  = $riskColors[$history->stress_category] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                            @endphp

                            <tr class="border-b border-[#F5F2EB] hover:bg-[#FDFCF8] transition-colors {{ $index === 0 ? 'bg-[#FDFCF8]' : '' }}">

                                <td class="py-3 px-3 text-[#5C564B]">
                                    {{ $index + 1 }}
                                    @if($index === 0)
                                        <span class="ml-1 inline-block px-1.5 py-0.5 bg-[#5A6D58] text-white text-[9px] font-bold rounded-md">
                                            Terbaru
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-3 text-[#5C564B] whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($history->created_at)->translatedFormat('d M Y, H:i') }}
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <span class="font-bold text-[#333D32]">{{ $history->depression_score }}</span>
                                    <br>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $histDepClass }}">
                                        {{ $history->depression_category }}
                                    </span>
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <span class="font-bold text-[#333D32]">{{ $history->anxiety_score }}</span>
                                    <br>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $histAnxClass }}">
                                        {{ $history->anxiety_category }}
                                    </span>
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <span class="font-bold text-[#333D32]">{{ $history->stress_score }}</span>
                                    <br>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $histStrClass }}">
                                        {{ $history->stress_category }}
                                    </span>
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $histRiskClass }}">
                                        {{ $history->overall_risk }}
                                    </span>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Disclaimer kecil di bawah tabel --}}
            <div class="flex items-center gap-2 text-[10px] text-[#A39B8B] pt-2">
                <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0"></i>
                <span>
                    Seluruh hasil di atas merupakan screening awal dan bukan diagnosis klinis.
                </span>
            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM I-DASS-Y --}}
    {{-- ========================================================= --}}

    <form
        action="{{ route('dass21.store') }}"
        method="POST"
        id="dass21Form"
        class="space-y-6"
    >

        @csrf


        {{-- ===================================================== --}}
        {{-- PETUNJUK PENGISIAN --}}
        {{-- ===================================================== --}}

        <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-sm">

            <div class="border-b border-[#F5F2EB] pb-4">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">

                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>

                    </div>

                    <div>

                        <h2 class="text-lg font-bold text-[#5A6D58]">
                            Petunjuk Pengisian
                        </h2>

                        <p class="text-xs text-[#A39B8B] mt-1">
                            Bacalah setiap pernyataan dengan teliti.
                        </p>

                    </div>

                </div>

                <p class="text-xs md:text-sm text-[#5C564B] mt-4 leading-relaxed">

                    Bacalah setiap pernyataan di bawah ini dan pilih opsi
                    nilai <strong>0 - 3</strong> yang paling menggambarkan
                    seberapa benar kondisi yang kamu rasakan dalam <strong>1 minggu terakhir</strong>.

                </p>

                <div class="mt-4 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl p-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                0
                            </span>

                            <span class="text-[#5C564B]">
                                Tidak benar
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                1
                            </span>

                            <span class="text-[#5C564B]">
                                Sedikit benar
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                2
                            </span>

                            <span class="text-[#5C564B]">
                                Agak benar
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                3
                            </span>

                            <span class="text-[#5C564B]">
                                Sangat benar
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- DAFTAR PERTANYAAN --}}
            {{-- ================================================= --}}

            <div class="space-y-4 mt-6">

                @forelse($questions as $index => $q)

                    <div
                        class="question-card p-5 rounded-2xl border border-[#E6E2D3] bg-[#FDFCF8] transition-all"
                        data-question="{{ $index + 1 }}"
                    >

                        {{-- ===================================== --}}
                        {{-- NOMOR + PERTANYAAN --}}
                        {{-- ===================================== --}}

                        <div class="flex items-start gap-3">

                            <span
                                class="w-8 h-8 rounded-full bg-[#5A6D58] text-white text-xs font-bold flex items-center justify-center flex-shrink-0"
                            >
                                {{ $q->question_number ?? ($index + 1) }}
                            </span>

                            <div class="flex-1 min-w-0">

                                <p class="text-sm md:text-base font-semibold text-[#333D32] leading-relaxed">

                                    {{-- PENTING:
                                         Database menggunakan kolom `text`,
                                         bukan `question_text`.
                                    --}}

                                    {{ $q->text }}

                                </p>


                                {{-- ================================= --}}
                                {{-- PILIHAN JAWABAN --}}
                                {{-- ================================= --}}

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">

                                    @foreach([
                                        0 => 'Tidak benar',
                                        1 => 'Sedikit benar',
                                        2 => 'Agak benar',
                                        3 => 'Sangat benar'
                                    ] as $val => $label)

                                        <label
                                            class="answer-option flex items-center gap-3 p-3 rounded-xl border border-[#E6E2D3] bg-white cursor-pointer hover:bg-[#F5F2EB] hover:border-[#8BA888] transition-all"
                                        >

                                            <input
                                                type="radio"
                                                name="answers[{{ $q->id }}]"
                                                value="{{ $val }}"
                                                class="w-4 h-4 accent-[#5A6D58]"
                                                {{ old('answers.' . $q->id) == $val ? 'checked' : '' }}
                                                required
                                            >

                                            <span class="text-xs md:text-sm font-medium text-[#5C564B]">

                                                <strong class="text-[#5A6D58]">
                                                    {{ $val }}
                                                </strong>

                                                -
                                                {{ $label }}

                                            </span>

                                        </label>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    {{-- ========================================= --}}
                    {{-- JIKA PERTANYAAN KOSONG --}}
                    {{-- ========================================= --}}

                    <div class="text-center py-12">

                        <div class="w-14 h-14 rounded-full bg-[#F5F2EB] mx-auto flex items-center justify-center text-[#A39B8B]">

                            <i
                                data-lucide="clipboard-x"
                                class="w-7 h-7"
                            ></i>

                        </div>

                        <h3 class="font-bold text-[#5A6D58] mt-4">
                            Pertanyaan belum tersedia
                        </h3>

                        <p class="text-xs text-[#A39B8B] mt-1">
                            Data pertanyaan I-DASS-Y belum tersedia di database.
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- ================================================= --}}
            {{-- TOMBOL SUBMIT --}}
            {{-- ================================================= --}}

            @if(count($questions) > 0)

                <div class="pt-6 mt-6 border-t border-[#F5F2EB]">

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">

                        <div class="text-xs text-[#A39B8B]">

                            <div class="flex items-center gap-2">

                                <i
                                    data-lucide="info"
                                    class="w-4 h-4"
                                ></i>

                                <span>
                                    Pastikan seluruh 21 pertanyaan telah dijawab.
                                </span>

                            </div>

                        </div>


                        <button
                            type="submit"
                            id="submitButton"
                            class="w-full sm:w-auto bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold text-sm px-6 py-3 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2"
                        >

                            <i
                                data-lucide="send"
                                class="w-4 h-4"
                            ></i>

                            <span>
                                Kirim Jawaban
                            </span>

                        </button>

                    </div>

                </div>

            @endif

        </div>

    </form>

</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('dass21Form');

    const submitButton = document.getElementById('submitButton');

    if (!form) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | HIGHLIGHT JAWABAN YANG DIPILIH
    |--------------------------------------------------------------------------
    */

    const radioButtons = form.querySelectorAll(
        'input[type="radio"]'
    );

    radioButtons.forEach(function (radio) {

        radio.addEventListener('change', function () {

            const questionCard =
                this.closest('.question-card');

            if (!questionCard) {
                return;
            }

            const options =
                questionCard.querySelectorAll('.answer-option');

            options.forEach(function (option) {

                option.classList.remove(
                    'bg-[#E8F0E7]',
                    'border-[#8BA888]'
                );

                option.classList.add(
                    'bg-white',
                    'border-[#E6E2D3]'
                );

            });


            const selectedOption =
                this.closest('.answer-option');

            if (selectedOption) {

                selectedOption.classList.remove(
                    'bg-white',
                    'border-[#E6E2D3]'
                );

                selectedOption.classList.add(
                    'bg-[#E8F0E7]',
                    'border-[#8BA888]'
                );

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | CEK SEMUA PERTANYAAN SEBELUM SUBMIT
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function (event) {

        const questions =
            form.querySelectorAll('.question-card');

        let unanswered = [];

        questions.forEach(function (question, index) {

            const checked =
                question.querySelector(
                    'input[type="radio"]:checked'
                );

            if (!checked) {

                unanswered.push(index + 1);

                question.classList.add(
                    'border-red-300',
                    'bg-red-50'
                );

            } else {

                question.classList.remove(
                    'border-red-300',
                    'bg-red-50'
                );

            }

        });


        if (unanswered.length > 0) {

            event.preventDefault();

            alert(
                'Mohon jawab semua pernyataan I-DASS-Y terlebih dahulu.\n\n' +
                'Pernyataan yang belum dijawab: nomor ' +
                unanswered.join(', ')
            );

            const firstUnanswered =
                questions[unanswered[0] - 1];

            if (firstUnanswered) {

                firstUnanswered.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            }

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | DISABLE BUTTON SETELAH SUBMIT
        |--------------------------------------------------------------------------
        */

        if (submitButton) {

            submitButton.disabled = true;

            submitButton.classList.add(
                'opacity-70',
                'cursor-not-allowed'
            );

            submitButton.innerHTML = `
                <svg
                    class="w-4 h-4 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>

                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>

                </svg>

                Memproses...
            `;

        }

    });


    /*
    |--------------------------------------------------------------------------
    | RESTORE SELECTED STATE
    |--------------------------------------------------------------------------
    */

    radioButtons.forEach(function (radio) {

        if (radio.checked) {

            const selectedOption =
                radio.closest('.answer-option');

            if (selectedOption) {

                selectedOption.classList.remove(
                    'bg-white',
                    'border-[#E6E2D3]'
                );

                selectedOption.classList.add(
                    'bg-[#E8F0E7]',
                    'border-[#8BA888]'
                );

            }

        }

    });

});

</script>

@endsection