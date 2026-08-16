<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\PortfolioController;
use Illuminate\Support\Facades\Route;

// Laravel automatically prefixes every route in this file with /api.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/exams', [ExamController::class, 'index']);
Route::get('/exams/{id}', [ExamController::class, 'show']);
Route::post('/exams/submit', [ExamController::class, 'submit']);
Route::post('/exams/grade', [ExamController::class, 'gradeAnswer']);

Route::post('/portfolios', [PortfolioController::class, 'store']);
Route::get('/dashboard/{userId}', [DashboardController::class, 'show']);
