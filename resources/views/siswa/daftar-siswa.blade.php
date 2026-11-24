@extends('layouts.app')

@section('title', $pageTitle ?? 'Daftar Siswa')

@section('content')

@php
    $siswaList = $siswaList ?? collect();
    $kelasList = $kelasList ?? collect();
    $filterKelas = $filterKelas ?? null;
    $search = $search ?? '';
    $deleteRouteName = $deleteRouteName ?? '';
    $backRoute = $backRoute ?? 'home';
    $backLabel = $backLabel ?? '← Kembali';
    $userRole = $userRole ?? '';
@endphp


<div class="siswa-page" x-data="liveSearchSiswa()">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="siswa-header">

        <div>

            <div class="siswa-badge">
                <span>🎓</span>
                DAFTAR SISWA
            </div>

            <h1>
                Daftar Siswa Terdaftar
            </h1>

            <p>
                Kelola data siswa yang terdaftar di
                sistem E-BK SMAN 1 Lohbener.
            </p>

        </div>


        <div class="siswa-header-actions">

            <a
                href="{{ route($backRoute) }}"
                class="btn-back"
            >
                {{ $backLabel }}
            </a>

        </div>

    </div>


    {{-- =========================================================
         STATISTIK RINGKAS
    ========================================================== --}}

    <div class="stats-row">

        <div class="stat-card">

            <div class="stat-label">
                Total Siswa
            </div>

            <div class="stat-value">
                {{ $siswaList->count() }}
            </div>

            <div class="stat-hint">
                @if($filterKelas && $filterKelas !== 'semua')
                    Kelas {{ $filterKelas }}
                @else
                    Semua Kelas
                @endif
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Jumlah Kelas
            </div>

            <div class="stat-value">
                {{ $kelasList->count() }}
            </div>

            <div class="stat-hint">
                Kelas Aktif
            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTER KELAS
    ========================================================== --}}

    <div class="filter-card">

        <div class="filter-header">

            <h3>
                🔍 Filter Berdasarkan Kelas
            </h3>

        </div>


        <div class="filter-buttons">

            <a
                href="{{ route(
                    $userRole === 'guru_bk'
                        ? 'counselor.siswa'
                        : 'principal.siswa'
                ) }}"
                class="filter-btn {{ !$filterKelas || $filterKelas === 'semua' ? 'active' : '' }}"
            >
                Semua Kelas
            </a>


            @foreach($kelasList as $kelas)

                <a
                    href="{{ route(
                        $userRole === 'guru_bk'
                            ? 'counselor.siswa'
                            : 'principal.siswa',
                        ['kelas' => $kelas]
                    ) }}"
                    class="filter-btn {{ $filterKelas === $kelas ? 'active' : '' }}"
                >
                    {{ $kelas }}
                </a>

            @endforeach

        </div>

    </div>


    {{-- =========================================================
         TABEL DAFTAR SISWA & LIVE SEARCH
    ========================================================== --}}

    <div class="table-card">

        <div class="table-header">

            <div>

                <h3>
                    📋 Daftar Siswa
                    @if($filterKelas && $filterKelas !== 'semua')
                        - Kelas {{ $filterKelas }}
                    @endif
                </h3>

                <p>
                    <span x-show="!searchQuery.trim()">
                        Menampilkan {{ $siswaList->count() }} siswa
                        @if($filterKelas && $filterKelas !== 'semua')
                            dari kelas {{ $filterKelas }}.
                        @else
                            dari semua kelas.
                        @endif
                    </span>
                    <span x-show="searchQuery.trim() !== ''" style="display: none;">
                        Menampilkan <strong x-text="visibleCount"></strong> dari {{ $siswaList->count() }} siswa untuk pencarian "<strong x-text="searchQuery"></strong>".
                    </span>
                </p>

            </div>

            {{-- LIVE SEARCH INPUT BOX --}}
            <div class="search-bar-wrapper">
                <div class="search-input-box">
                    <span class="search-input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>

                    <input
                        type="text"
                        x-model="searchQuery"
                        @input="updateVisibleCount()"
                        @keydown.escape="resetSearch()"
                        placeholder="Cari nama, kelas, atau email..."
                        class="live-search-input"
                        autocomplete="off"
                    >

                    <button
                        type="button"
                        x-show="searchQuery.trim() !== ''"
                        @click="resetSearch()"
                        class="search-clear-btn"
                        title="Hapus pencarian (Esc)"
                        style="display: none;"
                    >
                        ✕
                    </button>
                </div>

                <div class="search-meta-badge" title="Pencarian langsung tanpa reload halaman">
                    <span class="pulse-indicator"></span>
                    <span>Live Search</span>
                </div>
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
                            Nama
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Tanggal Terdaftar
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($siswaList as $index => $siswa)

                        <tr
                            class="siswa-row"
                            x-show="matchesSearch(@js($siswa->name), @js($siswa->kelas ?? ''), @js($siswa->email))"
                        >

                            {{-- NOMOR (Dihitung Dinamis via CSS Counter) --}}

                            <td class="row-num"></td>


                            {{-- NAMA --}}

                            <td>

                                <div class="siswa-name">

                                    <div class="siswa-avatar">
                                        {{ strtoupper(substr($siswa->name, 0, 1)) }}
                                    </div>

                                    <div>

                                        <strong>
                                            {{ $siswa->name }}
                                        </strong>

                                        @if($siswa->nama_orang_tua)
                                            <div class="siswa-parent">
                                                Orang tua: {{ $siswa->nama_orang_tua }}
                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- KELAS --}}

                            <td>

                                @if($siswa->kelas)

                                    <span class="kelas-badge">
                                        {{ $siswa->kelas }}
                                    </span>

                                @else

                                    <span class="kelas-empty">
                                        Belum diatur
                                    </span>

                                @endif

                            </td>


                            {{-- EMAIL --}}

                            <td>
                                {{ $siswa->email }}
                            </td>


                            {{-- TANGGAL TERDAFTAR --}}

                            <td>

                                @if(!empty($siswa->created_at))

                                    {{ \Carbon\Carbon::parse($siswa->created_at)->format('d/m/Y H:i') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td>

                                <div class="action-buttons">

                                    @if($userRole === 'guru_bk')

                                        <a
                                            href="{{ route('counselor.student-profile', $siswa->id) }}"
                                            class="btn-profile"
                                        >
                                            👤 Profil
                                        </a>

                                    @endif


                                    <form
                                        action="{{ route($deleteRouteName, $siswa->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa &quot;{{ $siswa->name }}&quot;?\n\nSemua data terkait (hasil DASS-21, booking konseling, riwayat chat) juga akan dihapus.\n\nTindakan ini tidak dapat dibatalkan.')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-delete"
                                        >
                                            🗑️ Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="empty-data"
                            >

                                @if($filterKelas && $filterKelas !== 'semua')
                                    Tidak ada siswa terdaftar di kelas {{ $filterKelas }}.
                                @else
                                    Belum terdapat data siswa yang terdaftar.
                                @endif

                            </td>

                        </tr>

                    @endforelse

                    {{-- ROW KETIKA HASIL PENCARIAN KOSONG --}}
                    @if($siswaList->count() > 0)
                        <tr x-show="visibleCount === 0" style="display: none;">
                            <td colspan="6" class="empty-data">
                                <div class="empty-search-state">
                                    <div class="empty-search-icon">🔍</div>
                                    <div class="empty-search-title">Tidak ada siswa yang cocok</div>
                                    <div class="empty-search-desc">
                                        Tidak ditemukan siswa dengan kata kunci "<span x-text="searchQuery" style="font-weight: 700; color: #354d39;"></span>" pada nama, kelas, atau email.
                                    </div>
                                    <button
                                        type="button"
                                        @click="resetSearch()"
                                        class="btn-reset-search"
                                    >
                                        ↺ Reset Pencarian
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
     STYLE
========================================================== --}}

