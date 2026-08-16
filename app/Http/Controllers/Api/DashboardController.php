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

        // Compatibility is based only on multiple-choice questions with an
        // answer key. Essay answers are kept for teacher assessment.
        $examResults = Exam::with('questions')->get()->map(function ($exam) use ($user) {
            $answers = $user->answers->filter(function ($ans) use ($exam) {
                return $ans->question && $ans->question->exam_id === $exam->id;
            });

            $multipleChoiceCount = $exam->questions->where('type', 'multiple_choice')->count();
            $correctCount = $answers->filter(function ($answer) {
                return $answer->question?->type === 'multiple_choice'
                    && mb_strtolower(trim($answer->answer_text)) === mb_strtolower(trim((string) $answer->question->correct_answer));
            })->count();
            $compatibility = $multipleChoiceCount > 0
                ? (int) round(($correctCount / $multipleChoiceCount) * 100)
                : 0;

            return [
                'exam_id'       => $exam->id,
                'category'      => $exam->category,
                'subcategory'   => $exam->subcategory,
                'exam_title'    => $exam->title,
                'answered_count'=> $answers->count(),
                'question_count'=> $multipleChoiceCount,
                'correct_count' => $correctCount,
                'compatibility' => $compatibility,
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
