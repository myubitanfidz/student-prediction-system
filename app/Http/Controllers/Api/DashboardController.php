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

        return response()->json([
            'status' => 'success',
            'data'   => [
                'student'     => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'exam_stats'  => $examResults,
                'portfolio'   => $user->portfolio,
            ],
        ]);
    }
}