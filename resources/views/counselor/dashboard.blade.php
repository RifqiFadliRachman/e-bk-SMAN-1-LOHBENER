@extends('layouts.app')

@section('title', 'Dashboard Guru BK - E-BK SMAN 1 Lohbener')

@section('content')
<div x-data="counselorDashboardManager()" class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER BANNER --}}
    {{-- ========================================================= --}}
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm relative overflow-hidden border border-[#4A5D48] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold mb-2 border border-[#8BA888]/40">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-[#A2C49F]"></i> PORTAL KHUSUS GURU BK
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Dashboard Konselor Bimbingan Konseling
            </h2>
            <p class="text-[#D8E2D7] text-xs mt-1">
                Monitoring Hasil Screening I-DASS-Y, Pencatatan Tindak Lanjut Layanan, dan Manajemen Sesi Konseling.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                @click="openCreateModal()"
                class="bg-[#8BA888] hover:bg-[#7A9777] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2"
            >
                <i data-lucide="clipboard-plus" class="w-4 h-4"></i>
                <span>Catat Tindak Lanjut</span>
            </button>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- STATS CARDS --}}
    {{-- ========================================================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Siswa --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Total Siswa Terdaftar</div>
                <div class="w-8 h-8 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-2">{{ $totalStudents ?? 0 }} Siswa</div>
            <div class="text-[11px] text-[#8BA888] font-bold mt-1">SMAN 1 Lohbener</div>
        </div>

        {{-- Card 2: Screening DASS --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Screening I-DASS-Y</div>
                <div class="w-8 h-8 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-2">{{ $totalAssessments ?? 0 }} Kali</div>
            <div class="text-[11px] text-[#5A6D58] font-bold mt-1">Tersimpan dalam Database</div>
        </div>

        {{-- Card 3: Perlu Perhatian --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Perlu Perhatian</div>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#E07A5F] mt-2">{{ $highRiskStudentsCount ?? 0 }} Siswa</div>
            <div class="text-[11px] text-[#E07A5F] font-bold mt-1">Skor DASS Parah / Sangat Parah</div>
        </div>

        {{-- Card 4: Tindak Lanjut Stats --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Tindak Lanjut</div>
                <div class="w-8 h-8 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-2">{{ $totalTindakLanjut ?? 0 }} Catatan</div>
            <div class="flex items-center gap-2 mt-1 text-[10px] font-bold">
                <span class="text-amber-600">{{ $tindakLanjutBelum ?? 0 }} Belum</span>
                <span>•</span>
                <span class="text-blue-600">{{ $tindakLanjutProses ?? 0 }} Proses</span>
                <span>•</span>
                <span class="text-emerald-600">{{ $tindakLanjutSelesai ?? 0 }} Selesai</span>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- FITUR PENCATATAN TINDAK LANJUT GURU BK --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xs overflow-hidden">

        {{-- Header Tindak Lanjut --}}
        <div class="p-5 border-b border-[#E6E2D3] bg-[#FDFCF8] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#5A6D58] text-white flex items-center justify-center">
                        <i data-lucide="clipboard-edit" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-base text-[#5A6D58]">Pencatatan Tindak Lanjut Guru BK</h3>
                    <span class="bg-[#E8F0E7] text-[#5A6D58] text-[10px] px-2.5 py-0.5 rounded-full font-bold border border-[#8BA888]/40 flex items-center gap-1">
                        <i data-lucide="lock" class="w-3 h-3 text-[#8BA888]"></i> Asas Kerahasiaan BK
                    </span>
                </div>
                <p class="text-xs text-[#A39B8B] mt-1">
                    Dokumentasi penanganan siswa, konseling, dan rujukan dengan hak akses terbatas dan catatan rahasia terenkripsi AES-256.
                </p>
            </div>

            <button
                type="button"
                @click="openCreateModal()"
                class="w-full md:w-auto bg-[#5A6D58] hover:bg-[#4E614C] text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center justify-center gap-2"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Tindak Lanjut</span>
            </button>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="p-4 bg-white border-b border-[#F5F2EB] flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-xs">
            {{-- Status Filter Tabs --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <button
                    type="button"
                    @click="statusFilter = 'all'"
                    :class="statusFilter === 'all' ? 'bg-[#5A6D58] text-white' : 'bg-[#F5F2EB] text-[#5C564B] hover:bg-[#E6E2D3]'"
                    class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1"
                >
                    Semua Status ({{ $totalTindakLanjut ?? 0 }})
                </button>
                <button
                    type="button"
                    @click="statusFilter = 'Belum ditangani'"
                    :class="statusFilter === 'Belum ditangani' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                    class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1"
                >
                    Belum Ditangani ({{ $tindakLanjutBelum ?? 0 }})
                </button>
                <button
                    type="button"
                    @click="statusFilter = 'Proses'"
                    :class="statusFilter === 'Proses' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
                    class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1"
                >
                    Proses ({{ $tindakLanjutProses ?? 0 }})
                </button>
                <button
                    type="button"
                    @click="statusFilter = 'Selesai'"
                    :class="statusFilter === 'Selesai' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                    class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1"
                >
                    Selesai ({{ $tindakLanjutSelesai ?? 0 }})
                </button>
            </div>

            {{-- Search and Tindakan Filter --}}
            <div class="flex items-center gap-2 w-full md:w-auto">
                <select
                    x-model="tindakanFilter"
                    class="px-3 py-2 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-xs font-medium text-[#333D32] outline-none focus:ring-2 focus:ring-[#8BA888]"
                >
                    <option value="all">Semua Tindakan</option>
                    <option value="Konseling">Konseling</option>
                    <option value="Rujukan">Rujukan</option>
                </select>

                <div class="relative flex-1 md:w-56">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-[#A39B8B] absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input
                        x-model="searchTindakLanjut"
                        type="text"
                        placeholder="Cari siswa / petugas..."
                        class="w-full pl-8 pr-3 py-2 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#8BA888] transition-all"
                    >
                </div>
            </div>
        </div>

        {{-- Table Tindak Lanjut --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F5F2EB] text-[#5A6D58] text-xs font-bold uppercase tracking-wider border-b border-[#E6E2D3]">
                        <th class="p-3.5 pl-5">Tanggal Tindak Lanjut</th>
                        <th class="p-3.5">Siswa</th>
                        <th class="p-3.5">Guru BK</th>
                        <th class="p-3.5">Tindakan</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Catatan</th>
                        <th class="p-3.5 pr-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E2D3] text-xs">
                    @forelse($tindakLanjuts as $item)
                        @php
                            $statusStyle = match($item->status) {
                                'Belum ditangani' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Proses'          => 'bg-blue-50 text-blue-700 border-blue-200',
                                'Selesai'         => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                default           => 'bg-gray-50 text-gray-700 border-gray-200',
                            };

                            $isRujukan = str_contains(strtolower($item->tindakan), 'rujukan');
                        @endphp
                        <tr
                            class="hover:bg-[#FDFCF8] transition-all"
                            x-show="
                                (statusFilter === 'all' || '{{ $item->status }}' === statusFilter) &&
                                (tindakanFilter === 'all' || '{{ strtolower($item->tindakan) }}'.includes(tindakanFilter.toLowerCase())) &&
                                (searchTindakLanjut === '' ||
                                    '{{ strtolower($item->student_name) }}'.includes(searchTindakLanjut.toLowerCase()) ||
                                    '{{ strtolower($item->counselor_name) }}'.includes(searchTindakLanjut.toLowerCase()) ||
                                    '{{ strtolower($item->tindakan) }}'.includes(searchTindakLanjut.toLowerCase())
                                )
                            "
                        >
                            {{-- 1. Tanggal tindak lanjut (Waktu penanganan) --}}
                            <td class="p-3.5 pl-5 whitespace-nowrap">
                                <div class="font-bold text-[#333D32] flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#8BA888]"></i>
                                    {{ $item->formatted_tanggal }}
                                </div>
                                <span class="text-[10px] text-[#A39B8B] block mt-0.5">Waktu Penanganan</span>
                            </td>

                            {{-- 2. Siswa (Identitas siswa yang dibatasi aksesnya) --}}
                            <td class="p-3.5">
                                <div class="font-bold text-[#5A6D58] flex items-center gap-1.5">
                                    <span>{{ $item->student_name }}</span>
                                    <span class="bg-[#F5F2EB] text-[#5A6D58] text-[9px] px-1.5 py-0.5 rounded font-bold border border-[#E6E2D3]" title="Akses Dibatasi Khusus Guru BK">
                                        BK Only
                                    </span>
                                </div>
                                <div class="text-[11px] text-[#5C564B]">
                                    Kelas: <strong>{{ $item->student_kelas ?? '-' }}</strong>
                                </div>
                            </td>

                            {{-- 3. Guru BK (Petugas yang menangani) --}}
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="font-medium text-[#333D32] flex items-center gap-1.5">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#5A6D58]"></i>
                                    {{ $item->counselor_name }}
                                </div>
                                <span class="text-[10px] text-[#A39B8B]">Petugas Penanganan</span>
                            </td>

                            {{-- 4. Tindakan (Konseling atau rujukan) --}}
                            <td class="p-3.5">
                                @if($isRujukan)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                        {{ $item->tindakan }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#E8F0E7] text-[#5A6D58] border border-[#8BA888]/40">
                                        <i data-lucide="heart-handshake" class="w-3 h-3"></i>
                                        {{ $item->tindakan }}
                                    </span>
                                @endif
                            </td>

                            {{-- 5. Status (Belum ditangani / Proses / Selesai) --}}
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusStyle }}">
                                        @if($item->status === 'Belum ditangani')
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                        @elseif($item->status === 'Proses')
                                            <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                                        @else
                                            <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                        @endif
                                        {{ $item->status }}
                                    </span>

                                    {{-- Dropdown Quick Status Change --}}
                                    <div class="relative" x-data="{ openStatus: false }">
                                        <button
                                            type="button"
                                            @click="openStatus = !openStatus"
                                            class="p-1 rounded-lg hover:bg-[#E6E2D3] text-[#5C564B] transition-colors"
                                            title="Ubah Status Cepat"
                                        >
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <div
                                            x-show="openStatus"
                                            @click.outside="openStatus = false"
                                            class="absolute left-0 mt-1 w-36 bg-white border border-[#E6E2D3] rounded-xl shadow-lg z-20 py-1"
                                            style="display: none;"
                                        >
                                            <form action="{{ route('counselor.tindak-lanjut.status', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Belum ditangani">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 text-[11px] font-medium text-amber-700 hover:bg-amber-50 flex items-center gap-1.5">
                                                    <i data-lucide="clock" class="w-3 h-3"></i> Belum ditangani
                                                </button>
                                            </form>
                                            <form action="{{ route('counselor.tindak-lanjut.status', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Proses">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 text-[11px] font-medium text-blue-700 hover:bg-blue-50 flex items-center gap-1.5">
                                                    <i data-lucide="loader-2" class="w-3 h-3"></i> Proses
                                                </button>
                                            </form>
                                            <form action="{{ route('counselor.tindak-lanjut.status', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Selesai">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 text-[11px] font-medium text-emerald-700 hover:bg-emerald-50 flex items-center gap-1.5">
                                                    <i data-lucide="check-circle" class="w-3 h-3"></i> Selesai
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 6. Catatan (Catatan tindak lanjut rahasia) --}}
                            <td class="p-3.5">
                                @if($item->decrypted_catatan)
                                    <button
                                        type="button"
                                        @click="openViewModal({
                                            student_name: '{{ addslashes($item->student_name) }}',
                                            student_kelas: '{{ addslashes($item->student_kelas ?? '-') }}',
                                            counselor_name: '{{ addslashes($item->counselor_name) }}',
                                            tanggal: '{{ $item->formatted_tanggal }}',
                                            tindakan: '{{ addslashes($item->tindakan) }}',
                                            status: '{{ $item->status }}',
                                            catatan: `{{ addslashes($item->decrypted_catatan) }}`
                                        })"
                                        class="bg-[#F5F2EB] hover:bg-[#E6E2D3] text-[#5A6D58] border border-[#E6E2D3] px-2.5 py-1 rounded-lg font-bold flex items-center gap-1 transition-colors text-[11px]"
                                        title="Buka Catatan Rahasia"
                                    >
                                        <i data-lucide="lock" class="w-3 h-3 text-[#8BA888]"></i>
                                        <span>Buka Catatan</span>
                                    </button>
                                @else
                                    <span class="text-[#A39B8B] italic text-[11px]">- Tidak ada catatan -</span>
                                @endif
                            </td>

                            {{-- Aksi: Edit & Hapus --}}
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        @click="openEditModal({
                                            id: {{ $item->id }},
                                            student_id: {{ $item->student_id }},
                                            tanggal_tindak_lanjut: '{{ $item->tanggal_tindak_lanjut }}',
                                            tindakan: '{{ addslashes($item->tindakan) }}',
                                            status: '{{ $item->status }}',
                                            catatan: `{{ addslashes($item->decrypted_catatan ?? '') }}`
                                        })"
                                        class="p-1.5 bg-[#F5F2EB] hover:bg-[#E6E2D3] text-[#5A6D58] rounded-lg transition-colors"
                                        title="Edit Data Tindak Lanjut"
                                    >
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <form
                                        action="{{ route('counselor.tindak-lanjut.destroy', $item->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan tindak lanjut ini?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
                                            title="Hapus Catatan"
                                        >
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#A39B8B] font-medium bg-[#F5F2EB]/50">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="clipboard-x" class="w-8 h-8 opacity-50"></i>
                                    <span class="font-semibold text-sm text-[#5A6D58]">Belum ada data pencatatan tindak lanjut.</span>
                                    <span class="text-xs">Klik tombol "Tambah Tindak Lanjut" untuk mendokumentasikan konseling atau rujukan siswa.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- DAFTAR HASIL SCREENING I-DASS-Y SISWA --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xs overflow-hidden" x-data="{ search: '' }">
        <div class="p-4 border-b border-[#E6E2D3] bg-[#FDFCF8] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-sm text-[#5A6D58]">Daftar Hasil Screening I-DASS-Y Siswa</h3>
                <span class="text-xs text-[#A39B8B]">Klasifikasi Otomatis Algoritma Lovibond</span>
            </div>

            <div class="relative w-full sm:w-auto">
                <i data-lucide="search" class="w-4 h-4 text-[#A39B8B] absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input x-model="search" type="text" placeholder="Cari nama atau kelas siswa..." class="w-full sm:w-64 pl-9 pr-4 py-2 bg-white border border-[#E6E2D3] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#8BA888] transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F5F2EB] text-[#5A6D58] text-xs font-bold uppercase tracking-wider border-b border-[#E6E2D3]">
                        <th class="p-4">Nama Siswa</th>
                        <th class="p-4">Kelas</th>
                        <th class="p-4">Tingkat Depresi</th>
                        <th class="p-4">Tingkat Kecemasan</th>
                        <th class="p-4">Tingkat Stres</th>
                        <th class="p-4">Status Risiko</th>
                        <th class="p-4">Aksi Intervensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E2D3] text-xs">
                    @forelse($studentsResults as $row)
                        <tr class="hover:bg-[#FDFCF8] transition-all" x-show="search === '' || '{{ strtolower($row->student_name) }}'.includes(search.toLowerCase()) || '{{ strtolower($row->kelas ?? '') }}'.includes(search.toLowerCase())">
                            <td class="p-4 font-bold text-[#5A6D58]">{{ $row->student_name }}
                                <div class="text-[10px] sm:hidden text-[#A39B8B] font-medium">{{ $row->kelas }}</div>
                            </td>
                            <td class="p-4 text-[#5C564B] font-medium hidden sm:table-cell">{{ $row->kelas }}</td>
                            <td class="p-4 font-semibold">{{ $row->depression_category }} <span class="text-[#A39B8B] text-[10px] ml-1">({{ $row->depression_score }})</span></td>
                            <td class="p-4 font-semibold">{{ $row->anxiety_category }} <span class="text-[#A39B8B] text-[10px] ml-1">({{ $row->anxiety_score }})</span></td>
                            <td class="p-4 font-semibold">{{ $row->stress_category }} <span class="text-[#A39B8B] text-[10px] ml-1">({{ $row->stress_score }})</span></td>
                            <td class="p-4">
                                @if(in_array($row->overall_risk, ['Parah', 'Sangat Parah']))
                                    <span class="bg-[#FEE2E2] text-[#991B1B] px-2.5 py-1.5 rounded-full font-bold text-[10px] border border-[#F87171]/50 shadow-xs">Risiko Tinggi</span>
                                @elseif($row->overall_risk == 'Sedang')
                                    <span class="bg-[#FEF3C7] text-[#92400E] px-2.5 py-1.5 rounded-full font-bold text-[10px] border border-[#FCD34D]/50 shadow-xs">Risiko Sedang</span>
                                @else
                                    <span class="bg-[#E8F0E7] text-[#5A6D58] px-2.5 py-1.5 rounded-full font-bold text-[10px] border border-[#8BA888]/50 shadow-xs">Normal</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-2">
                                    {{-- Status Tindak Lanjut --}}
                                    @if($row->has_tindak_lanjut)
                                        <span class="bg-[#E8F0E7] text-[#3D6B35] px-2.5 py-1.5 rounded-full font-bold text-[10px] border border-[#8BA888]/50 shadow-xs inline-flex items-center gap-1 w-fit">
                                            <i data-lucide="check-circle-2" class="w-3 h-3 flex-shrink-0"></i>
                                            <span class="hidden lg:inline">Sudah Ditindak Lanjuti</span>
                                            <span class="lg:hidden">Sudah</span>
                                        </span>
                                    @else
                                        <span class="bg-[#FEF3C7] text-[#92400E] px-2.5 py-1.5 rounded-full font-bold text-[10px] border border-[#FCD34D]/50 shadow-xs inline-flex items-center gap-1 w-fit">
                                            <i data-lucide="clock" class="w-3 h-3 flex-shrink-0"></i>
                                            <span class="hidden lg:inline">Belum Ditindak Lanjuti</span>
                                            <span class="lg:hidden">Belum</span>
                                        </span>
                                    @endif

                                    {{-- Tombol Aksi (sebaris) --}}
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('counselor.student-profile', $row->student_id) }}" class="bg-[#F5F2EB] text-[#5C564B] border border-[#E6E2D3] px-3 py-1.5 rounded-xl font-bold hover:bg-[#E6E2D3] transition-colors flex items-center gap-1.5" title="Lihat Profil & Catatan">
                                            <i data-lucide="user" class="w-3.5 h-3.5"></i> <span class="hidden lg:inline">Profil</span>
                                        </a>

                                        <a href="{{ route('chat.index', ['recipient_id' => $row->student_id]) }}" class="bg-[#5A6D58] text-white px-3 py-1.5 rounded-xl font-bold hover:bg-[#4E614C] transition-colors shadow-xs flex items-center gap-1.5" title="Chat Privat">
                                            <i data-lucide="message-circle" class="w-3.5 h-3.5 flex-shrink-0"></i> <span class="hidden lg:inline">Chat</span>
                                        </a>

                                        {{-- Tombol Hapus (hanya jika sudah ditindak lanjuti) --}}
                                        @if($row->has_tindak_lanjut)
                                            <form action="{{ route('counselor.dass-result.destroy', $row->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hasil screening I-DASS-Y siswa ini? Data yang dihapus tidak dapat dikembalikan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-[#FEE2E2] text-[#991B1B] border border-[#F87171]/50 px-3 py-1.5 rounded-xl font-bold hover:bg-[#FECACA] transition-colors shadow-xs flex items-center gap-1.5" title="Hapus Hasil Screening">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                                    <span class="hidden lg:inline">Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#A39B8B] font-medium bg-[#F5F2EB]/50">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="folder-open" class="w-8 h-8 opacity-50"></i>
                                    <span>Belum ada data I-DASS-Y siswa saat ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- MODAL FORM: TAMBAH / EDIT TINDAK LANJUT --}}
    {{-- ========================================================= --}}
    <div
        x-show="isModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
        style="display: none;"
    >
        <div
            @click.outside="isModalOpen = false"
            class="bg-white rounded-3xl border border-[#E6E2D3] max-w-lg w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto"
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-[#F5F2EB] pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#E8F0E7] text-[#5A6D58] flex items-center justify-center">
                        <i data-lucide="clipboard-edit" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-[#5A6D58]" x-text="isEditMode ? 'Edit Catatan Tindak Lanjut' : 'Catat Tindak Lanjut Baru'"></h3>
                        <p class="text-[11px] text-[#A39B8B]">Informasi penanganan siswa oleh Guru BK.</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="isModalOpen = false"
                    class="p-1 rounded-xl text-[#A39B8B] hover:text-[#333D32] hover:bg-[#F5F2EB] transition-colors"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Form Tindak Lanjut --}}
            <form
                :action="isEditMode ? `/counselor/tindak-lanjut/${editId}` : '{{ route('counselor.tindak-lanjut.store') }}'"
                method="POST"
                class="space-y-4 text-xs"
            >
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- 1. Siswa --}}
                <div>
                    <label class="block font-bold text-[#5A6D58] mb-1">
                        Siswa <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="student_id"
                        x-model="form.student_id"
                        required
                        class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]"
                    >
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($studentsList as $stu)
                            <option value="{{ $stu->id }}">
                                {{ $stu->name }} (Kelas {{ $stu->kelas ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-[#A39B8B] mt-0.5 block">
                        Identitas siswa dibatasi aksesnya hanya untuk konselor BK yang berwenang.
                    </span>
                </div>

                {{-- 2. Tanggal Tindak Lanjut & Petugas Guru BK --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#5A6D58] mb-1">
                            Tanggal Tindak Lanjut <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="tanggal_tindak_lanjut"
                            x-model="form.tanggal_tindak_lanjut"
                            required
                            class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]"
                        >
                        <span class="text-[10px] text-[#A39B8B] mt-0.5 block">Waktu penanganan siswa</span>
                    </div>

                    <div>
                        <label class="block font-bold text-[#5A6D58] mb-1">
                            Petugas Guru BK
                        </label>
                        <input
                            type="text"
                            value="{{ Auth::user()->name }}"
                            readonly
                            class="w-full p-2.5 bg-[#F5F2EB] border border-[#E6E2D3] rounded-xl font-medium text-[#5C564B] cursor-not-allowed"
                        >
                        <span class="text-[10px] text-[#A39B8B] mt-0.5 block">Konselor yang menangani</span>
                    </div>
                </div>

                {{-- 3. Tindakan & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#5A6D58] mb-1">
                            Tindakan <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="tindakan"
                            x-model="form.tindakan"
                            required
                            class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]"
                        >
                            <option value="Konseling">Konseling</option>
                            <option value="Konseling Individu">Konseling Individu</option>
                            <option value="Konseling Kelompok">Konseling Kelompok</option>
                            <option value="Rujukan">Rujukan</option>
                            <option value="Rujukan Psikolog / Psikiater">Rujukan Psikolog / Psikiater</option>
                            <option value="Alih Tangan Kasus">Alih Tangan Kasus</option>
                            <option value="Bimbingan Pribadi & Belajar">Bimbingan Pribadi & Belajar</option>
                        </select>
                        <span class="text-[10px] text-[#A39B8B] mt-0.5 block">Pilih bentuk konseling atau rujukan</span>
                    </div>

                    <div>
                        <label class="block font-bold text-[#5A6D58] mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="status"
                            x-model="form.status"
                            required
                            class="w-full p-2.5 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32]"
                        >
                            <option value="Belum ditangani">Belum ditangani</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <span class="text-[10px] text-[#A39B8B] mt-0.5 block">Status penanganan</span>
                    </div>
                </div>

                {{-- 4. Catatan Tindak Lanjut Rahasia --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-[#5A6D58]">
                            Catatan Tindak Lanjut Rahasia
                        </label>
                        <span class="text-[10px] font-mono text-[#8BA888] flex items-center gap-1 font-bold">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Enkripsi AES-256
                        </span>
                    </div>
                    <textarea
                        name="catatan"
                        x-model="form.catatan"
                        rows="4"
                        placeholder="Tuliskan catatan observasi, rencana konseling, hasil intervensi, atau catatan rujukan siswa secara rahasia..."
                        class="w-full p-3 bg-[#FDFCF8] border border-[#E6E2D3] rounded-xl font-medium outline-none focus:ring-2 focus:ring-[#8BA888] text-[#333D32] leading-relaxed"
                    ></textarea>
                    <div class="mt-1 bg-[#E8F0E7]/60 border border-[#8BA888]/30 rounded-lg p-2 text-[10px] text-[#5A6D58] flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5 flex-shrink-0 text-[#8BA888]"></i>
                        <span>Sesuai asas kerahasiaan konselor BK, catatan ini disimpan secara aman dengan enkripsi AES-256.</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#F5F2EB]">
                    <button
                        type="button"
                        @click="isModalOpen = false"
                        class="px-4 py-2 font-bold text-[#5C564B] bg-[#F5F2EB] hover:bg-[#E6E2D3] rounded-xl transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 font-bold text-white bg-[#5A6D58] hover:bg-[#4E614C] rounded-xl shadow-xs transition-colors flex items-center gap-1.5"
                    >
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan Tindak Lanjut'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- MODAL VIEW: DETAIL CATATAN RAHASIA --}}
    {{-- ========================================================= --}}
    <div
        x-show="isViewModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
        style="display: none;"
    >
        <div
            @click.outside="isViewModalOpen = false"
            class="bg-white rounded-3xl border border-[#E6E2D3] max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto"
        >
            <div class="flex items-center justify-between border-b border-[#F5F2EB] pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#5A6D58] text-white flex items-center justify-center">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-[#5A6D58]">Catatan Rahasia Tindak Lanjut</h3>
                        <p class="text-[10px] text-[#A39B8B]">Hanya dapat diakses oleh Guru BK yang berwenang</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="isViewModalOpen = false"
                    class="p-1 rounded-xl text-[#A39B8B] hover:text-[#333D32] hover:bg-[#F5F2EB] transition-colors"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-3 p-3 bg-[#FDFCF8] rounded-xl border border-[#E6E2D3]">
                    <div>
                        <span class="text-[#A39B8B] font-medium block">Siswa:</span>
                        <span class="font-bold text-[#5A6D58]" x-text="viewData.student_name"></span>
                        <span class="text-[10px] text-[#5C564B] block" x-text="`Kelas: ${viewData.student_kelas}`"></span>
                    </div>
                    <div>
                        <span class="text-[#A39B8B] font-medium block">Guru BK (Petugas):</span>
                        <span class="font-bold text-[#333D32]" x-text="viewData.counselor_name"></span>
                    </div>
                    <div>
                        <span class="text-[#A39B8B] font-medium block">Waktu Penanganan:</span>
                        <span class="font-bold text-[#333D32]" x-text="viewData.tanggal"></span>
                    </div>
                    <div>
                        <span class="text-[#A39B8B] font-medium block">Tindakan & Status:</span>
                        <span class="font-bold text-[#5A6D58]" x-text="viewData.tindakan"></span>
                        <span class="text-[10px] font-bold block text-blue-600" x-text="`Status: ${viewData.status}`"></span>
                    </div>
                </div>

                {{-- Catatan Isi --}}
                <div class="space-y-1.5">
                    <span class="font-bold text-[#5A6D58] flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-4 h-4 text-[#8BA888]"></i> Isi Catatan Tindak Lanjut:
                    </span>
                    <div class="p-4 bg-[#F5F8F5] border border-[#8BA888]/40 rounded-2xl text-[#333D32] leading-relaxed whitespace-pre-wrap font-medium" x-text="viewData.catatan">
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-[11px] text-amber-800 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <span>Informasi ini bersifat konfidensial dan dilindungi oleh kode etik profesi Bimbingan dan Konseling.</span>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button
                    type="button"
                    @click="isViewModalOpen = false"
                    class="px-4 py-2 font-bold text-white bg-[#5A6D58] hover:bg-[#4E614C] rounded-xl transition-colors text-xs"
                >
                    Tutup Catatan
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ========================================================= --}}
{{-- ALPINE JS MANAGER --}}
{{-- ========================================================= --}}
<script>
function counselorDashboardManager() {
    return {
        statusFilter: 'all',
        tindakanFilter: 'all',
        searchTindakLanjut: '',
        isModalOpen: false,
        isEditMode: false,
        isViewModalOpen: false,
        editId: null,

        form: {
            student_id: '',
            tanggal_tindak_lanjut: '{{ now()->format("Y-m-d\TH:i") }}',
            tindakan: 'Konseling',
            status: 'Belum ditangani',
            catatan: ''
        },

        viewData: {
            student_name: '',
            student_kelas: '',
            counselor_name: '',
            tanggal: '',
            tindakan: '',
            status: '',
            catatan: ''
        },

        openCreateModal(studentId = '') {
            this.isEditMode = false;
            this.editId = null;
            this.form = {
                student_id: studentId ? String(studentId) : '',
                tanggal_tindak_lanjut: '{{ now()->format("Y-m-d\TH:i") }}',
                tindakan: 'Konseling',
                status: 'Belum ditangani',
                catatan: ''
            };
            this.isModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) {
                    lucide.createIcons();
                }
            });
        },

        openEditModal(item) {
            this.isEditMode = true;
            this.editId = item.id;

            let dt = item.tanggal_tindak_lanjut ? item.tanggal_tindak_lanjut.replace(' ', 'T').substring(0, 16) : '{{ now()->format("Y-m-d\TH:i") }}';

            this.form = {
                student_id: String(item.student_id),
                tanggal_tindak_lanjut: dt,
                tindakan: item.tindakan,
                status: item.status,
                catatan: item.catatan || ''
            };
            this.isModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) {
                    lucide.createIcons();
                }
            });
        },

        openViewModal(data) {
            this.viewData = data;
            this.isViewModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) {
                    lucide.createIcons();
                }
            });
        }
    };
}

document.addEventListener('alpine:initialized', () => {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endsection
