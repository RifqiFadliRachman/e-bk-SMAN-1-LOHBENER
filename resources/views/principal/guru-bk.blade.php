@extends('layouts.app')

@section('title', 'Daftar Guru BK')

@section('content')

@php
    $guruBKList = $guruBKList ?? collect();
@endphp


<div class="guru-bk-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="guru-bk-header">

        <div>

            <div class="guru-bk-badge">
                <span>👨‍🏫</span>
                MANAJEMEN GURU BK
            </div>

            <h1>
                Daftar Guru BK
            </h1>

            <p>
                Kelola data guru Bimbingan dan Konseling
                SMAN 1 Lohbener.
            </p>

        </div>


        <div class="guru-bk-header-actions">

            <a
                href="{{ route('principal.dashboard') }}"
                class="btn-back"
            >
                ← Kembali ke Dashboard
            </a>

        </div>

    </div>


    {{-- =========================================================
         FORM TAMBAH GURU BK
    ========================================================== --}}

    <div class="form-card">

        <div class="form-card-header">

            <h3>
                ➕ Tambah Guru BK Baru
            </h3>

            <p>
                Isi data di bawah untuk menambahkan
                akun Guru BK baru ke sistem.
            </p>

        </div>


        <form
            action="{{ route('principal.guru-bk.store') }}"
            method="POST"
            class="guru-bk-form"
        >

            @csrf


            <div class="form-grid">


                {{-- NAMA --}}

                <div class="form-group">

                    <label for="name">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>


                {{-- EMAIL --}}

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="contoh@email.com"
                        required
                    >

                </div>


                {{-- PASSWORD --}}

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        required
                        minlength="8"
                    >

                </div>


                {{-- KONFIRMASI PASSWORD --}}

                <div class="form-group">

                    <label for="password_confirmation">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password"
                        required
                        minlength="8"
                    >

                </div>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-submit"
                >
                    👨‍🏫 Tambah Guru BK
                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
         TABEL DAFTAR GURU BK
    ========================================================== --}}

    <div class="table-card">

        <div class="table-header">

            <div>

                <h3>
                    📋 Daftar Guru BK Terdaftar
                </h3>

                <p>
                    Total {{ $guruBKList->count() }} guru BK
                    terdaftar dalam sistem.
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
                            Nama
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

                    @forelse($guruBKList as $index => $guru)

                        <tr>

                            {{-- NOMOR --}}

                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- NAMA --}}

                            <td>

                                <div class="guru-name">

                                    <div class="guru-avatar">
                                        {{ strtoupper(substr($guru->name, 0, 1)) }}
                                    </div>

                                    <strong>
                                        {{ $guru->name }}
                                    </strong>

                                </div>

                            </td>


                            {{-- EMAIL --}}

                            <td>
                                {{ $guru->email }}
                            </td>


                            {{-- TANGGAL TERDAFTAR --}}

                            <td>

                                @if(!empty($guru->created_at))

                                    {{ \Carbon\Carbon::parse($guru->created_at)->format('d/m/Y H:i') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td>

                                <form
                                    action="{{ route('principal.guru-bk.destroy', $guru->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus Guru BK &quot;{{ $guru->name }}&quot;? Tindakan ini tidak dapat dibatalkan.')"
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

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="empty-data"
                            >

                                Belum terdapat data Guru BK.

                            </td>

                        </tr>

                    @endforelse

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

.guru-bk-page {

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

.guru-bk-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 30px;

    margin-bottom: 30px;

}


.guru-bk-badge {

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


.guru-bk-header h1 {

    margin: 15px 0 8px;

    font-size: 32px;

    font-weight: 800;

}


.guru-bk-header p {

    margin: 0;

    color: #7b857d;

    font-size: 15px;

}


.guru-bk-header-actions {

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
| FORM CARD
|--------------------------------------------------------------------------
*/

.form-card {

    background: white;

    border: 1px solid #e7ebe6;

    border-radius: 16px;

    padding: 28px;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.04);

    margin-bottom: 24px;

}


.form-card-header {

    margin-bottom: 24px;

}


.form-card-header h3 {

    margin: 0 0 5px;

    font-size: 18px;

    color: #354d39;

}


.form-card-header p {

    margin: 0;

    color: #89928b;

    font-size: 13px;

}


.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 18px;

}


.form-group {

    display: flex;

    flex-direction: column;

    gap: 6px;

}


.form-group label {

    font-size: 13px;

    font-weight: 700;

    color: #526b56;

}


.form-group input {

    padding: 12px 14px;

    border: 1px solid #d8ddd7;

    border-radius: 10px;

    font-size: 14px;

    color: #26352b;

    background: #fafcfa;

    transition: 0.2s;

    outline: none;

}


.form-group input:focus {

    border-color: #5d735f;

    box-shadow:
        0 0 0 3px rgba(93, 115, 95, 0.12);

}


.form-group input::placeholder {

    color: #b0b8b2;

}


.form-actions {

    margin-top: 22px;

    display: flex;

    justify-content: flex-end;

}


.btn-submit {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 12px 24px;

    border-radius: 10px;

    background: #5d735f;

    color: white;

    font-weight: 700;

    font-size: 14px;

    border: none;

    cursor: pointer;

    transition: 0.2s;

}


.btn-submit:hover {

    background: #4c6250;

    transform: translateY(-1px);

    box-shadow:
        0 4px 12px rgba(93, 115, 95, 0.25);

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

    min-width: 600px;

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
| GURU NAME
|--------------------------------------------------------------------------
*/

.guru-name {

    display: flex;

    align-items: center;

    gap: 12px;

}


.guru-avatar {

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


/*
|--------------------------------------------------------------------------
| DELETE BUTTON
|--------------------------------------------------------------------------
*/

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

    .form-grid {

        grid-template-columns:
            1fr;

    }

}


@media (max-width: 700px) {

    .guru-bk-page {

        padding: 18px;

    }


    .guru-bk-header {

        flex-direction: column;

        align-items: flex-start;

    }


    .guru-bk-header h1 {

        font-size: 25px;

    }


    .guru-bk-header-actions {

        width: 100%;

    }


    .btn-back {

        width: 100%;

        justify-content: center;

    }

}

</style>

@endsection
