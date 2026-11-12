@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | DEFAULT DATA
    |--------------------------------------------------------------------------
    | Mencegah error Undefined variable apabila controller belum mengirim
    | salah satu variabel.
    |--------------------------------------------------------------------------
    */

    $totalStudents = $totalStudents ?? 0;

    $totalAssessments = $totalAssessments ?? 0;

    $totalSessionsCompleted = $totalSessionsCompleted ?? 0;

    $highRiskStudentsCount = $highRiskStudentsCount ?? 0;

    $riskPercentage = $riskPercentage ?? 0;

    $severityDistribution = $severityDistribution ?? [
        0,
        0,
        0,
        0,
        0
    ];

    $classLabels = $classLabels ?? [];

    $classStudentCounts = $classStudentCounts ?? [];

    $classHighRiskCounts = $classHighRiskCounts ?? [];

    $recentResults = $recentResults ?? collect();
@endphp


<div class="principal-dashboard">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="dashboard-header">

        <div>

            <div class="dashboard-badge">
                <span>🏫</span>
                DASHBOARD KEPALA SEKOLAH
            </div>

            <h1>
                Monitoring Layanan Bimbingan Konseling
            </h1>

            <p>
                Ringkasan kondisi screening kesehatan mental
                dan layanan konseling siswa SMAN 1 Lohbener.
            </p>

        </div>


        <div class="header-actions">

            <a
                href="{{ route('principal.export') }}"
                class="btn-export"
            >
                📊 Export Laporan
            </a>

        </div>

    </div>


    {{-- =========================================================
         STATISTIK UTAMA
    ========================================================== --}}

    <div class="stats-grid">


        {{-- TOTAL SISWA --}}

        <div class="stat-card">

            <div class="stat-icon">
                👨‍🎓
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Total Siswa
                </span>

                <strong>
                    {{ number_format($totalStudents) }}
                </strong>

                <small>
                    Siswa terdaftar
                </small>

            </div>

        </div>


        {{-- TOTAL SCREENING --}}

        <div class="stat-card">

            <div class="stat-icon">
                📝
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Total Screening
                </span>

                <strong>
                    {{ number_format($totalAssessments) }}
                </strong>

                <small>
                    Screening DASS-21
                </small>

            </div>

        </div>


        {{-- SESI SELESAI --}}

        <div class="stat-card">

            <div class="stat-icon">
                💬
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Sesi Selesai
                </span>

                <strong>
                    {{ number_format($totalSessionsCompleted) }}
                </strong>

                <small>
                    Konseling selesai
                </small>

            </div>

        </div>


        {{-- RISIKO TINGGI --}}

        <div class="stat-card risk-card">

            <div class="stat-icon">
                ⚠️
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Risiko Tinggi
                </span>

                <strong>
                    {{ number_format($highRiskStudentsCount) }}
                </strong>

                <small>
                    {{ number_format($riskPercentage, 1) }}%
                    dari siswa yang screening
                </small>

            </div>

        </div>

    </div>


    {{-- =========================================================
         INFORMASI SCREENING
    ========================================================== --}}

    <div class="info-card">

        <div class="info-icon">
            ℹ️
        </div>

        <div>

            <strong>
                Informasi Screening
            </strong>

            <p>
                Dashboard menampilkan hasil screening DASS-21
                terbaru dari setiap siswa. Data digunakan sebagai
                informasi awal untuk membantu pemantauan layanan
                Bimbingan dan Konseling.
            </p>

        </div>

    </div>


    {{-- =========================================================
         GRAFIK
    ========================================================== --}}

    <div class="charts-grid">


        {{-- DISTRIBUSI RISIKO --}}

        <div class="chart-card">

            <div class="chart-header">

                <div>

                    <h3>
                        Distribusi Tingkat Risiko
                    </h3>

                    <p>
                        Berdasarkan hasil screening terbaru
                        setiap siswa.
                    </p>

                </div>

            </div>


            <div class="chart-container">

                <canvas id="riskChart"></canvas>

            </div>

        </div>


        {{-- SISWA PER KELAS --}}

        <div class="chart-card">

            <div class="chart-header">

                <div>

                    <h3>
                        Jumlah Siswa Berdasarkan Kelas
                    </h3>

                    <p>
                        Distribusi jumlah siswa pada setiap kelas.
                    </p>

                </div>

            </div>


            <div class="chart-container">

                <canvas id="classChart"></canvas>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RISIKO TINGGI PER KELAS
    ========================================================== --}}

    <div class="chart-card full-card">

        <div class="chart-header">

            <div>

                <h3>
                    Siswa Risiko Tinggi Berdasarkan Kelas
                </h3>

                <p>
                    Jumlah siswa dengan kategori Parah atau
                    Sangat Parah pada salah satu dimensi DASS-21.
                </p>

            </div>

        </div>


        <div class="chart-container large-chart">

            <canvas id="classRiskChart"></canvas>

        </div>

    </div>


    {{-- =========================================================
         HASIL SCREENING TERBARU
    ========================================================== --}}

    <div class="table-card">

        <div class="table-header">

            <div>

                <h3>
                    Hasil Screening Terbaru
                </h3>

                <p>
                    Hasil screening terbaru dari setiap siswa.
                </p>

            </div>

        </div>


        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Siswa
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Depresi
                        </th>

                        <th>
                            Kecemasan
                        </th>

                        <th>
                            Stres
                        </th>

                        <th>
                            Tanggal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($recentResults as $index => $result)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | KATEGORI DEPRESSION
                            |--------------------------------------------------------------------------
                            */

                            $depressionScore =
                                (int) ($result->depression_score ?? 0);

                            if ($depressionScore <= 9) {
                                $depressionCategory = 'Normal';
                            } elseif ($depressionScore <= 13) {
                                $depressionCategory = 'Ringan';
                            } elseif ($depressionScore <= 20) {
                                $depressionCategory = 'Sedang';
                            } elseif ($depressionScore <= 27) {
                                $depressionCategory = 'Parah';
                            } else {
                                $depressionCategory = 'Sangat Parah';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | KATEGORI ANXIETY
                            |--------------------------------------------------------------------------
                            */

                            $anxietyScore =
                                (int) ($result->anxiety_score ?? 0);

                            if ($anxietyScore <= 7) {
                                $anxietyCategory = 'Normal';
                            } elseif ($anxietyScore <= 9) {
                                $anxietyCategory = 'Ringan';
                            } elseif ($anxietyScore <= 14) {
                                $anxietyCategory = 'Sedang';
                            } elseif ($anxietyScore <= 19) {
                                $anxietyCategory = 'Parah';
                            } else {
                                $anxietyCategory = 'Sangat Parah';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | KATEGORI STRESS
                            |--------------------------------------------------------------------------
                            */

                            $stressScore =
                                (int) ($result->stress_score ?? 0);

                            if ($stressScore <= 14) {
                                $stressCategory = 'Normal';
                            } elseif ($stressScore <= 18) {
                                $stressCategory = 'Ringan';
                            } elseif ($stressScore <= 25) {
                                $stressCategory = 'Sedang';
                            } elseif ($stressScore <= 33) {
                                $stressCategory = 'Parah';
                            } else {
                                $stressCategory = 'Sangat Parah';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | CLASS CSS
                            |--------------------------------------------------------------------------
                            */

                            $depressionClass =
                                strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $depressionCategory
                                    )
                                );

                            $anxietyClass =
                                strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $anxietyCategory
                                    )
                                );

                            $stressClass =
                                strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $stressCategory
                                    )
                                );

                        @endphp


                        <tr>

                            {{-- NOMOR --}}

                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- SISWA --}}

                            <td>

                                <strong>
                                    {{ $result->student_name ?? '-' }}
                                </strong>

                            </td>


                            {{-- KELAS --}}

                            <td>
                                {{ $result->kelas ?? '-' }}
                            </td>


                            {{-- DEPRESI --}}

                            <td>

                                <span
                                    class="risk-badge {{ $depressionClass }}"
                                >
                                    {{ $depressionCategory }}
                                </span>

                            </td>


                            {{-- KECEMASAN --}}

                            <td>

                                <span
                                    class="risk-badge {{ $anxietyClass }}"
                                >
                                    {{ $anxietyCategory }}
                                </span>

                            </td>


                            {{-- STRES --}}

                            <td>

                                <span
                                    class="risk-badge {{ $stressClass }}"
                                >
                                    {{ $stressCategory }}
                                </span>

                            </td>


                            {{-- TANGGAL --}}

                            <td>

                                @if(!empty($result->created_at))

                                    {{ \Carbon\Carbon::parse($result->created_at)->format('d/m/Y') }}

                                @else

                                    -

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="empty-data"
                            >

                                Belum terdapat data
                                screening DASS-21.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
     CHART.JS
