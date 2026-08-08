@extends('layouts.app')

@section('title', 'Booking Konseling - E-BK SMAN 1 Lohbener')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm relative overflow-hidden border border-[#4A5D48] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold mb-2 border border-[#8BA888]/40">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#A2C49F]"></i> JADWAL KONSULTASI LOHBENER
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Booking Jadwal Sesi Konseling BK
            </h2>
            <p class="text-[#D8E2D7] text-xs mt-1">
                Jadwalkan konsultasi tatap muka langsung di Ruang BK SMAN 1 Lohbener atau sesi online privat.
            </p>
        </div>

        <button onclick="document.getElementById('bookingModal').classList.remove('hidden')" class="bg-[#8BA888] hover:bg-[#7A9777] text-white font-bold text-xs px-5 py-3 rounded-xl shadow-xs transition-all flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Ajukan Konseling Baru
        </button>
    </div>

    <!-- Bookings List Table -->
    <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xs overflow-hidden">
        <div class="p-4 border-b border-[#E6E2D3] bg-[#FDFCF8] flex items-center justify-between">
            <h3 class="font-bold text-sm text-[#5A6D58]">Daftar Pengajuan & Status Konseling</h3>
            <span class="text-xs text-[#A39B8B]">Total {{ count($bookings) }} Sesi Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F5F2EB] text-[#5A6D58] text-xs font-bold uppercase tracking-wider border-b border-[#E6E2D3]">
                        <th class="p-4">Tanggal & Waktu</th>
                        <th class="p-4">Pemohon / Siswa</th>
                        <th class="p-4">Konselor BK</th>
                        <th class="p-4">Jenis Sesi</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E2D3] text-xs">
                    @forelse($bookings as $b)
                        <tr class="hover:bg-[#FDFCF8] transition-all">
                            <td class="p-4 font-semibold text-[#333D32]">
                                {{ \Carbon\Carbon::parse($b->tanggal_sesi)->format('d M Y') }} • {{ $b->jam_sesi }} WIB
                            </td>
                            <td class="p-4 font-bold text-[#5A6D58]">
                                {{ $b->student_name }} <span class="text-[10px] text-[#A39B8B]">({{ $b->kelas }})</span>
                            </td>
                            <td class="p-4 text-[#5C564B]">{{ $b->counselor_name }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $b->jenis_sesi == 'Tatap Muka' ? 'bg-[#F5F8F5] text-[#5A6D58] border-[#8BA888]' : 'bg-[#FDF8F6] text-[#E07A5F] border-[#E07A5F]/40' }}">
                                    {{ $b->jenis_sesi }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($b->status == 'Disetujui')
                                    <span class="bg-[#E8F0E7] text-[#5A6D58] px-2.5 py-1 rounded-full font-bold text-[11px] border border-[#8BA888]">Disetujui</span>
                                @elseif($b->status == 'Menunggu Approval')
                                    <span class="bg-[#FEF3C7] text-[#92400E] px-2.5 py-1 rounded-full font-bold text-[11px] border border-[#FCD34D]">Menunggu BK</span>
                                @else
                                    <span class="bg-[#FEE2E2] text-[#991B1B] px-2.5 py-1 rounded-full font-bold text-[11px] border border-[#F87171]">Ditolak/Selesai</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <a href="{{ route('chat.index') }}" class="text-[#8BA888] hover:underline font-bold">Chat Konselor</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#A39B8B]">Belum ada data pengajuan booking konseling.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Booking Form -->
<div id="bookingModal" class="hidden fixed inset-0 z-50 bg-[#333D32]/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-[#E6E2D3] space-y-4">
        <div class="flex items-center justify-between border-b pb-3 border-[#F5F2EB]">
            <h3 class="font-bold text-base text-[#5A6D58]">Formulir Booking Konseling</h3>
            <button onclick="document.getElementById('bookingModal').classList.add('hidden')" class="text-[#A39B8B] hover:text-[#5A6D58] font-bold">✕</button>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">Pilih Guru Konselor BK:</label>
                <select name="counselor_id" class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32]" required>
                    @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}">{{ $counselor->name }} (Konselor BK)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#5A6D58] mb-1">Tanggal Konseling:</label>
                    <input type="date" name="tanggal_sesi" class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]" required />
                </div>
                <div>
                    <label class="block font-bold text-[#5A6D58] mb-1">Jam Sesi:</label>
                    <select name="jam_sesi" class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]">
                        <option value="09:00">09:00 - 10:00 WIB</option>
                        <option value="11:00">11:00 - 12:00 WIB</option>
                        <option value="13:30">13:30 - 14:30 WIB</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">Metode Konseling:</label>
                <select name="jenis_sesi" class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]">
                    <option value="Tatap Muka">Tatap Muka (Ruang BK SMAN 1 Lohbener)</option>
                    <option value="Chat Online">Chat Online Terenkripsi (In-App)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#5A6D58] mb-1">Catatan Singkat Topik Permasalahan:</label>
                <textarea name="topik" rows="3" placeholder="Jelaskan secara singkat kendala atau hal yang ingin dikonsultasikan..." class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('bookingModal').classList.add('hidden')" class="px-4 py-2.5 font-bold text-[#5C564B] bg-[#F5F2EB] rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2.5 font-bold text-white bg-[#5A6D58] hover:bg-[#4E614C] rounded-xl shadow-xs">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endsection
