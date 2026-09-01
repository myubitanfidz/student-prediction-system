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
use Illuminate\Support\Facades\Storage;
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
                    'id'               => $exam->id,
                    'category'         => $exam->category,
                    'subcategory'      => $exam->subcategory,
                    'title'            => $exam->title,
                    'period_title'     => $exam->period_title,
                    'description'      => $exam->description,
                    'is_active'        => (bool) $exam->is_active,
                    'start_time'       => $exam->start_time,
                    'end_time'         => $exam->end_time,
                    'completed'        => (bool) $completion,
                    'retake_allowed'   => (bool) ($completion?->retake_allowed),
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

        // 1. Validasi Jadwal Periode PSB & Status Aktif (Khusus Santri)
        if ($user && $user->role === 'student') {
            if (! $exam->is_active) {
                return response()->json([
                    'status'       => 'error',
                    'period_title' => $exam->period_title,
                    'message'      => 'Ujian ini sedang dinonaktifkan oleh administrator.',
                ], 403);
            }

            $now = now();
            if ($exam->start_time) {
                $start = \Carbon\Carbon::parse($exam->start_time);
                if ($now->lt($start)) {
                    return response()->json([
                        'status'       => 'error',
                        'period_title' => $exam->period_title,
                        'message'      => "Ujian ({$exam->period_title}) belum dibuka. Waktu mulai: " . $start->format('d M Y H:i'),
                    ], 403);
                }
            }

            if ($exam->end_time) {
                $end = \Carbon\Carbon::parse($exam->end_time);
                if ($now->gt($end)) {
                    return response()->json([
                        'status'       => 'error',
                        'period_title' => $exam->period_title,
                        'message'      => "Periode pengerjaan ({$exam->period_title}) telah berakhir pada: " . $end->format('d M Y H:i'),
                    ], 403);
                }
            }
        }

        $completion = ExamCompletion::where('user_id', $user->id)
            ->where('exam_id', $id)
            ->first();

        $examPayload = [
            'id'               => $exam->id,
            'category'         => $exam->category,
            'subcategory'      => $exam->subcategory,
            'title'            => $exam->title,
            'period_title'     => $exam->period_title,
            'description'      => $exam->description,
            'duration_minutes' => $exam->duration_minutes,
        ];

        // 2. Mode Hasil Selesai (Finished & Locked)
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
                    'id'                 => $question->id,
                    'gclwama_tag'        => $question->gclwama_tag,
                    'type'               => $question->type,
                    'question_text'      => $question->question_text,
                    'options'            => $question->options,
                    'student_answer'     => $answer?->answer_text,
                    'file_url'           => $answer?->file_path ? asset('storage/' . $answer->file_path) : null,
                    'score'              => $answer?->score,
                    'is_correct'         => $isCorrect,
                    'correct_answer'     => $question->type === 'multiple_choice' ? $question->correct_answer : null,
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

        // 3. Mode Pengerjaan Aktif (Mengacak nomor & opsi PG, memastikan array options valid)
        $questions = $exam->questions->shuffle()->values()->map(function (Question $question) {
            $options = $question->options;
            if ($question->type === 'multiple_choice' && is_array($options)) {
                $options = collect($options)->shuffle()->values()->all();
            }

            return [
                'id'                 => $question->id,
                'gclwama_tag'        => $question->gclwama_tag,
                'type'               => $question->type,
                'time_limit_seconds' => (int) ($question->time_limit_seconds ?: 60),
                'question_text'      => $question->question_text,
                'options'            => is_array($options) ? $options : [],
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
            'answers.*.answer_text' => 'nullable|string',
            'answers.*.file'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
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
            // Jika izin ulang aktif, bersihkan jawaban lama
            if ($completion && $completion->retake_allowed) {
                $examQuestionIds = Question::where('exam_id', $examId)->pluck('id');
                $oldAnswers = StudentAnswer::where('user_id', $userId)
                    ->whereIn('question_id', $examQuestionIds)
                    ->get();

                foreach ($oldAnswers as $old) {
                    if ($old->file_path) {
                        Storage::disk('public')->delete($old->file_path);
                    }
                }

                StudentAnswer::where('user_id', $userId)
                    ->whereIn('question_id', $examQuestionIds)
                    ->delete();
            }

            foreach ($request->answers as $index => $ans) {
                $question = $questions->get($ans['question_id']);
                $score = null;
                $filePath = null;

                // Auto-grading untuk Pilihan Ganda (PG)
                if ($question?->type === 'multiple_choice') {
                    $score = mb_strtolower(trim((string) ($ans['answer_text'] ?? ''))) === mb_strtolower(trim((string) $question->correct_answer))
                        ? 100
                        : 0;
                }

                // Simpan berkas jika ada file upload (gambar G)
                if ($request->hasFile("answers.{$index}.file")) {
                    $uploadedFile = $request->file("answers.{$index}.file");
                    $filePath = $uploadedFile->store('exam_answers', 'public');
                }

                StudentAnswer::updateOrCreate(
                    [
                        'user_id'     => $userId,
                        'question_id' => $ans['question_id'],
                    ],
                    [
                        'answer_text' => $ans['answer_text'] ?? ($filePath ? '[Uploaded File]' : ''),
                        'file_path'   => $filePath,
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