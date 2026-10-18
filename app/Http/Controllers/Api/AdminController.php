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
        ->map(fn ($student) => $this->buildStudentPayload($student, $exams));

    return response()->json([
        'status'  => 'success',
        'data'    => $students,
        'periods' => $allPeriods,
    ]);
}

/**
 * Build a single student's full payload for the admin dashboard.
 */
private function buildStudentPayload(User $student, $exams): array
{
    $examStats       = $this->buildExamStats($student, $exams);
    $periodsAttended = $examStats->pluck('period_title')->unique()->values()->all();
    $percentages     = $examStats->pluck('mc_accuracy_pct')
        ->map(fn ($pct) => (float) $pct)
        ->filter(fn ($pct) => $pct > 0);

    return [
        'id'                 => $student->hash_id,
        'raw_id'             => $student->id,
        'name'               => $student->name,
        'email'              => $student->email,
        'periods'            => $periodsAttended,
        'tests_done'         => $student->completions->count(),
        'highest_score'      => $percentages->isNotEmpty() ? round($percentages->max(), 2) : 0,
        'exam_stats'         => $examStats,
        'career_predictions' => $this->buildCareerPredictions($student),
        'gclwama_breakdown'  => $this->buildGclwamaBreakdown($student),
        'bahasa_scores'      => $this->buildBahasaScores($student),
        'portfolio'          => $student->portfolio ? [
            'links' => $student->portfolio->links,
            'files' => $student->portfolio->files ?? [],
        ] : null,
        'has_portfolio'      => ! is_null($student->portfolio),
    ];
}

/**
 * Per-exam stats for one student.
 */
private function buildExamStats(User $student, $exams)
{
    return $exams->map(function ($exam) use ($student) {
        $answers   = $student->answers->filter(
            fn ($ans) => $ans->question && $ans->question->exam_id === $exam->id
        );
        $mcAnswers = $answers->filter(fn ($ans) => $ans->question->type === 'multiple_choice');

        $mcTotal   = $mcAnswers->count();
        $mcCorrect = $mcAnswers->where('score', '>=', 100)->count();
        $mcWrong   = $mcAnswers->where('score', '=', 0)->count();
        $ungraded  = $answers->whereNull('score')->count();
        $percentage = $mcTotal > 0 ? round(($mcCorrect / $mcTotal) * 100, 2) : 0;

        $completion = $student->completions->firstWhere('exam_id', $exam->id);

        return [
            'exam_id'          => $exam->hash_id,
            'raw_exam_id'      => $exam->id,
            'hash_id'          => $exam->hash_id,
            'category'         => $exam->category,
            'subcategory'      => $exam->subcategory,
            'exam_title'       => $exam->title,
            'title'            => $exam->title,
            'period_title'     => $exam->period_title ?? 'PSB',
            'answered_count'   => $answers->count(),
            'mc_total_count'   => $mcTotal,
            'mc_correct_count' => $mcCorrect,
            'mc_wrong_count'   => $mcWrong,
            'ungraded_count'   => $ungraded,
            'mc_accuracy_pct'  => $percentage,
            'percentage'       => $percentage,
            'score'            => $percentage,
            'total_score'      => $answers->sum('score'),
            'completed'        => (bool) $completion,
            'retake_allowed'   => (bool) ($completion?->retake_allowed),
        ];
    })->filter(fn ($stat) => $stat['answered_count'] > 0 || $stat['completed'])->values();
}

/**
 * The 7-dimension GCLWAMA breakdown for one student.
 */
private function buildGclwamaBreakdown(User $student): array
{
    $tagScores = [
        'G'           => [],
        'C'           => [],
        'L'           => [],
        'W'           => [],
        'A_animasi'   => [],
        'M'           => [],
        'A_algoritma' => [],
    ];

    foreach ($student->answers as $ans) {
        $tag = $ans->question?->gclwama_tag;
        if ($tag && isset($tagScores[$tag]) && $ans->score !== null) {
            $tagScores[$tag][] = (float) $ans->score;
        }
    }

    $avgTag = [];
    foreach ($tagScores as $tag => $scores) {
        $avgTag[$tag] = count($scores) > 0
            ? round(array_sum($scores) / count($scores), 1)
            : 0;
    }

    return [
        'Gambar (G)'     => $avgTag['G'],
        'Cerita (C)'     => $avgTag['C'],
        'Layout (L)'     => $avgTag['L'],
        'Warna (W)'      => $avgTag['W'],
        'Animasi (A)'    => $avgTag['A_animasi'],
        'Matematika (M)' => $avgTag['M'],
        'Algoritma (A)'  => $avgTag['A_algoritma'],
    ];
}

/**
 * The 4 IT career predictions computed from GCLWAMA averages.
 */
private function buildCareerPredictions(User $student): array
{
    $avgTag = $this->buildGclwamaBreakdown($student);

    $calc = function (string ...$keys) use ($avgTag): float {
        $values = [];
        foreach ($keys as $key) {
            $fullKey = match ($key) {
                'G'           => 'Gambar (G)',
                'C'           => 'Cerita (C)',
                'L'           => 'Layout (L)',
                'W'           => 'Warna (W)',
                'A_animasi'   => 'Animasi (A)',
                'M'           => 'Matematika (M)',
                'A_algoritma' => 'Algoritma (A)',
                default       => null,
            };
            if ($fullKey && isset($avgTag[$fullKey]) && $avgTag[$fullKey] > 0) {
                $values[] = $avgTag[$fullKey];
            }
        }
        return count($values) > 0 ? round(array_sum($values) / count($values), 1) : 0.0;
    };

    $predictions = [
        'Komik'       => $calc('G', 'W', 'A_animasi'),
        'DKV'         => $calc('L', 'W'),
        'Videografi'  => $calc('C', 'A_animasi'),
        'Programming' => $calc('M', 'A_algoritma'),
    ];
    arsort($predictions);

    return $predictions;
}

/**
 * Bahasa scores per language for one student.
 */
private function buildBahasaScores(User $student): array
{
    $bahasaScores = [];
    $examGroups   = $student->answers->groupBy(fn ($ans) => $ans->question?->exam_id);

    foreach ($examGroups as $examId => $answers) {
        $exam = $answers->first()?->question?->exam;
        if (! $exam) continue;

        $catText = strtolower(
            ($exam->category ?? '') . ' ' .
            ($exam->subcategory ?? '') . ' ' .
            ($exam->title ?? '')
        );

        $isBahasa = str_contains($catText, 'bahasa')
                 || str_contains($catText, 'arab')
                 || str_contains($catText, 'inggris')
                 || str_contains($catText, 'english');

        if (! $isBahasa) continue;

        $mcAnswers = $answers->filter(fn ($a) => $a->question->type === 'multiple_choice');
        $mcTotal   = $mcAnswers->count();
        $mcCorrect = $mcAnswers->where('score', '>=', 100)->count();
        $pct       = $mcTotal > 0 ? round(($mcCorrect / $mcTotal) * 100) : 0;

        $label = 'Bahasa';
        if (str_contains($catText, 'arab'))   $label = 'Bahasa Arab';
        if (str_contains($catText, 'inggris') || str_contains($catText, 'english')) $label = 'Bahasa Inggris';

        $bahasaScores[] = [
            'label'      => $label,
            'score'      => $pct,
            'exam_title' => $exam->title,
        ];
    }

    return collect($bahasaScores)
        ->groupBy('label')
        ->map(fn ($items) => [
            'label'      => $items->first()['label'],
            'score'      => round($items->avg('score'), 0),
            'exam_title' => $items->first()['exam_title'],
        ])
        ->values()
        ->all();
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