<style>


/*
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
*/

.siswa-page {

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

.siswa-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 30px;

    margin-bottom: 30px;

}


.siswa-badge {

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


.siswa-header h1 {

    margin: 15px 0 8px;

    font-size: 32px;

    font-weight: 800;

}


.siswa-header p {

    margin: 0;

    color: #7b857d;

    font-size: 15px;

}


.siswa-header-actions {

    display: flex;

    gap: 10px;

}


.btn-back {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 12px 18px;

    border-radius: 10px;

    background: #5d735f;

    color: white;

    text-decoration: none;

    font-weight: 700;

    font-size: 14px;

    transition: 0.2s;

}


.btn-back:hover {

    background: #4c6250;

}


/*
|--------------------------------------------------------------------------
| STATS ROW
|--------------------------------------------------------------------------
*/

.stats-row {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 16px;

    margin-bottom: 24px;

}


.stat-card {

    background: white;

    border: 1px solid #e7ebe6;

    border-radius: 16px;

    padding: 20px 24px;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.04);

}


.stat-label {

    font-size: 12px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 0.5px;

    color: #89928b;

}


.stat-value {

    font-size: 28px;

    font-weight: 800;

    color: #5d735f;

    margin: 4px 0;

}


.stat-hint {

    font-size: 12px;

    font-weight: 600;

    color: #89928b;

}


