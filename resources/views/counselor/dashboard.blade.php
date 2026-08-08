@extends('layouts.app')

@section('title', 'Dashboard Guru BK - E-BK SMAN 1 Lohbener')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm relative overflow-hidden border border-[#4A5D48] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold mb-2 border border-[#8BA888]/40">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-[#A2C49F]"></i> PORTAL KHUSUS GURU BK
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Dashboard Konselor Bimbingan Konseling
            </h2>
            <p class="text-[#D8E2D7] text-xs mt-1">
                Monitoring Hasil Screening DASS-21, Manajemen Sesi Konseling, dan Intervensi Dini Siswa.
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Total Siswa Terdaftar</div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-1">{{ $totalStudents ?? 105 }} Siswa</div>
            <div class="text-[11px] text-[#8BA888] font-bold mt-1">SMAN 1 Lohbener</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Screening DASS-21 Selesai</div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-1">{{ $totalAssessments ?? 45 }} Kali</div>
            <div class="text-[11px] text-[#5A6D58] font-bold mt-1">Tersimpan dalam Database</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Siswa Perlu Perhatian</div>
            <div class="text-2xl font-bold text-[#E07A5F] mt-1">{{ $highRiskStudentsCount ?? 8 }} Siswa</div>
            <div class="text-[11px] text-[#E07A5F] font-bold mt-1">Skor DASS Parah / Sangat Parah</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Booking Pending</div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-1">{{ $pendingBookingsCount ?? 3 }} Sesi</div>
            <div class="text-[11px] text-[#A39B8B] font-medium mt-1">Memerlukan Persetujuan</div>
        </div>
    </div>

    <!-- Student Risk Table -->
    <div class="bg-white rounded-3xl border border-[#E6E2D3] shadow-xs overflow-hidden" x-data="{ search: '' }">
        <div class="p-4 border-b border-[#E6E2D3] bg-[#FDFCF8] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-sm text-[#5A6D58]">Daftar Hasil Screening DASS-21 Siswa</h3>
                <span class="text-xs text-[#A39B8B]">Klasifikasi Otomatis Algoritma Lovibond</span>
            </div>
            
            <div class="relative w-full sm:w-auto">
                <i data-lucide="search" class="w-4 h-4 text-[#A39B8B] absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input x-model="search" type="text" placeholder="Cari nama siswa..." class="w-full sm:w-64 pl-9 pr-4 py-2 bg-white border border-[#E6E2D3] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#8BA888] transition-all">
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
                        <tr class="hover:bg-[#FDFCF8] transition-all" x-show="search === '' || '{{ strtolower($row->student_name) }}'.includes(search.toLowerCase())">
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
                                    <span class="bg-[#E8F0E7] text-[#5A6D58] px-2.5 py-1.5 rounded-full font-bold text-[10px] border border-[#8BA888]/50 shadow-xs:">Normal</span>
                                @endif
                            </td>
                            <td class="p-4 flex flex-wrap items-center gap-2">
                                <a href="{{ route('counselor.student-profile', $row->student_id) }}" class="bg-[#F5F2EB] text-[#5C564B] border border-[#E6E2D3] px-3 py-1.5 rounded-xl font-bold hover:bg-[#E6E2D3] transition-colors flex items-center gap-1.5" title="Lihat Profil & Catatan">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i> <span class="hidden lg:inline">Profil</span>
                                </a>
                                <a href="{{ route('chat.index', ['recipient_id' => $row->student_id]) }}" class="bg-[#5A6D58] text-white px-3 py-1.5 rounded-xl font-bold hover:bg-[#4E614C] transition-colors shadow-xs flex items-center gap-1.5" title="Chat Privat">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5 flex-shrink-0"></i> <span class="hidden lg:inline">Chat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#A39B8B] font-medium bg-[#F5F2EB]/50">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="folder-open" class="w-8 h-8 opacity-50"></i>
                                    <span>Belum ada data DASS-21 siswa saat ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
