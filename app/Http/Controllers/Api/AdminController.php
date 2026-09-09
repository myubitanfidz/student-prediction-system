<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SecureId;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamCompletion;
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

        $allPeriods = Exam::whereNotNull('period_title')
            ->where('period_title', '!=', '')
            ->distinct()
            ->pluck('period_title')
            ->values();

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

                    $completion = $student->completions->firstWhere('exam_id', $exam->id);
                    $completed = (bool) $completion;

                    return [
                        'exam_id'         => $exam->hash_id,
                        'raw_exam_id'     => $exam->id,
                        'category'        => $exam->category,
                        'subcategory'     => $exam->subcategory,
                        'exam_title'      => $exam->title,
                        'title'           => $exam->title,
                        'period_title'    => $exam->period_title ?? 'PSB',
                        'answered_count'  => $answers->count(),
                        'mc_accuracy_pct' => $percentage,
                        'percentage'      => $percentage,
                        'score'           => $percentage,
                        'total_score'     => $answers->sum('score'),
                        'completed'       => $completed,
                        'retake_allowed'  => (bool) ($completion?->retake_allowed),
                    ];
                })->filter(fn ($stat) => $stat['answered_count'] > 0 || $stat['completed'])->values();

                $percentages = $examStats
                    ->map(fn ($stat) => (float) $stat['mc_accuracy_pct'])
                    ->filter(fn ($pct) => $pct > 0);

                $periodsAttended = $examStats->pluck('period_title')->unique()->values()->all();

                return [
                    'id'            => $student->hash_id,
                    'raw_id'        => $student->id,
                    'name'          => $student->name,
                    'email'         => $student->email,
                    'periods'       => $periodsAttended,
                    'tests_done'    => $student->completions->count(),
                    'highest_score' => $percentages->isNotEmpty() ? round($percentages->max(), 2) : 0,
                    'exam_stats'    => $examStats,
                    'career_predictions' => [
                        'Programming' => 85,
                        'DKV'         => 90,
                        'Komik'       => 75,
                        'Videografi'  => 80,
                    ],
                    'portfolio'     => $student->portfolio ? [
                        'links' => $student->portfolio->links,
                        'files' => $student->portfolio->files ?? [],
                    ] : null,
                    'has_portfolio' => ! is_null($student->portfolio),
                ];
            });

        return response()->json([
            'status'  => 'success',
            'data'    => $students,
            'periods' => $allPeriods,
        ]);
    }

    public function getStudentAnswers(Request $request, $userId): JsonResponse
    {
        // 1. Dekode ID User dari format Hash SecureId atau integer murni
        $realUserId = is_numeric($userId) ? (int)$userId : SecureId::decode($userId, 'user');

        $student = User::select('id', 'name', 'email')->find($realUserId);

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data santri tidak ditemukan.'
            ], 404);
        }

        // 2. Dekode exam_id dari format Hash SecureId atau integer murni
        $examIdFilter = $request->query('exam_id');
        $resolvedExamId = null;
        if ($examIdFilter) {
            $resolvedExamId = is_numeric($examIdFilter)
                ? (int)$examIdFilter
                : SecureId::decode($examIdFilter, 'exam');
        }

        // 3. Query butir jawaban santri
        $query = StudentAnswer::where('user_id', $realUserId)
            ->with(['question.exam']);

        if ($resolvedExamId) {
            $query->whereHas('question', function ($q) use ($resolvedExamId) {
                $q->where('exam_id', $resolvedExamId);
            });
        }

        $answers = $query->get()->map(function ($ans) {
            $question = $ans->question;
            $exam = $question?->exam;

            return [
                'answer_id'       => $ans->id,
                'question_id'     => $question?->hash_id ?? $question?->id,
                'question_text'   => $question?->question_text,
                'question_type'   => $question?->type,
                'correct_answer'  => $question?->correct_answer,
                'gclwama_tag'     => $question?->gclwama_tag,
                'exam_title'      => $exam?->title,
                'student_answer'  => $ans->answer_text,
                'file_url'        => $ans->file_path ? asset('storage/' . $ans->file_path) : null,
                'current_score'   => $ans->score,
                'is_auto_graded'  => ($question?->type === 'essay' && !empty($question->correct_answer) && $ans->score !== null),
            ];
        });

        return response()->json([
            'status'  => 'success',
            'data'    => [
                'student' => $student,
                'answers' => $answers,
            ]
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

    public function allowRetake(Request $request): JsonResponse
    {
        $realUserId = is_numeric($request->user_id) ? (int)$request->user_id : SecureId::decode($request->user_id, 'user');
        $realExamId = is_numeric($request->exam_id) ? (int)$request->exam_id : SecureId::decode($request->exam_id, 'exam');

        $student = User::find($realUserId);
        if (! $student || $student->role !== 'student') {
            return response()->json(['message' => 'Santri tidak ditemukan'], 404);
        }

        $completion = ExamCompletion::where('user_id', $realUserId)
            ->where('exam_id', $realExamId)
            ->first();

        if (! $completion) {
            return response()->json(['message' => 'Santri belum menyelesaikan ujian ini'], 404);
        }

        $completion->update(['retake_allowed' => true]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Izin mengerjakan ulang berhasil diberikan',
            'data'    => $completion,
        ]);
    }

    public function regradeWithAi(Request $request): JsonResponse
    {
        $request->validate([
            'answer_id' => 'required|exists:student_answers,id'
        ]);
    
        $answer = StudentAnswer::with('question')->findOrFail($request->answer_id);
    
        if (empty($answer->question?->correct_answer)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Soal ini belum memiliki kunci referensi AI.'
            ], 422);
        }
    
        $score = \App\Services\AiGradingService::grade(
            $answer->question->correct_answer,
            $answer->answer_text ?? ''
        );
    
        if ($score === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi service AI Python. Periksa terminal Uvicorn.'
            ], 500);
        }
    
        $answer->update(['score' => $score]);
    
        return response()->json([
            'status'  => 'success',
            'message' => "Berhasil dinilai oleh AI! Skor: {$score}",
            'score'   => $score
        ]);
    }
}   