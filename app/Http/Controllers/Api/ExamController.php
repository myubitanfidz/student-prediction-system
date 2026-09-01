<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SecureId;
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
                    'id'               => $exam->hash_id, 
                    'raw_id'           => $exam->id,
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
        $realId = SecureId::decode($id, 'exam');
        if (!$realId) {
            return response()->json(['status' => 'error', 'message' => 'Tautan ujian tidak valid atau telah kedaluwarsa.'], 404);
        }

        $exam = Exam::with('questions')->find($realId);

        if (! $exam) {
            return response()->json(['status' => 'error', 'message' => 'Paket ujian tidak ditemukan di database.'], 404);
        }

        $user = $request->user();

        // Validasi Waktu
        if ($user && $user->role === 'student') {
            if (! (bool) $exam->is_active) {
                return response()->json([
                    'status'       => 'error',
                    'period_title' => $exam->period_title,
                    'message'      => 'Ujian ini sedang dinonaktifkan oleh administrator/guru.',
                ], 403);
            }

            $now = now()->timezone('Asia/Jakarta');

            if ($exam->start_time) {
                $start = \Carbon\Carbon::parse($exam->start_time)->timezone('Asia/Jakarta');
                if ($now->lessThan($start)) {
                    return response()->json([
                        'status'       => 'error',
                        'period_title' => $exam->period_title,
                        'message'      => "Ujian belum dimulai. Jadwal buka: " . $start->format('d M Y, H:i') . " WIB.",
                    ], 403);
                }
            }

            if ($exam->end_time) {
                $end = \Carbon\Carbon::parse($exam->end_time)->timezone('Asia/Jakarta');
                if ($now->greaterThan($end)) {
                    return response()->json([
                        'status'       => 'error',
                        'period_title' => $exam->period_title,
                        'message'      => "Periode ujian telah berakhir pada: " . $end->format('d M Y, H:i') . " WIB.",
                    ], 403);
                }
            }
        }

        $completion = $user ? ExamCompletion::where('user_id', $user->id)
            ->where('exam_id', $realId)
            ->first() : null;

        $examPayload = [
            'id'               => $exam->hash_id,
            'category'         => $exam->category,
            'subcategory'      => $exam->subcategory,
            'title'            => $exam->title,
            'period_title'     => $exam->period_title,
            'description'      => $exam->description,
            'duration_minutes' => $exam->duration_minutes,
        ];

        // 🌟 Jika Ujian Selesai, Hapus Total Kunci Jawaban dari Response
        if ($completion && ! (bool) $completion->retake_allowed) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'exam'           => $examPayload,
                    'completed'      => true,
                    'retake_allowed' => false,
                    'questions'      => [],
                    'results'        => [], // Kosongkan agar tidak ada celah kebocoran
                ],
            ]);
        }

        // 🌟 Option Tokenization & Obfuscation
        $appKey = config('app.key') ?: 'ts-secret-salt';
        $questions = $exam->questions->shuffle()->values()->map(function (Question $question) use ($appKey) {
            $options = $question->options;
            $hashedOptions = [];

            if ($question->type === 'multiple_choice' && is_array($options)) {
                $options = collect($options)->shuffle()->values()->all();
                foreach ($options as $opt) {
                    // Buat Token Acak berdasarkan kombinasi ID Soal + Teks Opsi
                    $token = substr(hash_hmac('sha256', $question->id . '#' . $opt, $appKey), 0, 16);
                    $hashedOptions[] = [
                        'token' => $token,
                        'text'  => (string) $opt,
                    ];
                }
            }

            return [
                'id'                 => $question->hash_id, // Gunakan Hash ID
                'type'               => $question->type,
                'time_limit_seconds' => (int) ($question->time_limit_seconds ?: 60),
                'question_text'      => $question->question_text,
                'options'            => $hashedOptions,
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
        $rawExamId = $request->exam_id;
        $decodedExamId = is_numeric($rawExamId) ? (int)$rawExamId : SecureId::decode($rawExamId, 'exam');
        
        // 🌟 Dekode array Jawaban (Question Hash ID)
        $answers = $request->input('answers', []);
        $resolvedAnswers = [];
        foreach ($answers as $index => $ans) {
            $qRaw = $ans['question_id'] ?? null;
            $qId = SecureId::decode($qRaw, 'question') ?: $qRaw;
            $resolvedAnswers[$index] = $ans;
            $resolvedAnswers[$index]['question_id'] = $qId;
        }
        
        $request->merge([
            'exam_id_resolved' => $decodedExamId,
            'answers'          => $resolvedAnswers
        ]);

        $validator = Validator::make($request->all(), [
            'exam_id_resolved'      => 'required|exists:exams,id',
            'answers'               => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_text' => 'nullable|string',
            'answers.*.file'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->user()->id;
        $examId = (int) $decodedExamId;

        $completion = ExamCompletion::where('user_id', $userId)->where('exam_id', $examId)->first();

        if ($completion && ! $completion->retake_allowed) {
            return response()->json(['status' => 'error', 'message' => 'Ujian sudah diselesaikan.'], 403);
        }

        $questionIds = collect($request->answers)->pluck('question_id');
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
        $appKey = config('app.key') ?: 'ts-secret-salt';

        DB::transaction(function () use ($request, $userId, $examId, $questions, $completion, $appKey) {
            if ($completion && $completion->retake_allowed) {
                $examQuestionIds = Question::where('exam_id', $examId)->pluck('id');
                $oldAnswers = StudentAnswer::where('user_id', $userId)->whereIn('question_id', $examQuestionIds)->get();
                foreach ($oldAnswers as $old) {
                    if ($old->file_path && Storage::disk('public')->exists($old->file_path)) {
                        Storage::disk('public')->delete($old->file_path);
                    }
                }
                StudentAnswer::where('user_id', $userId)->whereIn('question_id', $examQuestionIds)->delete();
            }

            foreach ($request->answers as $index => $ans) {
                $questionId = (int) $ans['question_id'];
                $question = $questions->get($questionId);
                $score = null;
                $filePath = null;
                $answerTextToSave = $ans['answer_text'] ?? '';

                // 🌟 Server-Side Verification Hash
                if ($question?->type === 'multiple_choice') {
                    $submittedToken = trim((string) $answerTextToSave);
                    $correctToken = substr(hash_hmac('sha256', $question->id . '#' . $question->correct_answer, $appKey), 0, 16);
                    
                    $score = hash_equals($correctToken, $submittedToken) ? 100 : 0;
                    
                    // Kembalikan token menjadi teks asli untuk dibaca admin di dashboard koreksi
                    $actualText = '';
                    if (is_array($question->options)) {
                        foreach ($question->options as $opt) {
                            $tok = substr(hash_hmac('sha256', $question->id . '#' . $opt, $appKey), 0, 16);
                            if (hash_equals($tok, $submittedToken)) {
                                $actualText = $opt;
                                break;
                            }
                        }
                    }
                    $answerTextToSave = $actualText ?: $submittedToken;
                }

                if ($request->hasFile("answers.{$index}.file")) {
                    $uploadedFile = $request->file("answers.{$index}.file");
                    $filePath = $uploadedFile->store('exam_answers', 'public');
                    $answerTextToSave = '[Uploaded File]';
                }

                StudentAnswer::updateOrCreate(
                    ['user_id' => $userId, 'question_id' => $questionId],
                    ['answer_text' => $answerTextToSave, 'file_path' => $filePath, 'score' => $score]
                );
            }

            ExamCompletion::updateOrCreate(
                ['user_id' => $userId, 'exam_id' => $examId],
                ['completed_at' => now(), 'retake_allowed' => false]
            );
        });

        return response()->json(['status' => 'success', 'message' => 'Jawaban disimpan']);
    }
}