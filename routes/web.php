<?php

use Illuminate\Support\Facades\Route;

// Redirect awal
Route::get('/', fn () => redirect()->route('beranda'));
Route::redirect('/home', '/beranda');

// --- AUTENTIKASI ---
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

// --- HALAMAN SANTRI ---
// Beranda Utama
Route::get('/beranda', fn () => view('student.home'))->name('beranda');

// Halaman Modul Pilihan (Disatukan ke explore.blade.php dengan parameter kategori)
Route::get('/beranda/bahasa', fn () => view('student.ujian.explore', ['category' => 'bahasa']))->name('beranda.bahasa');
Route::get('/beranda/it', fn () => view('student.ujian.explore', ['category' => 'it']))->name('beranda.it');

// Pengerjaan Ujian & Hasil Skor Ujian
Route::get('/ujian/{id}', fn (string $id) => view('student.ujian.kerjakan', ['examId' => $id]))->name('ujian.kerjakan');
Route::get('/hasil/{id}', fn (string $id) => view('student.ujian.hasil', ['examId' => $id]))->name('ujian.hasil');

// Portofolio & Profil Riwayat Ujian
Route::get('/portofolio', fn () => view('student.portofolio'))->name('portofolio.index');
Route::get('/profile', fn () => view('student.Profile'))->name('profile');
Route::redirect('/dashboard', '/profile')->name('dashboard');

// --- HALAMAN ADMIN & GURU ---
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/koreksi/{userId}', fn (string $userId) => view('admin.koreksi', ['userId' => $userId]))->name('admin.koreksi');
    Route::get('/exams', fn () => view('admin.exams.index'))->name('admin.exams.index');
    Route::get('/exams/{id}/questions', fn (string $id) => view('admin.exams.questions', ['examId' => $id]))->name('admin.exams.questions');
});