<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;

class DashboardController extends Controller
{
    public function show($userId)
    {
        $user = User::with(['portfolio', 'answers.question.exam'])->find($userId);

        if (!$user) {
            return response()->json(['message' => 'Santri tidak ditemukan'], 404);
        }

        // Rekap nilai ujian per subkategori
        $examResults = Exam::all()->map(function ($exam) use ($user) {
            $answers = $user->answers->filter(function ($ans) use ($exam) {
                return $ans->question && $ans->question->exam_id === $exam->id;
            });

            $totalScore = $answers->sum('score');
            $answeredCount = $answers->count();

            return [
                'exam_id'       => $exam->id,
                'category'      => $exam->category,
                'subcategory'   => $exam->subcategory,
                'exam_title'    => $exam->title,
                'answered_count'=> $answeredCount,
                'total_score'   => $totalScore,
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