/*
|--------------------------------------------------------------------------
| FILTER CARD
|--------------------------------------------------------------------------
*/

.filter-card {

    background: white;

    border: 1px solid #e7ebe6;

    border-radius: 16px;

    padding: 22px 24px;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.04);

    margin-bottom: 24px;

}


.filter-header h3 {

    margin: 0 0 14px;

    font-size: 16px;

    color: #354d39;

}


.filter-buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 8px;

}


.filter-btn {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    padding: 9px 16px;

    border-radius: 10px;

    font-size: 13px;

    font-weight: 700;

    text-decoration: none;

    color: #5c6e5e;

    background: #f5f7f4;

    border: 1px solid #d8ddd7;

    transition: 0.2s;

}


.filter-btn:hover {

    background: #eaf0e9;

    border-color: #bcc5bc;

}


.filter-btn.active {

    background: #5d735f;

    color: white;

    border-color: #5d735f;

}


/*
|--------------------------------------------------------------------------
| TABLE CARD
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

    display: flex;

    justify-content: space-between;

    align-items: center;

    flex-wrap: wrap;

    gap: 16px;

    margin-bottom: 20px;

}


.search-bar-wrapper {

    display: flex;

    align-items: center;

    gap: 12px;

    flex-wrap: wrap;

}


.search-input-box {

    position: relative;

    display: flex;

    align-items: center;

}


.search-input-icon {

    position: absolute;

    left: 12px;

    color: #89928b;

    pointer-events: none;

    display: flex;

    align-items: center;

}


.live-search-input {

    width: 320px;

    padding: 10px 38px 10px 38px;

    border: 1px solid #d8ddd7;

    border-radius: 12px;

    background: #fdfdfc;

    font-size: 13px;

    color: #26352b;

    outline: none;

    transition: all 0.2s ease;

}


.live-search-input:focus {

    border-color: #5d735f;

    background: #ffffff;

    box-shadow: 0 0 0 3px rgba(93, 115, 95, 0.15);

}


.search-clear-btn {

    position: absolute;

    right: 10px;

    background: #e9f0e8;

    color: #526b56;

    border: none;

    border-radius: 50%;

    width: 22px;

    height: 22px;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

    font-size: 11px;

    font-weight: 700;

    transition: 0.15s;

}


.search-clear-btn:hover {

    background: #d4e2d3;

    color: #26352b;

}


.search-meta-badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 7px 12px;

    background: #f5f7f4;

    border: 1px solid #e7ebe6;

    border-radius: 10px;

    font-size: 12px;

    color: #68736b;

    font-weight: 600;

}


.pulse-indicator {

    width: 8px;

    height: 8px;

    background-color: #526b56;

    border-radius: 50%;

    display: inline-block;

    box-shadow: 0 0 0 rgba(82, 107, 86, 0.4);

    animation: pulseGlow 1.8s infinite;

}


@keyframes pulseGlow {

    0% {

        box-shadow: 0 0 0 0 rgba(82, 107, 86, 0.5);

    }

    70% {

        box-shadow: 0 0 0 6px rgba(82, 107, 86, 0);

    }

    100% {

        box-shadow: 0 0 0 0 rgba(82, 107, 86, 0);

    }

}


tbody {

    counter-reset: rowNumber;

}


tbody tr.siswa-row:not([style*="display: none"]):not([style*="display:none"]) {

    counter-increment: rowNumber;

}


tbody tr.siswa-row:not([style*="display: none"]):not([style*="display:none"]) td.row-num::before {

    content: counter(rowNumber);

}


.empty-search-state {

    padding: 36px 20px;

    text-align: center;

}


.empty-search-icon {

    font-size: 32px;

    margin-bottom: 10px;

    opacity: 0.8;

}


.empty-search-title {

    font-size: 16px;

    font-weight: 700;

    color: #354d39;

    margin-bottom: 6px;

}


.empty-search-desc {

    font-size: 13px;

    color: #89928b;

    margin-bottom: 16px;

    max-width: 450px;

    margin-left: auto;

    margin-right: auto;

}


.btn-reset-search {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 8px 16px;

    background: #5d735f;

    color: white;

    border: none;

    border-radius: 10px;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    transition: 0.2s;

}


.btn-reset-search:hover {

    background: #4c6250;

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

    min-width: 700px;

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
| SISWA NAME
|--------------------------------------------------------------------------
*/

