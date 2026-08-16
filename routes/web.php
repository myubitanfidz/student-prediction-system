<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Frontend routes — Talenta Santri
|--------------------------------------------------------------------------
| Setiap route di bawah cuma me-render "cangkang" halaman (Blade view).
| Semua data asli (daftar ujian, soal, dashboard, dst.) diambil di sisi
| browser lewat fetch() ke API backend temanmu — lihat resources/js/api.js
| dan resources/js/app.js. Laravel di sini TIDAK query database sama
| sekali untuk halaman-halaman ini.
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('beranda'));

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

Route::get('/beranda', fn () => view('beranda.index'))->name('beranda');

Route::get('/ujian/{id}', fn (string $id) => view('ujian.kerjakan', ['examId' => $id]))
    ->name('ujian.kerjakan');
Route::get('/portofolio', fn () => view('portofolio.index'))->name('portofolio.index');

Route::get('/dashboard', fn () => view('dashboard.index'))->name('dashboard');
