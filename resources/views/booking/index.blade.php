@extends('layouts.app')

@section('title', 'Jadwal Konseling - E-BK SMAN 1 Lohbener')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-xl font-bold text-[#333D32]">
                Jadwal Konseling
            </h1>

            @if(Auth::check() && Auth::user()->role === 'guru_bk')
                <p class="text-xs text-[#A39B8B] mt-1">
                    Kelola pengajuan dan jadwal konseling siswa.
                </p>
            @else
                <p class="text-xs text-[#A39B8B] mt-1">
                    Ajukan jadwal konseling dengan Guru BK.
                </p>
            @endif
        </div>


        {{-- Tombol Booking hanya untuk siswa --}}
        @if(Auth::check() && Auth::user()->role === 'siswa')

            <button
                type="button"
                onclick="openBookingModal()"
                class="bg-[#8BA888] hover:bg-[#7A9777] text-white font-bold text-xs px-5 py-3 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Ajukan Konseling Baru
            </button>

        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="bg-[#E8F0E7] border border-[#8BA888] text-[#5A6D58] rounded-xl p-4 text-xs">

            <div class="flex items-center gap-2">

                <i data-lucide="check-circle" class="w-4 h-4"></i>

                <span class="font-bold">
                    {{ session('success') }}
                </span>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERROR MESSAGE --}}
    {{-- ========================================================= --}}

    @if(session('error'))

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-xs">

            <div class="flex items-center gap-2">

                <i data-lucide="alert-circle" class="w-4 h-4"></i>

                <span class="font-bold">
                    {{ session('error') }}
                </span>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-xs">

            <div class="font-bold mb-2 flex items-center gap-2">

                <i data-lucide="alert-triangle" class="w-4 h-4"></i>

                <span>
                    Terjadi kesalahan.
                </span>

            </div>

            <ul class="list-disc list-inside space-y-1">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- DAFTAR BOOKING --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="p-5 border-b border-[#E6E2D3] bg-[#FDFCF8] flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <div>

                <h3 class="font-bold text-sm text-[#5A6D58]">
                    Daftar Pengajuan Konseling
                </h3>

                @if(Auth::check() && Auth::user()->role === 'guru_bk')

                    <p class="text-[11px] text-[#A39B8B] mt-1">
                        Kelola status dan jadwal konseling siswa.
                    </p>

                @else

                    <p class="text-[11px] text-[#A39B8B] mt-1">
                        Pantau pengajuan dan status konseling Anda.
                    </p>

                @endif

            </div>


            <span class="text-xs text-[#A39B8B]">

                Total
                <span class="font-bold text-[#5A6D58]">
                    {{ $bookings->count() }}
                </span>
                Sesi

            </span>

        </div>


        {{-- ========================================================= --}}
        {{-- TABLE --}}
        {{-- ========================================================= --}}

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse min-w-[1100px]">

                <thead>

                    <tr class="bg-[#F5F2EB] text-[#5A6D58] text-xs font-bold uppercase tracking-wider border-b border-[#E6E2D3]">

                        <th class="p-4">
                            Tanggal & Waktu
                        </th>

                        <th class="p-4">
                            Pemohon / Siswa
                        </th>

                        <th class="p-4">
                            Konselor BK
                        </th>

                        <th class="p-4">
                            Jenis Sesi
                        </th>

                        <th class="p-4">
                            Topik
                        </th>

                        <th class="p-4">
                            Status
                        </th>

                        <th class="p-4">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#E6E2D3] text-xs">

                    @forelse($bookings as $b)

                        <tr class="hover:bg-[#FDFCF8] transition-all">

                            {{-- ================================================= --}}
                            {{-- TANGGAL & JAM --}}
                            {{-- ================================================= --}}

                            <td class="p-4">

                                <div class="font-bold text-[#333D32]">

                                    {{ \Carbon\Carbon::parse($b->tanggal_sesi)->format('d M Y') }}

                                </div>

                                <div class="text-[#A39B8B] mt-1">

                                    <i
                                        data-lucide="clock"
                                        class="w-3 h-3 inline-block mr-1"
                                    ></i>

                                    {{ \Carbon\Carbon::parse($b->jam_sesi)->format('H:i') }}
                                    WIB

                                </div>

                            </td>


                            {{-- ================================================= --}}
                            {{-- SISWA --}}
                            {{-- ================================================= --}}

                            <td class="p-4">

                                <div class="font-bold text-[#5A6D58]">

                                    {{ $b->student_name }}

                                </div>


                                @if(!empty($b->kelas))

                                    <div class="text-[10px] text-[#A39B8B] mt-1">

                                        Kelas {{ $b->kelas }}

                                    </div>

                                @endif

                            </td>


                            {{-- ================================================= --}}
                            {{-- KONSELOR --}}
                            {{-- ================================================= --}}

                            <td class="p-4 text-[#5C564B]">

                                <div class="font-semibold">

                                    {{ $b->counselor_name }}

                                </div>

                                <div class="text-[10px] text-[#A39B8B] mt-1">
                                    Guru BK
                                </div>

                            </td>


                            {{-- ================================================= --}}
                            {{-- JENIS SESI --}}
                            {{-- ================================================= --}}

                            <td class="p-4">

                                @if($b->jenis_sesi === 'Tatap Muka')

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border bg-[#F5F8F5] text-[#5A6D58] border-[#8BA888]">

                                        <i
                                            data-lucide="map-pin"
                                            class="w-3 h-3"
                                        ></i>

                                        Tatap Muka

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border bg-[#FDF8F6] text-[#E07A5F] border-[#E07A5F]/40">

                                        <i
                                            data-lucide="message-circle"
                                            class="w-3 h-3"
                                        ></i>

                                        Chat Online

                                    </span>

                                @endif

                            </td>


                            {{-- ================================================= --}}
                            {{-- TOPIK --}}
                            {{-- ================================================= --}}

                            <td class="p-4 max-w-[220px]">

                                @if(!empty($b->topik))

                                    <div
                                        class="text-[#5C564B] truncate"
                                        title="{{ $b->topik }}"
                                    >
                                        {{ $b->topik }}
                                    </div>

                                @else

                                    <span class="text-[#A39B8B] italic">
                                        Tidak ada topik
                                    </span>

                                @endif

                            </td>


                            {{-- ================================================= --}}
                            {{-- STATUS --}}
                            {{-- ================================================= --}}

                            <td class="p-4">

                                @if($b->status === 'Menunggu Approval')

                                    <span class="inline-flex items-center gap-1 bg-[#FEF3C7] text-[#92400E] px-2.5 py-1 rounded-full font-bold text-[11px] border border-[#FCD34D]">

                                        <i
                                            data-lucide="clock"
                                            class="w-3 h-3"
                                        ></i>

                                        Menunggu BK

                                    </span>


                                @elseif($b->status === 'Diterima')

                                    <span class="inline-flex items-center gap-1 bg-[#E8F0E7] text-[#5A6D58] px-2.5 py-1 rounded-full font-bold text-[11px] border border-[#8BA888]">

                                        <i
                                            data-lucide="check-circle"
                                            class="w-3 h-3"
                                        ></i>

                                        Diterima

                                    </span>


                                @elseif($b->status === 'Penjadwalan')

                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-bold text-[11px] border border-blue-300">

                                        <i
                                            data-lucide="calendar-clock"
                                            class="w-3 h-3"
                                        ></i>

                                        Dijadwalkan

                                    </span>


                                @elseif($b->status === 'Selesai')

                                    <span class="inline-flex items-center gap-1 bg-[#E8F0E7] text-[#5A6D58] px-2.5 py-1 rounded-full font-bold text-[11px] border border-[#8BA888]">

                                        <i
                                            data-lucide="check-check"
                                            class="w-3 h-3"
                                        ></i>

                                        Selesai

                                    </span>


                                @elseif($b->status === 'Ditolak')

                                    <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 px-2.5 py-1 rounded-full font-bold text-[11px] border border-red-300">

                                        <i
                                            data-lucide="x-circle"
                                            class="w-3 h-3"
                                        ></i>

                                        Ditolak

                                    </span>


                                @else

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full font-bold text-[11px] bg-gray-100 text-gray-600 border border-gray-300">

                                        {{ $b->status }}

                                    </span>

                                @endif

                            </td>


                            {{-- ================================================= --}}
                            {{-- AKSI --}}
                            {{-- ================================================= --}}

                            <td class="p-4">

                                {{-- ================================================= --}}
                                {{-- AKSI GURU BK --}}
                                {{-- ================================================= --}}

                                @if(Auth::check() && Auth::user()->role === 'guru_bk')

                                    <div class="flex flex-wrap items-center gap-2">

                                        {{-- ================================ --}}
                                        {{-- MENUNGGU APPROVAL --}}
                                        {{-- ================================ --}}

                                        @if($b->status === 'Menunggu Approval')

                                            {{-- TERIMA --}}
                                            <form
                                                action="{{ route('booking.status.update', $b->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menerima pengajuan konseling ini?')"
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="Diterima"
                                                >

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-[#8BA888] hover:bg-[#7A9777] text-white font-bold text-[11px] transition-all"
                                                >

                                                    <i
                                                        data-lucide="check"
                                                        class="w-3 h-3"
                                                    ></i>

                                                    Terima

                                                </button>

                                            </form>


                                            {{-- TOLAK --}}
                                            <form
                                                action="{{ route('booking.status.update', $b->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan konseling ini?')"
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="Ditolak"
                                                >

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold text-[11px] transition-all"
                                                >

                                                    <i
                                                        data-lucide="x"
                                                        class="w-3 h-3"
                                                    ></i>

                                                    Tolak

                                                </button>

                                            </form>

                                        @endif


                                        {{-- ================================ --}}
                                        {{-- DITERIMA --}}
                                        {{-- ================================ --}}

                                        @if($b->status === 'Diterima')

                                            <button
                                                type="button"
                                                onclick="openScheduleModal(
                                                    {{ $b->id }},
                                                    '{{ $b->tanggal_sesi }}',
                                                    '{{ $b->jam_sesi }}'
                                                )"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition-all"
                                            >

                                                <i
                                                    data-lucide="calendar-plus"
                                                    class="w-3 h-3"
                                                ></i>

                                                Jadwalkan

                                            </button>

                                        @endif


                                        {{-- ================================ --}}
                                        {{-- PENJADWALAN --}}
                                        {{-- ================================ --}}

                                        @if($b->status === 'Penjadwalan')

                                            <button
                                                type="button"
                                                onclick="openScheduleModal(
                                                    {{ $b->id }},
                                                    '{{ $b->tanggal_sesi }}',
                                                    '{{ $b->jam_sesi }}'
                                                )"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition-all"
                                            >

                                                <i
                                                    data-lucide="calendar-clock"
                                                    class="w-3 h-3"
                                                ></i>

                                                Ubah Jadwal

                                            </button>


                                            {{-- SELESAI --}}
                                            <form
                                                action="{{ route('booking.status.update', $b->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Tandai sesi konseling ini sebagai selesai?')"
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="Selesai"
                                                >

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold text-[11px] transition-all"
                                                >

                                                    <i
                                                        data-lucide="check-check"
                                                        class="w-3 h-3"
                                                    ></i>

                                                    Selesai

                                                </button>

                                            </form>

                                        @endif


                                        {{-- ================================ --}}
                                        {{-- SELESAI --}}
                                        {{-- ================================ --}}

                                        @if($b->status === 'Selesai')

                                            <form
                                                action="{{ route('booking.destroy', $b->id) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sesi konseling siswa ini yang sudah selesai? Data yang dihapus tidak dapat dikembalikan.')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#FEE2E2] hover:bg-[#FECACA] text-[#991B1B] border border-[#F87171]/50 rounded-xl font-bold text-[11px] shadow-xs transition-colors"
                                                    title="Hapus riwayat sesi konseling selesai"
                                                >
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>

                                        @endif


                                        {{-- ================================ --}}
                                        {{-- DITOLAK --}}
                                        {{-- ================================ --}}

                                        @if($b->status === 'Ditolak')

                                            <span class="text-[11px] text-red-600 font-bold">
                                                Pengajuan ditolak
                                            </span>

                                        @endif

                                    </div>


                                {{-- ================================================= --}}
                                {{-- AKSI SISWA --}}
                                {{-- ================================================= --}}

                                @else

                                    <div class="flex flex-wrap items-center gap-2">

                                        {{-- CHAT --}}
                                        <a
                                            href="{{ route('chat.index', ['recipient_id' => $b->counselor_id]) }}"
                                            class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-[#8BA888] hover:bg-[#7A9777] text-white font-bold text-[11px] transition-all"
                                        >

                                            <i
                                                data-lucide="message-circle"
                                                class="w-3 h-3"
                                            ></i>

                                            Chat Konselor

                                        </a>


                                        {{-- INFO STATUS --}}
                                        @if($b->status === 'Menunggu Approval')

                                            <span class="text-[10px] text-[#A39B8B]">
                                                Menunggu persetujuan Guru BK
                                            </span>

                                        @elseif($b->status === 'Diterima')

                                            <span class="text-[10px] text-[#5A6D58] font-semibold">
                                                Pengajuan diterima
                                            </span>

                                        @elseif($b->status === 'Penjadwalan')

                                            <span class="text-[10px] text-blue-600 font-semibold">
                                                Jadwal telah ditentukan
                                            </span>

                                        @elseif($b->status === 'Selesai')

                                            <span class="text-[10px] text-[#5A6D58] font-semibold">
                                                Konseling selesai
                                            </span>

                                        @elseif($b->status === 'Ditolak')

                                            <span class="text-[10px] text-red-600 font-semibold">
                                                Pengajuan ditolak
                                            </span>

                                        @endif

                                    </div>

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="p-12 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="w-14 h-14 rounded-2xl bg-[#F5F2EB] flex items-center justify-center mb-3">

                                        <i
                                            data-lucide="calendar-x"
                                            class="w-7 h-7 text-[#A39B8B]"
                                        ></i>

                                    </div>

                                    <p class="font-bold text-sm text-[#5A6D58]">
                                        Belum ada pengajuan konseling
                                    </p>

                                    <p class="text-xs text-[#A39B8B] mt-1">
                                        Data booking konseling akan tampil di sini.
                                    </p>

                                    @if(Auth::check() && Auth::user()->role === 'siswa')

                                        <button
                                            type="button"
                                            onclick="openBookingModal()"
                                            class="mt-4 px-4 py-2 rounded-xl bg-[#8BA888] hover:bg-[#7A9777] text-white text-xs font-bold"
                                        >
                                            Ajukan Konseling
                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MODAL BOOKING SISWA --}}
{{-- ========================================================= --}}

@if(Auth::check() && Auth::user()->role === 'siswa')

<div
    id="bookingModal"
    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 px-4"
>

    <div
        class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto"
    >

        {{-- Header --}}
        <div class="p-5 border-b border-[#E6E2D3] bg-[#FDFCF8] flex items-center justify-between">

            <div>

                <h2 class="font-bold text-[#333D32] text-base">
                    Ajukan Konseling Baru
                </h2>

                <p class="text-[11px] text-[#A39B8B] mt-1">
                    Isi data pengajuan konseling dengan lengkap.
                </p>

            </div>


            <button
                type="button"
                onclick="closeBookingModal()"
                class="w-8 h-8 rounded-lg bg-[#F5F2EB] hover:bg-[#E6E2D3] text-[#5C564B] flex items-center justify-center"
            >

                <i
                    data-lucide="x"
                    class="w-4 h-4"
                ></i>

            </button>

        </div>


        {{-- Body --}}
        <div class="p-5">

            <form
                action="{{ route('booking.store') }}"
                method="POST"
                class="space-y-4 text-xs"
            >

                @csrf


                {{-- Guru BK --}}
                <div>

                    <label
                        for="counselor_id"
                        class="block font-bold text-[#5A6D58] mb-1"
                    >
                        Pilih Guru Konselor BK
                    </label>


                    <select
                        name="counselor_id"
                        id="counselor_id"
                        required
                        class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl outline-none text-[#333D32]"
                    >

                        <option value="">
                            -- Pilih Guru BK --
                        </option>


                        @forelse($counselors as $counselor)

                            <option
                                value="{{ $counselor->id }}"
                                {{ old('counselor_id') == $counselor->id ? 'selected' : '' }}
                            >
                                {{ $counselor->name }} - Guru BK
                            </option>

                        @empty

                            <option
                                value=""
                                disabled
                            >
                                Belum ada Guru BK tersedia
                            </option>

                        @endforelse

                    </select>


                    @error('counselor_id')

                        <p class="text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Tanggal & Jam --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    {{-- Tanggal --}}
                    <div>

                        <label
                            for="tanggal_sesi"
                            class="block font-bold text-[#5A6D58] mb-1"
                        >
                            Tanggal Konseling
                        </label>


                        <input
                            type="date"
                            name="tanggal_sesi"
                            id="tanggal_sesi"
                            min="{{ date('Y-m-d') }}"
                            value="{{ old('tanggal_sesi') }}"
                            required
                            class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]"
                        >


                        @error('tanggal_sesi')

                            <p class="text-red-600 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Jam --}}
                    <div>

                        <label
                            for="jam_sesi"
                            class="block font-bold text-[#5A6D58] mb-1"
                        >
                            Jam Sesi
                        </label>


                        <select
                            name="jam_sesi"
                            id="jam_sesi"
                            required
                            class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]"
                        >

                            <option
                                value="09:00"
                                {{ old('jam_sesi') === '09:00' ? 'selected' : '' }}
                            >
                                09:00 - 10:00 WIB
                            </option>

                            <option
                                value="11:00"
                                {{ old('jam_sesi') === '11:00' ? 'selected' : '' }}
                            >
                                11:00 - 12:00 WIB
                            </option>

                            <option
                                value="13:30"
                                {{ old('jam_sesi') === '13:30' ? 'selected' : '' }}
                            >
                                13:30 - 14:30 WIB
                            </option>

                        </select>


                        @error('jam_sesi')

                            <p class="text-red-600 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                {{-- Jenis Sesi --}}
                <div>

                    <label
                        for="jenis_sesi"
                        class="block font-bold text-[#5A6D58] mb-1"
                    >
                        Metode Konseling
                    </label>


                    <select
                        name="jenis_sesi"
                        id="jenis_sesi"
                        required
                        class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32]"
                    >

                        <option
                            value="Tatap Muka"
                            {{ old('jenis_sesi', 'Tatap Muka') === 'Tatap Muka' ? 'selected' : '' }}
                        >
                            Tatap Muka - Ruang BK
                        </option>

                        <option
                            value="Chat Online"
                            {{ old('jenis_sesi') === 'Chat Online' ? 'selected' : '' }}
                        >
                            Chat Online - In-App
                        </option>

                    </select>


                    @error('jenis_sesi')

                        <p class="text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Topik --}}
                <div>

                    <label
                        for="topik"
                        class="block font-bold text-[#5A6D58] mb-1"
                    >
                        Topik Permasalahan
                    </label>


                    <textarea
                        name="topik"
                        id="topik"
                        rows="4"
                        maxlength="2000"
                        placeholder="Jelaskan secara singkat hal yang ingin dikonsultasikan..."
                        class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-[#333D32] resize-none"
                    >{{ old('topik') }}</textarea>


                    <div class="text-[10px] text-[#A39B8B] mt-1">
                        Maksimal 2000 karakter.
                    </div>


                    @error('topik')

                        <p class="text-red-600 mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Buttons --}}
                <div class="flex justify-end gap-2 pt-2">

                    <button
                        type="button"
                        onclick="closeBookingModal()"
                        class="px-4 py-2.5 font-bold text-[#5C564B] bg-[#F5F2EB] hover:bg-[#E6E2D3] rounded-xl"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="px-5 py-2.5 font-bold text-white bg-[#5A6D58] hover:bg-[#4E614C] rounded-xl shadow-sm"
                    >

                        <span class="flex items-center gap-2">

                            <i
                                data-lucide="send"
                                class="w-4 h-4"
                            ></i>

                            Kirim Pengajuan

                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- ========================================================= --}}
{{-- MODAL PENJADWALAN GURU BK --}}
{{-- ========================================================= --}}