========================================================== --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | DATA DARI CONTROLLER
    |--------------------------------------------------------------------------
    */

    const severityData =
        @json($severityDistribution);

    const classLabels =
        @json($classLabels);

    const classStudentCounts =
        @json($classStudentCounts);

    const classHighRiskCounts =
        @json($classHighRiskCounts);


    /*
    |--------------------------------------------------------------------------
    | GRAFIK DISTRIBUSI RISIKO
    |--------------------------------------------------------------------------
    */

    const riskCanvas =
        document.getElementById('riskChart');


    if (riskCanvas) {

        new Chart(
            riskCanvas,
            {

                type: 'doughnut',

                data: {

                    labels: [
                        'Normal',
                        'Ringan',
                        'Sedang',
                        'Parah',
                        'Sangat Parah'
                    ],

                    datasets: [

                        {

                            data: severityData,

                            borderWidth: 2

                        }

                    ]

                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {

                            position: 'bottom'

                        }

                    }

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GRAFIK JUMLAH SISWA PER KELAS
    |--------------------------------------------------------------------------
    */

    const classCanvas =
        document.getElementById('classChart');


    if (classCanvas) {

        new Chart(
            classCanvas,
            {

                type: 'bar',

                data: {

                    labels: classLabels,

                    datasets: [

                        {

                            label: 'Jumlah Siswa',

                            data: classStudentCounts,

                            borderWidth: 1

                        }

                    ]

                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {

                                precision: 0

                            }

                        }

                    }

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GRAFIK RISIKO TINGGI PER KELAS
    |--------------------------------------------------------------------------
    */

    const classRiskCanvas =
        document.getElementById('classRiskChart');


    if (classRiskCanvas) {

        new Chart(
            classRiskCanvas,
            {

                type: 'bar',

                data: {

                    labels: classLabels,

                    datasets: [

                        {

                            label: 'Siswa Risiko Tinggi',

                            data: classHighRiskCounts,

                            borderWidth: 1

                        }

                    ]

                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {

                                precision: 0

                            }

                        }

                    }

                }

            }
        );

    }

});

</script>


{{-- =========================================================
     STYLE
========================================================== --}}

<style>


/*
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
*/

.principal-dashboard {

    max-width: 1400px;

    margin: 0 auto;

    padding: 30px;

    color: #26352b;

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.dashboard-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 30px;

    margin-bottom: 30px;

}


.dashboard-badge {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 8px 14px;

    border-radius: 999px;

    background: #e9f0e8;

    color: #526b56;

    font-size: 13px;

    font-weight: 700;

}


.dashboard-header h1 {

    margin: 15px 0 8px;

    font-size: 32px;

    font-weight: 800;

}


.dashboard-header p {

    margin: 0;

    color: #7b857d;

    font-size: 15px;

}


.header-actions {

    display: flex;

    gap: 10px;

}


.btn-export {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 12px 18px;

    border-radius: 10px;

    background: #5d735f;

    color: white;

    text-decoration: none;

    font-weight: 700;

    transition: 0.2s;

}


.btn-export:hover {

    background: #4c6250;

}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 20px;

    margin-bottom: 20px;

}


