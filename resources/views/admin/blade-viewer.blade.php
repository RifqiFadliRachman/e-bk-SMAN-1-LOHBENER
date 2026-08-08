@extends('layouts.app')

@section('title', 'Kode Sumber Laravel Blade - E-BK SMAN 1 Lohbener')

@section('content')
<div x-data="bladeViewer()" class="space-y-6">

    {{-- Header Banner --}}
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm relative overflow-hidden border border-[#4A5D48] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold mb-2 border border-[#8BA888]/40">
                <i data-lucide="code" class="w-3.5 h-3.5 text-[#A2C49F]"></i> LARAVEL BLADE ARCHITECTURE
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Kode Sumber Laravel Blade &amp; Structure
            </h2>
            <p class="text-[#D8E2D7] text-xs mt-1 leading-relaxed">
                Seluruh komponen E-BK SMAN 1 Lohbener dalam format <strong>Laravel Blade (.blade.php)</strong>, Controller PHP, dan Route definitions.
            </p>
        </div>

        <div class="flex items-center gap-2 bg-[#4A5D48] p-2 rounded-2xl border border-[#8BA888]/30">
            <i data-lucide="server" class="w-4 h-4 text-[#A2C49F]"></i>
            <span class="text-xs font-mono font-bold text-white">Laravel 11.x • PHP 8.2+</span>
        </div>
    </div>

    {{-- Code Browser Grid --}}
    <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xs overflow-hidden grid grid-cols-1 md:grid-cols-4">

        {{-- Left Sidebar File List --}}
        <div class="p-4 bg-[#FDFCF8] border-r border-[#E6E2D3] space-y-2">
            <div class="text-[11px] font-bold text-[#A39B8B] uppercase tracking-wider px-2 mb-2 flex items-center justify-between">
                <span>Struktur File Laravel</span>
                <i data-lucide="database" class="w-3.5 h-3.5"></i>
            </div>

            @foreach($bladeFiles as $key => $file)
                <button
                    @click="selectedFile = '{{ $key }}'"
                    :class="selectedFile === '{{ $key }}' ? 'bg-[#5A6D58] text-white shadow-xs' : 'bg-white text-[#5C564B] hover:bg-[#F5F2EB] border border-[#E6E2D3]'"
                    class="w-full text-left p-3 rounded-2xl text-xs font-bold transition-all flex items-center gap-2.5"
                >
                    <i data-lucide="file-code" class="w-4 h-4 flex-shrink-0"></i>
                    <div class="truncate min-w-0">
                        <div>{{ $file['title'] }}</div>
                        <div class="text-[10px] font-mono opacity-70 truncate">{{ $file['path'] }}</div>
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Right Code Display Area --}}
        <div class="md:col-span-3 flex flex-col bg-[#1E241E] text-[#E8F0E7]">
            {{-- Code Header Bar --}}
            <div class="p-3 bg-[#2A332A] border-b border-[#3E4F3C] flex items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#E07A5F]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#FCD34D]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#8BA888]"></span>
                    <span class="text-xs font-mono text-[#D8E2D7] font-semibold ml-2" x-text="files[selectedFile]?.path || ''"></span>
                </div>

                <button
                    @click="copyCode()"
                    class="bg-[#5A6D58] hover:bg-[#4A5D48] text-white font-bold text-xs px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5"
                >
                    <template x-if="copied"><span class="flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5 text-[#A2C49F]"></i> Tersalin!</span></template>
                    <template x-if="!copied"><span class="flex items-center gap-1"><i data-lucide="copy" class="w-3.5 h-3.5"></i> Salin Kode</span></template>
                </button>
            </div>

            {{-- Code Body --}}
            <div class="p-4 overflow-x-auto max-h-[550px] font-mono text-xs leading-relaxed">
                <pre class="text-[#A2C49F]"><code x-text="files[selectedFile]?.code || 'Pilih file dari sidebar.'"></code></pre>
            </div>
        </div>
    </div>

    {{-- Guide Box --}}
    <div class="p-5 rounded-3xl bg-[#F5F8F5] border border-[#8BA888]/40 space-y-2">
        <h4 class="font-bold text-sm text-[#5A6D58] flex items-center gap-2">
            <i data-lucide="external-link" class="w-4 h-4 text-[#8BA888]"></i>
            Cara Menjalankan di Project Laravel PHP Kamu:
        </h4>
        <ol class="text-xs text-[#5C564B] space-y-1 list-decimal list-inside font-medium leading-relaxed">
            <li>Salin file <code>app/Http/Controllers/BKController.php</code> dan <code>routes/web.php</code> ke project Laravel.</li>
            <li>Buat folder <code>resources/views/layouts</code>, <code>dass21</code>, <code>booking</code>, <code>chat</code>, dan <code>counselor</code>.</li>
            <li>Simpan template <code>.blade.php</code> sesuai path yang tertera.</li>
            <li>Jalankan <code>php artisan serve</code> untuk membuka aplikasi (http://127.0.0.1:8000).</li>
        </ol>
    </div>

</div>

<script>
    function bladeViewer() {
        return {
            selectedFile: 'layout',
            copied: false,
            files: @json($bladeFiles),

            copyCode() {
                const code = this.files[this.selectedFile]?.code || '';
                navigator.clipboard.writeText(code);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        };
    }
</script>
<script>
    document.addEventListener('alpine:initialized', () => lucide.createIcons());
</script>
@endsection
