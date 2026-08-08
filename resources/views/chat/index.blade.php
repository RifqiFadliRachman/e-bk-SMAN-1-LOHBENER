@extends('layouts.app')

@section('title', 'Chat Privat Terenkripsi AES-256 - E-BK SMAN 1 Lohbener')

@section('content')
<div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xs overflow-hidden flex flex-col md:flex-row h-[650px]">
    <!-- Left Contacts Column -->
    <div class="w-full md:w-80 border-r border-[#E6E2D3] bg-[#FDFCF8] flex flex-col">
        <div class="p-4 border-b border-[#E6E2D3] bg-[#5A6D58] text-white">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-sm flex items-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4 text-[#A2C49F]"></i> Kontak Privat BK
                </h3>
                <span class="text-[10px] bg-[#8BA888]/30 text-[#E8F0E7] font-mono px-2 py-0.5 rounded-full border border-[#8BA888]/40">
                    AES-256 E2EE
                </span>
            </div>
            <p class="text-[11px] text-[#D8E2D7] mt-1">Sistem Konseling Kerahasiaan Terjamin</p>
        </div>

        <div class="p-3 space-y-2 overflow-y-auto flex-1">
            @foreach($contacts as $contact)
                <a href="{{ route('chat.index', ['recipient_id' => $contact->id]) }}" class="p-3 rounded-2xl flex items-center gap-3 border transition-all {{ $selectedRecipientId == $contact->id ? 'bg-[#5A6D58] text-white' : 'bg-white text-[#5C564B] hover:bg-[#F5F2EB] border-[#E6E2D3]' }}">
                    <div class="w-10 h-10 rounded-full bg-[#8BA888] text-white flex items-center justify-center font-bold">
                        {{ substr($contact->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold truncate">{{ $contact->name }}</div>
                        <div class="text-[11px] opacity-75 truncate">{{ $contact->role == 'guru_bk' ? 'Konselor BK' : 'Siswa SMAN 1 Lohbener' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Right Active Chat View -->
    <div class="flex-1 flex flex-col bg-white">
        <!-- Chat Header -->
        <div class="p-4 border-b border-[#E6E2D3] flex items-center justify-between bg-[#FDFCF8]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#5A6D58] text-white flex items-center justify-center font-bold border-2 border-[#8BA888]">
                    {{ substr($activeRecipient->name ?? 'K', 0, 1) }}
                </div>
                <div>
                    <h4 class="font-bold text-sm text-[#5A6D58] flex items-center gap-2">
                        {{ $activeRecipient->name ?? 'Guru Konselor BK' }}
                        <span class="w-2 h-2 rounded-full bg-[#8BA888] inline-block animate-pulse"></span>
                    </h4>
                    <p class="text-[11px] text-[#A39B8B]">Terproteksi Kodeks Etik Kerahasiaan SMAN 1 Lohbener</p>
                </div>
            </div>
        </div>

        <!-- Messages Stream -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#FDFCF8]/60">
            <div class="text-center py-2">
                <span class="inline-flex items-center gap-1 text-[11px] text-[#A39B8B] bg-[#F5F2EB] border border-[#E6E2D3] px-3 py-1 rounded-full font-mono">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#8BA888]"></i> Terenkripsi dengan Laravel Crypt (AES-256-CBC)
                </span>
            </div>

            @foreach($messages as $msg)
                <div class="flex flex-col {{ $msg->sender_id == (Auth::id() ?? 1) ? 'items-end' : 'items-start' }}">
                    <div class="max-w-md p-3.5 rounded-2xl text-xs space-y-1 shadow-xs {{ $msg->sender_id == (Auth::id() ?? 1) ? 'bg-[#5A6D58] text-white rounded-br-none' : 'bg-[#F5F2EB] text-[#333D32] border border-[#E6E2D3] rounded-bl-none' }}">
                        <div class="font-bold text-[10px] opacity-80 flex justify-between gap-4">
                            <span>{{ $msg->sender_id == (Auth::id() ?? 1) ? 'Anda' : ($activeRecipient->name ?? 'Pengirim') }}</span>
                            <span>{{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}</span>
                        </div>
                        <p class="text-xs leading-relaxed font-normal">
                            {{ Crypt::decryptString($msg->encrypted_payload) }}
                        </p>
                    </div>
                    <span class="text-[10px] text-[#A39B8B] mt-1">AES-256 Encrypted</span>
                </div>
            @endforeach
        </div>

        <!-- Chat Form -->
        <form action="{{ route('chat.send') }}" method="POST" class="p-3 border-t border-[#E6E2D3] bg-white flex items-center gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $activeRecipient->id ?? '' }}">
            <input type="text" name="message" placeholder="Tuliskan pesan konsultasi privat terenkripsi..." class="flex-1 text-xs p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none focus:ring-2 focus:ring-[#8BA888] font-medium text-[#333D32]" required>
            <button type="submit" class="bg-[#5A6D58] hover:bg-[#4E614C] text-white p-3 rounded-xl font-bold shadow-xs">
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </div>
</div>
@endsection