.stat-card {

    display: flex;

    align-items: center;

    gap: 18px;

    padding: 22px;

    background: white;

    border: 1px solid #e7ebe6;

    border-radius: 16px;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.04);

}


.stat-icon {

    width: 55px;

    height: 55px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 14px;

    background: #edf3ed;

    font-size: 25px;

    flex-shrink: 0;

}


.stat-content {

    display: flex;

    flex-direction: column;

    gap: 3px;

}


.stat-label {

    color: #7a837c;

    font-size: 13px;

    font-weight: 600;

}


.stat-content strong {

    font-size: 28px;

    color: #334b37;

}


.stat-content small {

    color: #909890;

    font-size: 12px;

}


.risk-card {

    border-color: #eaded8;

}


/*
|--------------------------------------------------------------------------
| INFO
|--------------------------------------------------------------------------
*/

.info-card {

    display: flex;

    align-items: flex-start;

    gap: 15px;

    padding: 18px 20px;

    margin-bottom: 20px;

    background: #f3f7f2;

    border: 1px solid #dce8db;

    border-radius: 14px;

}


.info-icon {

    font-size: 22px;

}


.info-card strong {

    color: #526b56;

}


.info-card p {

    margin: 5px 0 0;

    color: #718073;

    font-size: 14px;

    line-height: 1.6;

}


