@extends('layouts.app')

@section('title', 'Screening DASS-21 - E-BK SMAN 1 Lohbener')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="bg-[#5A6D58] rounded-3xl p-6 md:p-8 text-white shadow-sm">

        <div class="flex items-center gap-2 mb-4">

            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold">

                <i data-lucide="activity" class="w-4 h-4"></i>

                INSTRUMEN STANDAR DASS-21

            </span>

        </div>

        <h1 class="text-2xl md:text-3xl font-bold">
            Deteksi Dini Kesehatan Mental Siswa (DASS-21)
        </h1>

        <p class="text-sm md:text-base text-white/80 mt-3 leading-relaxed max-w-4xl">
            Kuesioner Depresi, Kecemasan, dan Stres yang terdiri dari
            21 pernyataan. Hasil penilaian digunakan sebagai informasi
            awal untuk membantu proses layanan Bimbingan dan Konseling.
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
    {{-- FORM DASS-21 --}}
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
                    kondisi yang kamu rasakan dalam <strong>1 minggu terakhir</strong>.

                </p>

                <div class="mt-4 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl p-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                0
                            </span>

                            <span class="text-[#5C564B]">
                                Tidak pernah
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                1
                            </span>

                            <span class="text-[#5C564B]">
                                Kadang-kadang
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                2
                            </span>

                            <span class="text-[#5C564B]">
                                Sering
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="font-bold text-[#5A6D58]">
                                3
                            </span>

                            <span class="text-[#5C564B]">
                                Hampir selalu
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
                                        0 => 'Tidak pernah',
                                        1 => 'Kadang-kadang',
                                        2 => 'Sering',
                                        3 => 'Hampir selalu'
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
                            Data pertanyaan DASS-21 belum tersedia di database.
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
                'Mohon jawab semua pertanyaan DASS-21 terlebih dahulu.\n\n' +
                'Pertanyaan yang belum dijawab: ' +
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