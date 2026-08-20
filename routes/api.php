<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AdminExamController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\PortfolioController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC AUTH ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- PROTECTED ROUTES (SANCTUM) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Santri Endpoints
    Route::middleware('role:student')->group(function () {
        Route::get('/exams', [ExamController::class, 'index']);
        Route::get('/exams/{id}', [ExamController::class, 'show']);
        Route::post('/exams/submit', [ExamController::class, 'submit']);
        Route::post('/portfolios', [PortfolioController::class, 'store']);
        Route::get('/dashboard', [DashboardController::class, 'show']);
    });

    // Admin Endpoints
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Students & Grading
        Route::get('/students', [AdminController::class, 'getStudents']);
        Route::get('/students/{userId}/answers', [AdminController::class, 'getStudentAnswers']);
        Route::post('/grade', [AdminController::class, 'gradeAnswer']);

        // Exam Package Management
        Route::get('/exams', [AdminExamController::class, 'index']);
        Route::post('/exams', [AdminExamController::class, 'storeExam']);
        Route::put('/exams/{id}', [AdminExamController::class, 'updateExam']);
        Route::delete('/exams/{id}', [AdminExamController::class, 'destroyExam']);

        // Questions Management
        Route::get('/exams/{examId}/questions', [AdminExamController::class, 'getQuestionsByExam']);
        Route::post('/questions', [AdminExamController::class, 'storeQuestion']);
        Route::put('/questions/{id}', [AdminExamController::class, 'updateQuestion']);
        Route::delete('/questions/{id}', [AdminExamController::class, 'destroyQuestion']);
    });
});