{{-- Notifications Drawer (included in layout, controlled by Alpine.js) --}}
<div
    x-data="{ open: false }"
    @open-notifications.window="open = true"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#333D32]/60 backdrop-blur-xs" @click="open = false"></div>

    {{-- Drawer Panel --}}
    <div
        class="absolute right-0 top-0 bg-white w-full max-w-md h-full shadow-2xl flex flex-col"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
    >
        {{-- Drawer Header --}}
        <div class="p-4 border-b border-[#4A5D48] bg-[#5A6D58] text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="bell" class="w-5 h-5 text-[#A2C49F]"></i>
                <h3 class="font-bold text-sm">Notifikasi Real-time BK</h3>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-[#E8F0E7] hover:text-white underline font-medium">
                        Tandai Semua Dibaca
                    </button>
                </form>
                <button @click="open = false" class="p-1 text-[#D8E2D7] hover:text-white font-bold">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Notifications Stream --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#FDFCF8]">
            @forelse($notifications ?? [] as $notif)
                <div class="p-4 rounded-2xl border transition-all cursor-pointer space-y-1.5 {{ $notif->is_read ? 'bg-[#F5F2EB] border-[#E6E2D3] opacity-80' : 'bg-white border-[#8BA888] shadow-xs ring-1 ring-[#8BA888]/30' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            @if($notif->type === 'urgent')
                                <i data-lucide="shield-alert" class="w-4 h-4 text-[#E07A5F] flex-shrink-0"></i>
                            @elseif($notif->type === 'booking')
                                <i data-lucide="calendar" class="w-4 h-4 text-[#8BA888] flex-shrink-0"></i>
                            @else
                                <i data-lucide="message-square" class="w-4 h-4 text-[#5A6D58] flex-shrink-0"></i>
                            @endif
                            <h4 class="font-bold text-xs text-[#5A6D58] leading-snug">{{ $notif->title }}</h4>
                        </div>
                        @if(!$notif->is_read)
                            <span class="w-2 h-2 rounded-full bg-[#8BA888] flex-shrink-0 mt-1"></span>
                        @endif
                    </div>

                    <p class="text-xs text-[#5C564B] leading-relaxed">{{ $notif->message }}</p>
                    <span class="text-[10px] text-[#A39B8B] block pt-1">
                        {{ \Carbon\Carbon::parse($notif->created_at)->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>
            @empty
                <div class="p-12 text-center text-xs text-[#A39B8B]">
                    Belum ada notifikasi baru.
                </div>
            @endforelse
        </div>
    </div>
</div>