.siswa-name {

    display: flex;

    align-items: center;

    gap: 12px;

}


.siswa-avatar {

    width: 36px;

    height: 36px;

    border-radius: 50%;

    background: #5d735f;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: 700;

    font-size: 14px;

    flex-shrink: 0;

}


.siswa-parent {

    font-size: 11px;

    color: #89928b;

    margin-top: 2px;

}


/*
|--------------------------------------------------------------------------
| KELAS BADGE
|--------------------------------------------------------------------------
*/

.kelas-badge {

    display: inline-flex;

    align-items: center;

    padding: 5px 12px;

    border-radius: 8px;

    background: #e9f0e8;

    color: #526b56;

    font-size: 12px;

    font-weight: 700;

    border: 1px solid #c5d5c3;

}


.kelas-empty {

    font-size: 12px;

    color: #b0b8b2;

    font-style: italic;

}


/*
|--------------------------------------------------------------------------
| ACTION BUTTONS
|--------------------------------------------------------------------------
*/

.action-buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 6px;

}


.btn-profile {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    padding: 7px 14px;

    border-radius: 8px;

    background: #f0f4ef;

    color: #526b56;

    font-weight: 700;

    font-size: 12px;

    border: 1px solid #c5d5c3;

    text-decoration: none;

    transition: 0.2s;

}


.btn-profile:hover {

    background: #e2ebe1;

    border-color: #a8bba6;

    transform: translateY(-1px);

}


.btn-delete {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    padding: 7px 14px;

    border-radius: 8px;

    background: #fce5df;

    color: #a45b4c;

    font-weight: 700;

    font-size: 12px;

    border: 1px solid #f0c5ba;

    cursor: pointer;

    transition: 0.2s;

}


.btn-delete:hover {

    background: #f6d5cd;

    border-color: #e5a99a;

    transform: translateY(-1px);

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

@media (max-width: 800px) {

    .stats-row {

        grid-template-columns:
            1fr;

    }

    .live-search-input {
        width: 100%;
    }

    .search-bar-wrapper {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }

    .search-input-box {
        width: 100%;
    }

}


@media (max-width: 700px) {

    .siswa-page {

        padding: 18px;

    }


    .siswa-header {

        flex-direction: column;

        align-items: flex-start;

    }


    .siswa-header h1 {

        font-size: 25px;

    }


    .siswa-header-actions {

        width: 100%;

    }


    .btn-back {

        width: 100%;

        justify-content: center;

    }

}

</style>

<script>
function liveSearchSiswa() {
    return {
        searchQuery: @js($search ?? ''),
        visibleCount: {{ $siswaList->count() }},

        matchesSearch(name, kelas, email) {
            if (!this.searchQuery || !this.searchQuery.trim()) {
                return true;
            }

            const q = this.searchQuery.toLowerCase().trim();
            const n = (name || '').toLowerCase();
            const k = (kelas || '').toLowerCase();
            const e = (email || '').toLowerCase();

            return n.includes(q) || k.includes(q) || e.includes(q);
        },

        updateVisibleCount() {
            this.$nextTick(() => {
                const rows = document.querySelectorAll('tbody tr.siswa-row');
                let count = 0;
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        count++;
                    }
                });
                this.visibleCount = count;
            });
        },

        resetSearch() {
            this.searchQuery = '';
            this.updateVisibleCount();
        },

        init() {
            this.updateVisibleCount();
        }
    };
}
</script>

@endsection