@if(Auth::check() && Auth::user()->role === 'guru_bk')

<div
    id="scheduleModal"
    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 px-4"
>

    <div
        class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden"
    >

        {{-- Header --}}
        <div class="p-5 border-b border-[#E6E2D3] bg-[#FDFCF8] flex items-center justify-between">

            <div>

                <h2 class="font-bold text-[#333D32] text-base">
                    Jadwalkan Konseling
                </h2>

                <p class="text-[11px] text-[#A39B8B] mt-1">
                    Tentukan tanggal dan jam konseling siswa.
                </p>

            </div>


            <button
                type="button"
                onclick="closeScheduleModal()"
                class="w-8 h-8 rounded-lg bg-[#F5F2EB] hover:bg-[#E6E2D3] text-[#5C564B] flex items-center justify-center"
            >

                <i
                    data-lucide="x"
                    class="w-4 h-4"
                ></i>

            </button>

        </div>


        {{-- Form --}}
        <form
            id="scheduleForm"
            method="POST"
            class="p-5 space-y-4"
        >

            @csrf

            @method('PATCH')


            {{-- Tanggal --}}
            <div>

                <label
                    for="schedule_tanggal"
                    class="block text-xs font-bold text-[#5A6D58] mb-1"
                >
                    Tanggal Konseling
                </label>


                <input
                    type="date"
                    id="schedule_tanggal"
                    name="tanggal_sesi"
                    min="{{ date('Y-m-d') }}"
                    required
                    class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-sm text-[#333D32]"
                >

            </div>


            {{-- Jam --}}
            <div>

                <label
                    for="schedule_jam"
                    class="block text-xs font-bold text-[#5A6D58] mb-1"
                >
                    Jam Konseling
                </label>


                <select
                    id="schedule_jam"
                    name="jam_sesi"
                    required
                    class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-sm text-[#333D32]"
                >

                    <option value="09:00">
                        09:00 - 10:00 WIB
                    </option>

                    <option value="11:00">
                        11:00 - 12:00 WIB
                    </option>

                    <option value="13:30">
                        13:30 - 14:30 WIB
                    </option>

                </select>

            </div>


            {{-- Informasi --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">

                <div class="flex gap-2">

                    <i
                        data-lucide="info"
                        class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"
                    ></i>

                    <p class="text-[11px] text-blue-700 leading-relaxed">
                        Setelah jadwal disimpan, status pengajuan akan berubah menjadi
                        <strong>Penjadwalan</strong>.
                    </p>

                </div>

            </div>


            {{-- Buttons --}}
            <div class="flex justify-end gap-2 pt-2">

                <button
                    type="button"
                    onclick="closeScheduleModal()"
                    class="px-4 py-2.5 font-bold text-[#5C564B] bg-[#F5F2EB] hover:bg-[#E6E2D3] rounded-xl"
                >
                    Batal
                </button>


                <button
                    type="submit"
                    class="px-5 py-2.5 font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl"
                >

                    <span class="flex items-center gap-2">

                        <i
                            data-lucide="calendar-check"
                            class="w-4 h-4"
                        ></i>

                        Simpan Jadwal

                    </span>

                </button>

            </div>

        </form>

    </div>

</div>

@endif


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | BOOKING MODAL
    |--------------------------------------------------------------------------
    */

    const bookingModal =
        document.getElementById('bookingModal');


    window.openBookingModal = function () {

        if (!bookingModal) {
            return;
        }

        bookingModal.classList.remove('hidden');
        bookingModal.classList.add('flex');

        document.body.classList.add('overflow-hidden');

    };


    window.closeBookingModal = function () {

        if (!bookingModal) {
            return;
        }

        bookingModal.classList.add('hidden');
        bookingModal.classList.remove('flex');

        document.body.classList.remove('overflow-hidden');

    };


    /*
    |--------------------------------------------------------------------------
    | SCHEDULE MODAL
    |--------------------------------------------------------------------------
    */

    const scheduleModal =
        document.getElementById('scheduleModal');

    const scheduleForm =
        document.getElementById('scheduleForm');

    const scheduleTanggal =
        document.getElementById('schedule_tanggal');

    const scheduleJam =
        document.getElementById('schedule_jam');


    window.openScheduleModal = function (
        bookingId,
        tanggal,
        jam
    ) {

        if (!scheduleModal || !scheduleForm) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Action Form
        |--------------------------------------------------------------------------
        |
        | Route:
        | PATCH /booking/{id}/schedule
        |
        */

        scheduleForm.action =
            "{{ url('/booking') }}/"
            + bookingId
            + "/schedule";


        /*
        |--------------------------------------------------------------------------
        | Isi tanggal
        |--------------------------------------------------------------------------
        */

        if (scheduleTanggal && tanggal) {

            scheduleTanggal.value =
                tanggal;

        }


        /*
        |--------------------------------------------------------------------------
        | Isi jam
        |--------------------------------------------------------------------------
        */

        if (scheduleJam && jam) {

            let formattedJam =
                jam.substring(0, 5);

            scheduleJam.value =
                formattedJam;

        }


        scheduleModal.classList.remove('hidden');
        scheduleModal.classList.add('flex');

        document.body.classList.add('overflow-hidden');

    };


    window.closeScheduleModal = function () {

        if (!scheduleModal) {
            return;
        }

        scheduleModal.classList.add('hidden');
        scheduleModal.classList.remove('flex');

        document.body.classList.remove('overflow-hidden');

    };


    /*
    |--------------------------------------------------------------------------
    | CLICK OUTSIDE MODAL
    |--------------------------------------------------------------------------
    */

    if (bookingModal) {

        bookingModal.addEventListener(
            'click',
            function (event) {

                if (event.target === bookingModal) {

                    closeBookingModal();

                }

            }
        );

    }


    if (scheduleModal) {

        scheduleModal.addEventListener(
            'click',
            function (event) {

                if (event.target === scheduleModal) {

                    closeScheduleModal();

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ESCAPE KEY
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                if (
                    bookingModal &&
                    !bookingModal.classList.contains('hidden')
                ) {

                    closeBookingModal();

                }


                if (
                    scheduleModal &&
                    !scheduleModal.classList.contains('hidden')
                ) {

                    closeScheduleModal();

                }

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | BUKA BOOKING MODAL OTOMATIS
    |--------------------------------------------------------------------------
    |
    | Jika validasi form booking gagal,
    | modal otomatis dibuka kembali.
    |
    */

    @if($errors->has('counselor_id') ||
        $errors->has('tanggal_sesi') ||
        $errors->has('jam_sesi') ||
        $errors->has('jenis_sesi') ||
        $errors->has('topik'))

        if (bookingModal) {

            openBookingModal();

        }

    @endif


    /*
    |--------------------------------------------------------------------------
    | LUCIDE ICON
    |--------------------------------------------------------------------------
    */

    if (
        typeof lucide !== 'undefined' &&
        typeof lucide.createIcons === 'function'
    ) {

        lucide.createIcons();

    }

});

</script>

@endsection