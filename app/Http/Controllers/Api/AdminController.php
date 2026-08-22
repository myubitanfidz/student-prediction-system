<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function getStudents(): JsonResponse
    {
        $exams = Exam::all();

        $students = User::where('role', 'student')
            ->with(['portfolio', 'answers.question.exam', 'completions'])
            ->get()
            ->map(function ($student) use ($exams) {
                $examStats = $exams->map(function ($exam) use ($student) {
                    $answers = $student->answers->filter(
                        fn ($ans) => $ans->question && $ans->question->exam_id === $exam->id
                    );
                    $mcAnswers = $answers->filter(fn ($ans) => $ans->question->type === 'multiple_choice');

                    $mcTotal = $mcAnswers->count();
                    $mcCorrect = $mcAnswers->where('score', '>=', 100)->count();
                    $percentage = $mcTotal > 0 ? round(($mcCorrect / $mcTotal) * 100, 2) : 0;

                    $completed = $student->completions->contains('exam_id', $exam->id);

                    return [
                        'exam_id'         => $exam->id,
                        'category'        => $exam->category,
                        'subcategory'     => $exam->subcategory,
                        'exam_title'      => $exam->title,
                        'title'           => $exam->title,
                        'answered_count'  => $answers->count(),
                        'mc_accuracy_pct' => $percentage,
                        'percentage'      => $percentage,
                        'score'           => $percentage,
                        'total_score'     => $answers->sum('score'),
                        'completed'       => $completed,
                    ];
                })->filter(fn ($stat) => $stat['answered_count'] > 0 || $stat['completed'])->values();

                $percentages = $examStats
                    ->map(fn ($stat) => (float) $stat['mc_accuracy_pct'])
                    ->filter(fn ($pct) => $pct > 0);

                return [
                    'id'            => $student->id,
                    'name'          => $student->name,
                    'email'         => $student->email,
                    'login_count'   => (int) ($student->login_count ?? 0),
                    'participated'  => (int) ($student->login_count ?? 0) > 0 ? 1 : 0,
                    'tests_done'    => $student->completions->count(),
                    'highest_score' => $percentages->isNotEmpty() ? round($percentages->max(), 2) : 0,
                    'exam_stats'    => $examStats,
                    'portfolio'     => $student->portfolio ? [
                        'links' => $student->portfolio->links,
                        'files' => $student->portfolio->files ?? [],
                    ] : null,
                    'has_portfolio' => ! is_null($student->portfolio),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $students,
        ]);
    }

    public function getStudentAnswers($userId): JsonResponse
    {
        $student = User::with(['portfolio', 'answers.question.exam'])->find($userId);

        if (! $student || $student->role !== 'student') {
            return response()->json(['message' => 'Santri tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'student'   => $student->only(['id', 'name', 'email']),
                'portfolio' => $student->portfolio,
                'answers'   => $student->answers->map(fn ($ans) => [
                    'answer_id'      => $ans->id,
                    'exam_title'     => $ans->question->exam->title ?? '-',
                    'question_type'  => $ans->question->type,
                    'question_text'  => $ans->question->question_text,
                    'student_answer' => $ans->answer_text,
                    'current_score'  => $ans->score,
                ]),
            ],
        ]);
    }

    public function gradeAnswer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'answer_id' => 'required|exists:student_answers,id',
            'score'     => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $answer = StudentAnswer::find($request->answer_id);
        $answer->update(['score' => $request->score]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Nilai berhasil disimpan',
            'data'    => $answer,
        ]);
    }
}
