<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['portfolio', 'answers.question.exam']);

        // 1. Existing Exam Stats Logic
        $examResults = Exam::all()->map(function ($exam) use ($user) {
            $answers = $user->answers->filter(fn ($ans) => $ans->question && $ans->question->exam_id === $exam->id);
            $mcAnswers = $answers->filter(fn ($ans) => $ans->question->type === 'multiple_choice');

            $mcTotal = $mcAnswers->count();
            $mcCorrect = $mcAnswers->where('score', '>=', 100)->count();
            $percentage = $mcTotal > 0 ? round(($mcCorrect / $mcTotal) * 100, 2) : 0;

            return [
                'exam_id'         => $exam->id,
                'category'        => $exam->category,
                'subcategory'     => $exam->subcategory,
                'exam_title'      => $exam->title,
                'answered_count'  => $answers->count(),
                'mc_accuracy_pct' => $percentage,
                'total_score'     => $answers->sum('score'),
            ];
        });

        // 2. NEW: GCLWAMA to Career Path Engine
        $allAnswers = $user->answers->filter(fn ($ans) => $ans->question);
        
        $categoryScores = [];
        $categoryCounts = [];

        // Group scores by category (G, C, L, W, A, M, A)
        foreach ($allAnswers as $ans) {
            $cat = strtolower(trim($ans->question->category ?? ''));
            if (!$cat) continue;

            if (!isset($categoryScores[$cat])) {
                $categoryScores[$cat] = 0;
                $categoryCounts[$cat] = 0;
            }
            $categoryScores[$cat] += (float) ($ans->score ?? 0);
            $categoryCounts[$cat]++;
        }

        // Average out each category
        $avgCat = [];
        foreach ($categoryScores as $cat => $score) {
            $avgCat[$cat] = $categoryCounts[$cat] > 0 ? ($score / $categoryCounts[$cat]) : 0;
        }

        // Helper function to safely average multiple categories for a specific career
        $getScore = function (...$cats) use ($avgCat) {
            $validCats = collect($cats)->map(fn($c) => strtolower($c))->filter(fn($c) => isset($avgCat[$c]));
            return $validCats->count() > 0 ? $validCats->map(fn($c) => $avgCat[$c])->average() : 0;
        };

        // Map categories to real IT & Creative Roles
        $careerPredictions = [
            'Programmer (Software Engineer)' => round($getScore('algoritma', 'matematika'), 1),
            'DKV (Desainer Visual)'          => round($getScore('gambar', 'warna', 'layout'), 1),
            'Animator (2D/3D)'               => round($getScore('animasi', 'gambar', 'cerita'), 1),
            'Videographer / Video Editor'    => round($getScore('cerita', 'warna', 'layout', 'audio'), 1),
        ];

        // Sort from highest inclination to lowest
        arsort($careerPredictions);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'student'     => [
                    'id'                 => $user->id,
                    'name'               => $user->name,
                    'email'              => $user->email,
                    'career_predictions' => $careerPredictions, // Injected into student data
                ],
                'exam_stats'  => $examResults,
                'portfolio'   => $user->portfolio,
            ],
        ]);
    }
}