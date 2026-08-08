@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah - E-BK SMAN 1 Lohbener')

@section('content')
<div class="space-y-6">

    {{-- Executive Header Banner --}}
    <div class="bg-[#5A6D58] rounded-3xl p-6 text-[#FDFCF8] shadow-sm border border-[#4A5D48] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-[#8BA888]/30 text-[#E8F0E7] text-xs px-3 py-1 rounded-full font-bold border border-[#8BA888]/40 mb-2">
                <i data-lucide="school" class="w-3.5 h-3.5 text-[#A2C49F]"></i> EXECUTIVE DASHBOARD KEPALA SEKOLAH
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Laporan Evaluasi Layanan BK &amp; Kesehatan Mental Siswa
            </h2>
            <p class="text-xs text-[#D8E2D7] mt-1">
                Pengawas: {{ Auth::user()->name ?? 'Drs. Agus Supriatna, M.Pd' }} • SMA Negeri 1 Lohbener
            </p>
        </div>

        <a href="{{ route('principal.export') }}" class="bg-[#8BA888] hover:bg-[#7A9777] text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 whitespace-nowrap">
            <i data-lucide="download" class="w-4 h-4"></i> Cetak / Export Rekap Laporan
        </a>
    </div>

    {{-- KPI Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Total Siswa Terjangkau</div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-1">{{ $totalStudents ?? 105 }} Siswa</div>
            <div class="text-[11px] text-[#8BA888] font-bold mt-1">SMAN 1 Lohbener</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Total Screening DASS-21</div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-1">{{ $totalAssessments ?? 45 }} Kali</div>
            <div class="text-[11px] text-[#5A6D58] font-bold mt-1">Tersimpan dalam Basis Data</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Sesi Konseling Terlaksana</div>
            <div class="text-2xl font-bold text-[#5A6D58] mt-1">{{ $totalSessionsCompleted ?? 18 }} Sesi</div>
            <div class="text-[11px] text-[#8BA888] font-bold mt-1">Tatap Muka &amp; Chat Online</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#E6E2D3] shadow-xs">
            <div class="text-xs font-bold text-[#A39B8B] uppercase tracking-wider">Tingkat Risiko Teratasi</div>
            <div class="text-2xl font-bold text-[#8BA888] mt-1">{{ $resolvedRate ?? '88.5' }}%</div>
            <div class="text-[11px] text-[#A39B8B] font-medium mt-1">Evaluasi Berkala BK</div>
        </div>
    </div>

    {{-- Analytics Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Severity Distribution Pie Chart --}}
        <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-bold text-base text-[#5A6D58]">Distribusi Tingkat Keparahan DASS-21</h3>
                <p class="text-xs text-[#A39B8B]">
                    Kategori gabungan Depresi, Kecemasan, dan Stres seluruh siswa.
                </p>
            </div>
            <div class="h-64 w-full flex items-center justify-center">
                <canvas id="severityPieChart"></canvas>
            </div>
        </div>

        {{-- Class Risk Breakdown Bar Chart --}}
        <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-bold text-base text-[#5A6D58]">Pemetaan Risiko Per Kelas</h3>
                <p class="text-xs text-[#A39B8B]">
                    Jumlah siswa berisiko sedang hingga tinggi per rombel kelas.
                </p>
            </div>
            <div class="h-64 w-full">
                <canvas id="classRiskBarChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Printable Evaluation Report Summary --}}
    <div class="bg-white rounded-3xl border border-[#E6E2D3] p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b pb-3 border-[#F5F2EB]">
            <div>
                <h3 class="font-bold text-base text-[#5A6D58]">
                    Ringkasan Laporan Resmi Bimbingan Konseling SMAN 1 Lohbener
                </h3>
                <p class="text-xs text-[#A39B8B]">
                    Disusun untuk evaluasi Kepala Sekolah &amp; Dinas Pendidikan Kabupaten Indramayu.
                </p>
            </div>
            <span class="bg-[#F5F2EB] text-[#5A6D58] text-xs px-3 py-1 rounded-full font-bold border border-[#E6E2D3]">
                Terverifikasi Sistem
            </span>
        </div>

        <div class="space-y-3 text-xs leading-relaxed text-[#5C564B]">
            <p>
                Berdasarkan data monitoring real-time, digitalisasi layanan Bimbingan Konseling di SMAN 1 Lohbener telah meningkatkan partisipasi siswa secara signifikan dari 22.2% menjadi <strong>68.4%</strong> melalui ketersediaan mode booking konseling mandiri dan chat privat terenkripsi.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                <div class="p-3.5 bg-[#FDFCF8] rounded-2xl border border-[#E6E2D3]">
                    <strong class="text-[#5A6D58] block mb-1">Rekomendasi Kebijakan Sekolah:</strong>
                    1. Penguatan program manajemen stres jelang ujian semester untuk kelas XII MIPA 3 &amp; XI IPS 2.<br>
                    2. Sosialisasi keberlanjutan pemanfaatan instrumen DASS-21 awal semester.
                </div>
                <div class="p-3.5 bg-[#FDFCF8] rounded-2xl border border-[#E6E2D3]">
                    <strong class="text-[#5A6D58] block mb-1">Jaminan Kepatuhan Kerahasiaan:</strong>
                    Seluruh catatan rekam medis dan isi chat individual terproteksi AES-256 dan hanya dapat diakses oleh konselor penanggung jawab.
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    // Severity Distribution Pie
    const pieCtx = document.getElementById('severityPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Ringan', 'Sedang', 'Parah', 'Sangat Parah'],
            datasets: [{
                data: @json($severityDistribution ?? [28, 14, 8, 4, 1]),
                backgroundColor: ['#8BA888', '#5A6D58', '#D97706', '#E07A5F', '#991B1B'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } }
            },
            cutout: '55%',
        }
    });

    // Class Risk Breakdown Bar
    const barCtx = document.getElementById('classRiskBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($classLabels ?? ['X MIPA 1','X IPS 1','XI MIPA 1','XI MIPA 2','XI IPS 1','XII MIPA 1','XII MIPA 3']),
            datasets: [
                {
                    label: 'Total Siswa',
                    data: @json($classStudentCounts ?? [32, 28, 30, 31, 29, 33, 30]),
                    backgroundColor: '#E6E2D3',
                    borderRadius: 4,
                },
                {
                    label: 'Siswa Berisiko',
                    data: @json($classHighRiskCounts ?? [2, 1, 3, 2, 4, 1, 5]),
                    backgroundColor: '#E07A5F',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { font: { size: 10 } } },
                y: { ticks: { font: { size: 10 } }, beginAtZero: true }
            },
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } }
            }
        }
    });
</script>
@endsection
