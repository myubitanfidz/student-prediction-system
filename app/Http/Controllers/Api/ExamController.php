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
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $completions = ExamCompletion::where('user_id', $userId)->get()->keyBy('exam_id');

        // 🌟 Prioritaskan paket ujian yang is_featured (terhubung)
        $exams = Exam::orderBy('is_featured', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function (Exam $exam) use ($completions) {
                $completion = $completions->get($exam->id);

                return [
                    'id'               => $exam->hash_id, 
                    'raw_id'           => $exam->id,
                    'category'         => $exam->category,
                    'subcategory'      => $exam->subcategory,
                    'home_slot'        => $exam->home_slot, // 🌟 'it_gclwama', 'bahasa_inggris', 'bahasa_arab'
                    'title'            => $exam->title,
                    'period_title'     => $exam->period_title,
                    'description'      => $exam->description,
                    'is_active'        => (bool) $exam->is_active,
                    'is_featured'      => (bool) $exam->is_featured,
                    'start_time'       => $exam->start_time,
                    'end_time'         => $exam->end_time,
                    'completed'        => (bool) $completion?->completed_at,
                    'retake_allowed'   => (bool) ($completion?->retake_allowed),
                ];
            })
            ->groupBy('category');

        return response()->json(['status' => 'success', 'data' => $exams]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $realId = SecureId::decode($id, 'exam');
        if (!$realId) return response()->json(['status' => 'error', 'message' => 'Tautan ujian tidak valid.'], 404);

        $exam = Exam::with('questions')->find($realId);
        if (! $exam) return response()->json(['status' => 'error', 'message' => 'Paket ujian tidak ditemukan.'], 404);

        $user = $request->user();

        // 1. Validasi Jadwal
        if ($user && $user->role === 'student') {
            if (! (bool) $exam->is_active) {
                return response()->json(['status' => 'error', 'message' => 'Ujian sedang dinonaktifkan.'], 403);
            }
            
            $now = now()->timezone('Asia/Jakarta');
            
            if ($exam->start_time && $now->lessThan(\Carbon\Carbon::parse($exam->start_time)->timezone('Asia/Jakarta'))) {
                return response()->json(['status' => 'error', 'message' => "Ujian belum dibuka."], 403);
            }
            if ($exam->end_time && $now->greaterThan(\Carbon\Carbon::parse($exam->end_time)->timezone('Asia/Jakarta'))) {
                return response()->json(['status' => 'error', 'message' => "Ujian telah ditutup."], 403);
            }
        }

        // 2. Anti-Cheat Session Tracking
        $completion = null;
        if ($user && $user->role === 'student') {
            $completion = ExamCompletion::firstOrCreate(
                ['user_id' => $user->id, 'exam_id' => $realId],
                [
                    'started_at'     => now(),
                    'session_nonce'  => Str::random(40),
                    'completed_at'   => null,
                    'retake_allowed' => false
                ]
            );

            if ($completion->retake_allowed && $completion->completed_at) {
                $completion->update([
                    'started_at'     => now(),
                    'completed_at'   => null,
                    'session_nonce'  => Str::random(40)
                ]);
            }
        }

        $examPayload = [
            'id'               => $exam->hash_id,
            'category'         => $exam->category,
            'subcategory'      => $exam->subcategory,
            'title'            => $exam->title,
            'period_title'     => $exam->period_title,
            'session_nonce'    => $completion ? $completion->session_nonce : null,
        ];

        // 3. Mode Selesai
        if ($completion && $completion->completed_at && ! $completion->retake_allowed) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'exam'           => $examPayload,
                    'completed'      => true,
                    'retake_allowed' => false,
                    'questions'      => [],
                    'results'        => [],
                ],
            ]);
        }

        // 4. Mode Pengerjaan dengan Tokenisasi
        $appKey = config('app.key') ?: 'ts-secret-salt';
        $questions = $exam->questions->shuffle()->values()->map(function (Question $question) use ($appKey) {
            $options = $question->options;
            $hashedOptions = [];

            if ($question->type === 'multiple_choice' && is_array($options)) {
                $options = collect($options)->shuffle()->values()->all();
                foreach ($options as $opt) {
                    $token = substr(hash_hmac('sha256', $question->id . '#' . $opt, $appKey), 0, 16);
                    $hashedOptions[] = ['token' => $token, 'text' => (string) $opt];
                }
            }

            return [
                'id'                 => $question->hash_id,
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
            'session_nonce'         => 'required|string', 
            'violation_count'       => 'nullable|integer|min:0',
            'answers'               => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_text' => 'nullable|string',
            'answers.*.file'        => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'mimetypes:image/jpeg,image/png,image/webp,application/pdf',
                'max:5120',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->user()->id;
        $examId = (int) $decodedExamId;

        $completion = ExamCompletion::where('user_id', $userId)->where('exam_id', $examId)->first();

        // 🛡️ LAYER 1: Replay Attack Validation
        if (!$completion || $completion->session_nonce !== $request->session_nonce) {
            return response()->json(['status' => 'error', 'message' => 'Sesi ujian tidak valid atau telah kedaluwarsa.'], 403);
        }

        if ($completion->completed_at && !$completion->retake_allowed) {
            return response()->json(['status' => 'error', 'message' => 'Ujian sudah diselesaikan.'], 403);
        }

        // 🛡️ LAYER 2: Server-Side Time Validation
        $totalTimeLimit = Question::where('exam_id', $examId)->sum('time_limit_seconds');
        $maxAllowedTime = $totalTimeLimit + 600;
        
        if ($completion->started_at && now()->diffInSeconds($completion->started_at) > $maxAllowedTime) {
            return response()->json(['status' => 'error', 'message' => 'Batas waktu pengerjaan telah habis.'], 403);
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

                if ($question?->type === 'multiple_choice') {
                    $submittedToken = trim((string) $answerTextToSave);
                    $correctToken = substr(hash_hmac('sha256', $question->id . '#' . $question->correct_answer, $appKey), 0, 16);
                    
                    $score = hash_equals($correctToken, $submittedToken) ? 100 : 0;
                    
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

                    if ($uploadedFile->isValid()) {
                        $extension = strtolower($uploadedFile->getClientOriginalExtension());
                        $safeFileName = Str::random(40) . '.' . $extension;
                        $filePath = $uploadedFile->storeAs('exam_answers', $safeFileName, 'public');
                        $answerTextToSave = '[Uploaded File]';
                    }
                }

                StudentAnswer::updateOrCreate(
                    ['user_id' => $userId, 'question_id' => $questionId],
                    ['answer_text' => $answerTextToSave, 'file_path' => $filePath, 'score' => $score]
                );
            }

            $completion->update([
                'completed_at'    => now(),
                'violation_count' => (int) $request->input('violation_count', 0),
                'retake_allowed'  => false,
                'session_nonce'   => null, 
            ]);
        });

        return response()->json(['status' => 'success', 'message' => 'Jawaban disimpan.']);
    }
}