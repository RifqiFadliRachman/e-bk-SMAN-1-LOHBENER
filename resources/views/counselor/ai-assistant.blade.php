@extends('layouts.app')

@section('title', 'AI Counselor Assistant - E-BK SMAN 1 Lohbener')

@section('content')
<div x-data="aiCounselorAssistant()" class="space-y-6">

    {{-- Header Banner --}}
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm relative overflow-hidden border border-[#4A5D48]">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold border border-[#8BA888]/40 mb-2">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#FCD34D]"></i> GEMINI AI COUNSELOR ASSISTANT
            </div>
            <h2 class="text-xl font-bold tracking-tight text-white">
                @if($isCounselor)
                    AI Strategy Advisor (Pendamping Konselor)
                @else
                    Teman Refleksi Diri AI
                @endif
            </h2>
            <p class="text-xs text-[#D8E2D7] mt-1 leading-relaxed">
                @if($isCounselor)
                    Membantu Guru BK merumuskan strategi pendekatan intervensi psikopedagogis berdasarkan skor DASS-21 dan situasi siswa.
                @else
                    Ruang aman berbasis kecerdasan buatan untuk membantu mengekspresikan pikiran, mengurai emosi, dan menyajikan latihan relaksasi mandiri.
                @endif
            </p>
        </div>
    </div>

    {{-- Main Form & Response View --}}
    <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-xs space-y-6">

        <form @submit.prevent="handleGenerate" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-[#5A6D58] mb-1.5">
                    @if($isCounselor)
                        Skenario / Permasalahan Siswa yang Ingin Didiskusikan:
                    @else
                        Apa yang sedang dirasakan atau mengganggu pikiranmu hari ini?
                    @endif
                </label>
                <textarea
                    rows="4"
                    x-model="promptInput"
                    placeholder="{{ $isCounselor
                        ? 'Contoh: Siswa mengalami penurunan nilai drastis dan skor DASS-21 kecemasan parah (18). Langkah intervensi awal apa yang disarankan?'
                        : 'Contoh: Saya merasa sangat cemas menghadapi ujian besok dan sulit berkonsentrasi...' }}"
                    class="w-full text-xs p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none focus:ring-2 focus:ring-[#8BA888] font-medium text-[#333D32]"
                ></textarea>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-[11px] text-[#A39B8B] flex items-center gap-1">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-[#8BA888]"></i> AI Server-Side Secured • Privasi Terjaga
                </span>
                <button
                    type="submit"
                    :disabled="isGenerating || !promptInput.trim()"
                    class="bg-[#5A6D58] hover:bg-[#4E614C] disabled:opacity-50 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2"
                >
                    <template x-if="isGenerating">
                        <span class="flex items-center gap-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4 animate-spin"></i> Menganalisis...
                        </span>
                    </template>
                    <template x-if="!isGenerating">
                        <span class="flex items-center gap-2">
                            <i data-lucide="sparkles" class="w-4 h-4 text-[#FCD34D]"></i> Dapatkan Respon AI
                        </span>
                    </template>
                </button>
            </div>
        </form>

        {{-- AI Output Card --}}
        <div x-show="aiResponse" x-transition class="bg-[#F5F8F5] border border-[#8BA888]/40 p-5 rounded-2xl space-y-3">
            <div class="flex items-center gap-2 text-[#5A6D58] font-bold text-sm">
                <i data-lucide="bot" class="w-5 h-5 text-[#8BA888]"></i> Respon Asisten AI SMAN 1 Lohbener
            </div>
            <div
                x-text="aiResponse"
                class="text-xs text-[#333D32] leading-relaxed whitespace-pre-line font-medium bg-white p-4 rounded-xl border border-[#E6E2D3]"
            ></div>
            <p class="text-[10px] text-[#A39B8B] italic">
                *Catatan: Respon AI bersifat sebagai pendukung refleksi diri dan panduan intervensi awal, bukan pengganti diagnosis medis/psikiatri.
            </p>
        </div>

    </div>

</div>

<script>
    function aiCounselorAssistant() {
        return {
            promptInput: '',
            isGenerating: false,
            aiResponse: null,

            async handleGenerate() {
                if (!this.promptInput.trim()) return;

                this.isGenerating = true;
                this.aiResponse = null;

                try {
                    const res = await fetch('{{ route("counselor.ai-assistant.generate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            userPrompt: this.promptInput,
                            role: '{{ $userRole }}',
                            dassScores: @json($dassScores),
                        }),
                    });

                    const data = await res.json();
                    this.aiResponse = data.aiResponse || 'Asisten AI siap membantu.';
                } catch (err) {
                    console.error(err);
                    this.aiResponse = 'Gagal terhubung dengan layanan AI.';
                } finally {
                    this.isGenerating = false;
                }
            }
        };
    }
</script>

{{-- Re-initialize Lucide icons after Alpine renders --}}
<script>
    document.addEventListener('alpine:initialized', () => lucide.createIcons());
</script>
@endsection
