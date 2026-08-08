@extends('layouts.app')

@section('title', 'Screening DASS-21 - E-BK SMAN 1 Lohbener')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm relative overflow-hidden border border-[#4A5D48]">
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold mb-3 border border-[#8BA888]/40">
                <i data-lucide="activity" class="w-3.5 h-3.5 text-[#A2C49F]"></i> INSTRUMEN STANDAR DASS-21
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Deteksi Dini Kesehatan Mental Siswa (DASS-21)
            </h2>
            <p class="text-[#D8E2D7] text-sm mt-2 leading-relaxed">
                Kuesioner Depresi, Kecemasan, dan Stres (21 Pernyataan). Hasil penilaian dirancang secara obyektif berdasarkan algoritma Lovibond & Lovibond (1995).
            </p>
        </div>
    </div>

    <!-- Active Form Questionnaire -->
    <form action="{{ route('dass21.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-xs space-y-6">
            <div class="border-b pb-3 border-[#F5F2EB]">
                <h3 class="text-lg font-bold text-[#5A6D58]">Petunjuk Pengisian</h3>
                <p class="text-xs text-[#A39B8B] mt-1">
                    Bacalah setiap pernyataan di bawah ini dan pilih opsi nilai (0 - 3) yang paling menggambarkan kondisi yang kamu rasakan dalam 1 minggu terakhir.
                </p>
            </div>

            <div class="space-y-4">
                @foreach($questions as $index => $q)
                    <div class="p-4 rounded-2xl border border-[#E6E2D3] bg-[#FDFCF8] transition-all">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#5A6D58] text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-[#333D32] leading-snug">
                                    {{ $q->text }}
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-3">
                                    @foreach([0 => 'Tidak pernah', 1 => 'Kadang-kadang', 2 => 'Sering', 3 => 'Hampir selalu'] as $val => $label)
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-[#E6E2D3] bg-white cursor-pointer hover:bg-[#F5F2EB] text-xs font-medium text-[#5C564B]">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $val }}" class="accent-[#5A6D58]" required>
                                            <span>{{ $val }} - {{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-[#F5F2EB] flex justify-end">
                <button type="submit" class="bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold text-sm px-6 py-3 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    Kirim & Dapatkan Rekomendasi BK <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
