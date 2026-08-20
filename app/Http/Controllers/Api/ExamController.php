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
        $completedExamIds = ExamCompletion::where('user_id', $userId)
            ->pluck('exam_id')
            ->toArray();

        $exams = Exam::all()
            ->map(function (Exam $exam) use ($completedExamIds) {
                return [
                    'id'          => $exam->id,
                    'category'    => $exam->category,
                    'subcategory' => $exam->subcategory,
                    'title'       => $exam->title,
                    'description' => $exam->description,
                    'completed'   => in_array($exam->id, $completedExamIds, true),
                ];
            })
            ->groupBy('category');

        return response()->json([
            'status' => 'success',
            'data'   => $exams,
        ]);
    }

    public function show($id): JsonResponse
    {
        $exam = Exam::with('questions')->find($id);

        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'exam'      => $exam->only(['id', 'category', 'subcategory', 'title', 'description']),
                'questions' => $exam->questions->map(fn ($q) => [
                    'id'            => $q->id,
                    'type'          => $q->type,
                    'question_text' => $q->question_text,
                    'options'       => $q->options,
                ]),
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
        $questionIds = collect($request->answers)->pluck('question_id');
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        DB::transaction(function () use ($request, $userId, $questions) {
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
                ['user_id' => $userId, 'exam_id' => $request->exam_id],
                ['completed_at' => now()]
            );
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Jawaban ujian berhasil dikirim',
        ]);
    }
}