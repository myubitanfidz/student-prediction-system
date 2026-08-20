<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('beranda'));

// Autentikasi
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

// Halaman Santri
Route::get('/beranda', fn () => view('beranda.index'))->name('beranda');
Route::get('/ujian/{id}', fn (string $id) => view('ujian.kerjakan', ['examId' => $id]))->name('ujian.kerjakan');
Route::get('/portofolio', fn () => view('portofolio.index'))->name('portofolio.index');
Route::get('/profile', fn () => view('dashboard.index'))->name('profile');
Route::redirect('/dashboard', '/profile')->name('dashboard');

// Halaman Admin
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/koreksi/{userId}', fn (string $userId) => view('admin.koreksi', ['userId' => $userId]))->name('admin.koreksi');
    
    // Kelola Ujian & Soal
    Route::get('/exams', fn () => view('admin.exams.index'))->name('admin.exams.index');
    Route::get('/exams/{id}/questions', fn (string $id) => view('admin.exams.questions', ['examId' => $id]))->name('admin.exams.questions');
});