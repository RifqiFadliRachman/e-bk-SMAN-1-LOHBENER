@extends('layouts.app')

@section('title', 'Profil Siswa - E-BK SMAN 1 Lohbener')

@section('content')
<div x-data="studentProfile()" class="space-y-6">

    {{-- Header + Back Button --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('counselor.dashboard') }}" class="bg-[#F5F2EB] hover:bg-[#E6E2D3] text-[#5A6D58] font-bold text-xs px-3.5 py-2 rounded-xl border border-[#E6E2D3] flex items-center gap-1.5 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
        <h2 class="text-lg font-bold text-[#5A6D58]">Detail Profil Siswa</h2>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-xs space-y-6">

        {{-- Student Identity Header --}}
        <div class="flex items-center justify-between border-b pb-4 border-[#F5F2EB]">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-[#5A6D58] text-[#FDFCF8] flex items-center justify-center font-bold text-lg border border-[#4A5D48]">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-[#5A6D58]">{{ $student->name }}</h3>
                        <span class="bg-[#F5F2EB] text-[#5A6D58] text-xs px-2.5 py-0.5 rounded-full font-bold border border-[#E6E2D3] flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#8BA888]"></i> Enkripsi Aktif
                        </span>
                    </div>
                    <p class="text-xs text-[#A39B8B] mt-0.5">
                        NISN: <strong>{{ $student->nisn ?? '-' }}</strong> • Kelas: <strong>{{ $student->kelas }}</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- Security Alert Banner --}}
        <div class="bg-[#5A6D58] text-[#FDFCF8] p-4 rounded-2xl text-xs space-y-2 border border-[#4A5D48]">
            <div class="flex items-center justify-between">
                <span class="font-bold text-[#A2C49F] flex items-center gap-1.5">
                    <i data-lucide="lock" class="w-4 h-4"></i> Mode Perlindungan Data Sensitif Siswa
                </span>
                <button
                    @click="isDecryptedView = !isDecryptedView"
                    class="bg-[#8BA888] hover:bg-[#7A9777] text-white font-bold px-3 py-1 rounded-xl text-[11px] transition-all flex items-center gap-1"
                >
                    <template x-if="isDecryptedView"><span class="flex items-center gap-1"><i data-lucide="lock" class="w-3 h-3"></i> Kunci Kembali</span></template>
                    <template x-if="!isDecryptedView"><span class="flex items-center gap-1"><i data-lucide="unlock" class="w-3 h-3"></i> Dekripsi Data Sensitif</span></template>
                </button>
            </div>
            <p class="text-[#D8E2D7] text-[11px] leading-relaxed">
                Sesuai asas kerahasiaan BK SMAN 1 Lohbener, data sensitif disimpan dalam format ciphertext AES-256.
            </p>
        </div>

        {{-- View Mode (non-edit) --}}
        <div x-show="!isEditing" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 bg-[#FDFCF8] rounded-2xl border border-[#E6E2D3]">
                    <span class="text-[#A39B8B] font-medium">Alamat Tinggal:</span>
                    <p class="font-semibold text-[#333D32] mt-1">{{ $student->alamat ?? '-' }}</p>
                </div>
                <div class="p-3 bg-[#FDFCF8] rounded-2xl border border-[#E6E2D3]">
                    <span class="text-[#A39B8B] font-medium">Nama Orang Tua / Wali:</span>
                    <p class="font-semibold text-[#333D32] mt-1">{{ $student->nama_orang_tua ?? '-' }}</p>
                </div>
                <div class="p-3 bg-[#FDFCF8] rounded-2xl border border-[#E6E2D3]">
                    <span class="text-[#A39B8B] font-medium">Kontak Darurat Orang Tua:</span>
                    <p class="font-semibold text-[#333D32] mt-1">
                        <span x-show="isDecryptedView">{{ $student->kontak_darurat ?? '-' }}</span>
                        <span x-show="!isDecryptedView">{{ $maskedKontak }}</span>
                    </p>
                </div>
                <div class="p-3 bg-[#FDFCF8] rounded-2xl border border-[#E6E2D3]">
                    <span class="text-[#A39B8B] font-medium">Guru BK Pendamping:</span>
                    <p class="font-semibold text-[#333D32] mt-1">{{ $counselorName ?? 'Dra. Hani Nuraeni, M.Pd' }}</p>
                </div>
            </div>

            {{-- Encrypted Notes Block --}}
            <div class="p-4 rounded-2xl border border-[#8BA888]/40 bg-[#F5F8F5] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-[#5A6D58] flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-4 h-4 text-[#8BA888]"></i> Catatan Khusus &amp; Riwayat Psikologis
                    </span>
                    <span class="text-[10px] font-mono bg-[#E8F0E7] text-[#5A6D58] px-2 py-0.5 rounded-full font-bold"
                          x-text="isDecryptedView ? 'STATUS: DECRYPTED' : 'STATUS: ENCRYPTED'">
                    </span>
                </div>

                <template x-if="isDecryptedView">
                    <p class="text-[#333D32] font-medium bg-white p-3 rounded-xl border border-[#E6E2D3] leading-relaxed">
                        "{{ $decryptedNote ?? 'Tidak ada catatan khusus.' }}"
                    </p>
                </template>
                <template x-if="!isDecryptedView">
                    <div class="bg-[#333D32] text-[#FCD34D] font-mono text-[11px] p-3 rounded-xl border border-[#435441] break-all">
                        🔒 {{ $encryptedNote ?? 'AES256::[No Record]' }}
                    </div>
                </template>
            </div>

            {{-- DASS-21 Status --}}
            @if($latestDass)
            <div class="p-4 rounded-2xl border border-[#E6E2D3] bg-[#FDFCF8] space-y-2">
                <span class="font-bold text-[#5A6D58]">Status Mental Terakhir (DASS-21)</span>
                <div class="grid grid-cols-3 gap-2 text-center pt-1">
                    <div class="p-2 bg-white rounded-xl border border-[#E6E2D3]">
                        <div class="text-[10px] text-[#A39B8B]">Depresi</div>
                        <div class="font-bold text-[#5A6D58] mt-0.5">{{ $latestDass->depression_category }} ({{ $latestDass->depression_score }})</div>
                    </div>
                    <div class="p-2 bg-white rounded-xl border border-[#E6E2D3]">
                        <div class="text-[10px] text-[#A39B8B]">Kecemasan</div>
                        <div class="font-bold text-[#5A6D58] mt-0.5">{{ $latestDass->anxiety_category }} ({{ $latestDass->anxiety_score }})</div>
                    </div>
                    <div class="p-2 bg-white rounded-xl border border-[#E6E2D3]">
                        <div class="text-[10px] text-[#A39B8B]">Stres</div>
                        <div class="font-bold text-[#5A6D58] mt-0.5">{{ $latestDass->stress_category }} ({{ $latestDass->stress_score }})</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-end gap-2 pt-2">
                <button
                    @click="isEditing = true"
                    class="bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold text-xs px-4 py-2 rounded-xl transition-all"
                >
                    Edit &amp; Update Enkripsi
                </button>
            </div>
        </div>

        {{-- Edit Mode Form --}}
        <form x-show="isEditing" action="{{ route('counselor.student-profile.update', $student->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">Alamat Tinggal</label>
                <input type="text" name="alamat" value="{{ $student->alamat ?? '' }}" class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]">
            </div>
            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">Nama Orang Tua</label>
                <input type="text" name="nama_orang_tua" value="{{ $student->nama_orang_tua ?? '' }}" class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]">
            </div>
            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">Kontak Darurat Orang Tua</label>
                <input type="text" name="kontak_darurat" value="{{ $student->kontak_darurat ?? '' }}" class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]">
            </div>
            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">
                    Catatan Khusus Sensitif (Otomatis Dienkripsi AES-256)
                </label>
                <textarea name="catatan_sensitif" rows="3" class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]">{{ $decryptedNote ?? '' }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="isEditing = false" class="px-4 py-2 font-bold text-[#5C564B] bg-[#F5F2EB] rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 font-bold text-white bg-[#5A6D58] hover:bg-[#4E614C] rounded-xl shadow-xs">
                    Simpan &amp; Re-Encrypt
                </button>
            </div>
        </form>

    </div>

</div>

<script>
    function studentProfile() {
        return {
            isDecryptedView: false,
            isEditing: false,
        };
    }
</script>
<script>
    document.addEventListener('alpine:initialized', () => lucide.createIcons());
</script>
@endsection
