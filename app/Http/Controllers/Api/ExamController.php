<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamCompletion;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $completions = ExamCompletion::where('user_id', $userId)
            ->get()
            ->keyBy('exam_id');

        $exams = Exam::all()
            ->map(function (Exam $exam) use ($completions) {
                $completion = $completions->get($exam->id);

                return [
                    'id'             => $exam->id,
                    'category'       => $exam->category,
                    'subcategory'    => $exam->subcategory,
                    'title'          => $exam->title,
                    'description'    => $exam->description,
                    'completed'      => (bool) $completion,
                    'retake_allowed' => (bool) ($completion?->retake_allowed),
                ];
            })
            ->groupBy('category');

        return response()->json([
            'status' => 'success',
            'data'   => $exams,
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $exam = Exam::with('questions')->find($id);

        if (! $exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        $user = $request->user();
        $completion = ExamCompletion::where('user_id', $user->id)
            ->where('exam_id', $id)
            ->first();

        $examPayload = $exam->only(['id', 'category', 'subcategory', 'title', 'description']);

        // Finished & locked — show results only
        if ($completion && ! $completion->retake_allowed) {
            $answers = StudentAnswer::where('user_id', $user->id)
                ->whereIn('question_id', $exam->questions->pluck('id'))
                ->get()
                ->keyBy('question_id');

            $results = $exam->questions->map(function (Question $question) use ($answers) {
                $answer = $answers->get($question->id);
                $isCorrect = null;

                if ($question->type === 'multiple_choice') {
                    $isCorrect = $answer !== null && (float) $answer->score >= 100;
                }

                return [
                    'id'              => $question->id,
                    'type'            => $question->type,
                    'question_text'   => $question->question_text,
                    'options'         => $question->options,
                    'student_answer'  => $answer?->answer_text,
                    'score'           => $answer?->score,
                    'is_correct'      => $isCorrect,
                    'correct_answer'  => $question->type === 'multiple_choice' ? $question->correct_answer : null,
                ];
            })->values();

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'exam'           => $examPayload,
                    'completed'      => true,
                    'retake_allowed' => false,
                    'questions'      => [],
                    'results'        => $results,
                ],
            ]);
        }

        // Active attempt — shuffle questions (and MC options) each open
        $questions = $exam->questions->shuffle()->values()->map(function (Question $question) {
            $options = $question->options;
            if ($question->type === 'multiple_choice' && is_array($options)) {
                $options = collect($options)->shuffle()->values()->all();
            }

            return [
                'id'            => $question->id,
                'type'          => $question->type,
                'question_text' => $question->question_text,
                'options'       => $options,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'exam'           => $examPayload,
                'completed'      => false,
                'retake_allowed' => (bool) ($completion?->retake_allowed),
                'questions'      => $questions,
                'results'        => [],
            ],
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'exam_id'               => 'required|exists:exams,id',
            'answers'               => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->user()->id;
        $examId = (int) $request->exam_id;

        $completion = ExamCompletion::where('user_id', $userId)
            ->where('exam_id', $examId)
            ->first();

        if ($completion && ! $completion->retake_allowed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah menyelesaikan ujian ini. Minta izin ulang ke guru/admin untuk mengerjakan lagi.',
            ], 403);
        }

        $questionIds = collect($request->answers)->pluck('question_id');
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        DB::transaction(function () use ($request, $userId, $examId, $questions, $completion) {
            if ($completion && $completion->retake_allowed) {
                $examQuestionIds = Question::where('exam_id', $examId)->pluck('id');
                StudentAnswer::where('user_id', $userId)
                    ->whereIn('question_id', $examQuestionIds)
                    ->delete();
            }

            foreach ($request->answers as $ans) {
                $question = $questions->get($ans['question_id']);
                $score = null;

                if ($question?->type === 'multiple_choice') {
                    $score = mb_strtolower(trim($ans['answer_text'])) === mb_strtolower(trim((string) $question->correct_answer))
                        ? 100
                        : 0;
                }

                StudentAnswer::updateOrCreate(
                    [
                        'user_id'     => $userId,
                        'question_id' => $ans['question_id'],
                    ],
                    [
                        'answer_text' => $ans['answer_text'],
                        'score'       => $score,
                    ]
                );
            }

            ExamCompletion::updateOrCreate(
                ['user_id' => $userId, 'exam_id' => $examId],
                [
                    'completed_at'   => now(),
                    'retake_allowed' => false,
                ]
            );
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Jawaban ujian berhasil dikirim',
        ]);
    }
}