/*
|--------------------------------------------------------------------------
| CHART GRID
|--------------------------------------------------------------------------
*/

.charts-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;

    margin-bottom: 20px;

}


.chart-card {

    background: white;

    border: 1px solid #e7ebe6;

    border-radius: 16px;

    padding: 24px;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.04);

}


.full-card {

    margin-bottom: 20px;

}


.chart-header h3 {

    margin: 0 0 5px;

    font-size: 18px;

    color: #354d39;

}


.chart-header p {

    margin: 0;

    color: #89928b;

    font-size: 13px;

}


.chart-container {

    position: relative;

    height: 320px;

    margin-top: 20px;

}


.large-chart {

    height: 350px;

}


/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.table-card {

    background: white;

    border: 1px solid #e7ebe6;

    border-radius: 16px;

    padding: 24px;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.04);

}


.table-header {

    margin-bottom: 20px;

}


.table-header h3 {

    margin: 0 0 5px;

    font-size: 18px;

    color: #354d39;

}


.table-header p {

    margin: 0;

    color: #89928b;

    font-size: 13px;

}


.table-responsive {

    width: 100%;

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 850px;

}


thead {

    background: #f5f7f4;

}


th {

    padding: 13px 15px;

    text-align: left;

    font-size: 12px;

    color: #68736b;

    font-weight: 700;

}


td {

    padding: 14px 15px;

    border-top: 1px solid #edf0ed;

    font-size: 13px;

    color: #59635c;

}


tbody tr:hover {

    background: #fafcfa;

}


/*
|--------------------------------------------------------------------------
| RISK BADGES
|--------------------------------------------------------------------------
*/

.risk-badge {

    display: inline-block;

    padding: 5px 10px;

    border-radius: 999px;

    font-size: 11px;

    font-weight: 700;

    background: #eef2ee;

    color: #5d6c60;

}


.risk-badge.normal {

    background: #e8f3e9;

    color: #4f7555;

}


.risk-badge.ringan {

    background: #f0f4df;

    color: #707d42;

}


.risk-badge.sedang {

    background: #fff3d8;

    color: #947033;

}


.risk-badge.parah {

    background: #fce5df;

    color: #a45b4c;

}


.risk-badge.sangat-parah {

    background: #f6dddd;

    color: #9b3f3f;

}


/*
|--------------------------------------------------------------------------
| EMPTY DATA
|--------------------------------------------------------------------------
*/

.empty-data {

    text-align: center;

    padding: 40px !important;

    color: #929a94;

}


/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

@media (max-width: 1100px) {

    .stats-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .charts-grid {

        grid-template-columns:
            1fr;

    }

}


@media (max-width: 700px) {

    .principal-dashboard {

        padding: 18px;

    }


    .dashboard-header {

        flex-direction: column;

        align-items: flex-start;

    }


    .dashboard-header h1 {

        font-size: 25px;

    }


    .stats-grid {

        grid-template-columns:
            1fr;

    }


    .header-actions {

        width: 100%;

    }


    .btn-export {

        width: 100%;

        justify-content: center;

    }

}

</style>

@endsection