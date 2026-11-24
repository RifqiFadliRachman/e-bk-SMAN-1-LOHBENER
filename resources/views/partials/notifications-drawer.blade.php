{{-- Notification Dropdown Popover (Compact & Functional) --}}
<div
    id="notificationDropdown"
    class="hidden absolute right-0 mt-2 bg-white border border-[#E6E2D3] rounded-2xl shadow-xl overflow-hidden z-50 transition-all"
    style="width: 350px; max-width: calc(100vw - 2rem);"
>
    {{-- Header --}}
    <div class="px-4 py-3 bg-[#5A6D58] text-white flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i data-lucide="bell" class="w-4 h-4 text-[#A2C49F]"></i>
            <h3 class="font-bold text-xs sm:text-sm">Notifikasi</h3>
            @if(($unreadNotificationsCount ?? 0) > 0)
                <span class="bg-[#E07A5F] text-white text-[10px] font-bold px-2 py-0.5 rounded-full leading-none">
                    {{ $unreadNotificationsCount }} Baru
                </span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if(count($notifications ?? []) > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[11px] text-[#E8F0E7] hover:text-white underline font-medium" title="Tandai semua telah dibaca">
                        Tandai Dibaca
                    </button>
                </form>
            @endif
            <button type="button" onclick="toggleNotificationDropdown()" class="p-1 text-[#D8E2D7] hover:text-white rounded-lg transition-colors" aria-label="Tutup Notifikasi">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="max-h-80 overflow-y-auto divide-y divide-[#E6E2D3] bg-[#FDFCF8]">
        @forelse($notifications ?? [] as $notif)
            @php
                $bgClass = match($notif->type) {
                    'reschedule' => 'bg-amber-50/80 hover:bg-amber-100/60',
                    'approved'   => 'bg-emerald-50/80 hover:bg-emerald-100/60',
                    'chat'       => 'bg-purple-50/60 hover:bg-purple-100/50',
                    'urgent'     => 'bg-red-50/70 hover:bg-red-100/50',
                    'booking'    => 'bg-blue-50/60 hover:bg-blue-100/50',
                    default      => 'bg-white hover:bg-[#F5F2EB]',
                };
            @endphp
            <a
                href="{{ $notif->link ?? '#' }}"
                class="block p-3.5 transition-all border-l-4 {{ $notif->border_color ?? 'border-l-gray-300' }} {{ $notif->is_read ? 'opacity-70 bg-white hover:bg-[#F5F2EB]' : $bgClass }}"
            >
                <div class="flex items-start gap-2.5">
                    {{-- Icon Kolom Kiri --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if($notif->type === 'reschedule')
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center border border-amber-300 shadow-xs">
                                <i data-lucide="calendar-clock" class="w-4 h-4"></i>
                            </div>
                        @elseif($notif->type === 'approved' || $notif->type === 'success')
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center border border-emerald-300 shadow-xs">
                                <i data-lucide="calendar-check" class="w-4 h-4"></i>
                            </div>
                        @elseif($notif->type === 'chat')
                            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center border border-purple-300 shadow-xs">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </div>
                        @elseif($notif->type === 'done')
                            <div class="w-8 h-8 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center border border-[#8BA888]/40 shadow-xs">
                                <i data-lucide="check-check" class="w-4 h-4"></i>
                            </div>
                        @elseif($notif->type === 'urgent')
                            <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center border border-red-300 shadow-xs">
                                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                            </div>
                        @elseif($notif->type === 'booking')
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center border border-blue-200 shadow-xs">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center border border-gray-300 shadow-xs">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Konten Notifikasi --}}
                    <div class="flex-1 min-w-0">
                        {{-- Baris Badge Kategori & Waktu --}}
                        <div class="flex items-center justify-between gap-1 mb-1">
                            @if(isset($notif->badge_label))
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full border shadow-2xs {{ $notif->badge_color ?? 'bg-gray-100 text-gray-700 border-gray-300' }}">
                                    @if($notif->type === 'reschedule')
                                        <i data-lucide="clock" class="w-2.5 h-2.5"></i>
                                    @elseif($notif->type === 'approved' || $notif->type === 'success')
                                        <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                    @elseif($notif->type === 'chat')
                                        <i data-lucide="message-circle" class="w-2.5 h-2.5"></i>
                                    @elseif($notif->type === 'done')
                                        <i data-lucide="check-check" class="w-2.5 h-2.5"></i>
                                    @elseif($notif->type === 'urgent')
                                        <i data-lucide="alert-circle" class="w-2.5 h-2.5"></i>
                                    @else
                                        <i data-lucide="calendar" class="w-2.5 h-2.5"></i>
                                    @endif
                                    {{ $notif->badge_label }}
                                </span>
                            @endif

                            <div class="flex items-center gap-1.5 ml-auto">
                                <span class="text-[10px] text-[#A39B8B]">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </span>
                                @if(!$notif->is_read)
                                    <span class="w-2 h-2 rounded-full bg-[#E07A5F] flex-shrink-0" title="Belum dibaca"></span>
                                @endif
                            </div>
                        </div>

                        {{-- Judul Notifikasi --}}
                        <h4 class="font-bold text-xs leading-snug {{ $notif->type === 'reschedule' ? 'text-amber-950 font-extrabold' : ($notif->type === 'approved' ? 'text-emerald-950 font-extrabold' : ($notif->type === 'chat' ? 'text-purple-950' : 'text-[#333D32]')) }}">
                            {{ $notif->title }}
                        </h4>

                        {{-- Isi Pesan --}}
                        <p class="text-[11px] text-[#5C564B] mt-1 line-clamp-2 leading-relaxed">
                            {{ $notif->message }}
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-8 text-center text-xs text-[#A39B8B]">
                <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 opacity-30 text-[#5A6D58]"></i>
                <p class="font-semibold text-[#5C564B]">Belum ada notifikasi baru</p>
                <p class="text-[11px] text-[#A39B8B] mt-0.5">Semua pembaruan aktivitas akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</div>
