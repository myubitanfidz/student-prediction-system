<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminExamController extends Controller
{
    public function index(): JsonResponse
    {
        $exams = Exam::withCount('questions')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $exams,
        ]);
    }

    public function bulkStartPeriod(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period_title' => 'required|string|max:255',
            'start_time'   => 'nullable|date',
            'end_time'     => 'nullable|date|after_or_equal:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $now = now();
        $startTime = $request->start_time ? \Carbon\Carbon::parse($request->start_time) : $now;
        $endTime = $request->end_time ? \Carbon\Carbon::parse($request->end_time) : null;

        // Update seluruh paket ujian yang memiliki period_title tersebut menjadi aktif dan mulai sekarang
        $affected = Exam::where('period_title', $request->period_title)
            ->update([
                'is_active'  => true,
                'start_time' => $startTime,
                'end_time'   => $endTime,
            ]);

        if ($affected === 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Tidak ditemukan paket ujian dengan periode {$request->period_title}",
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => "Seluruh ujian pada gelombang '{$request->period_title}' berhasil dimulai secara serentak!",
            'affected' => $affected,
        ]);
    }

    public function storeExam(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category'     => 'required|string|max:100',
            'subcategory'  => 'required|string|max:100',
            'title'        => 'required|string|max:255',
            'period_title' => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'is_active'    => 'nullable|boolean',
            'start_time'   => 'nullable|date',
            'end_time'     => 'nullable|date|after_or_equal:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exam = Exam::create([
            'category'     => $request->category,
            'subcategory'  => $request->subcategory,
            'title'        => $request->title,
            'period_title' => $request->period_title ?? 'PSB 2026/2027',
            'description'  => $request->description,
            'is_active'    => $request->boolean('is_active', true),
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket ujian berhasil ditambahkan',
            'data'    => $exam,
        ], 201);
    }

    public function updateExam(Request $request, $id): JsonResponse
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category'     => 'required|string|max:100',
            'subcategory'  => 'required|string|max:100',
            'title'        => 'required|string|max:255',
            'period_title' => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'is_active'    => 'nullable|boolean',
            'start_time'   => 'nullable|date',
            'end_time'     => 'nullable|date|after_or_equal:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exam->update([
            'category'     => $request->category,
            'subcategory'  => $request->subcategory,
            'title'        => $request->title,
            'period_title' => $request->period_title ?? 'PSB 2026/2027',
            'description'  => $request->description,
            'is_active'    => $request->boolean('is_active', true),
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket ujian berhasil diperbarui',
            'data'    => $exam,
        ]);
    }

    public function destroyExam($id): JsonResponse
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        $exam->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket ujian berhasil dihapus',
        ]);
    }

    public function getQuestionsByExam($examId)
    {
        $exam = Exam::with('questions')->find($examId);
    
        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }
    
        $exam->questions->makeVisible('correct_answer');
    
        return response()->json([
            'status' => 'success',
            'data'   => [
                'exam'      => $exam,
                'questions' => $exam->questions,
            ],
        ]);
    }

    public function storeQuestion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'exam_id'            => 'required|exists:exams,id',
            'type'               => 'required|in:multiple_choice,essay,image_upload',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
            'gclwama_tag'        => 'nullable|in:G,C,L,W,A_animasi,M,A_algoritma',
            'question_text'      => 'required|string',
            'options'            => 'nullable|array',
            'correct_answer'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = Question::create([
            'exam_id'            => $request->exam_id,
            'type'               => $request->type,
            'time_limit_seconds' => $request->time_limit_seconds ?? 60,
            'gclwama_tag'        => $request->gclwama_tag,
            'question_text'      => $request->question_text,
            'options'            => $request->type === 'multiple_choice' ? $request->options : null,
            'correct_answer'     => $request->type === 'multiple_choice' ? $request->correct_answer : null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Soal berhasil ditambahkan',
            'data'    => $question,
        ], 201);
    }

    public function updateQuestion(Request $request, $id): JsonResponse
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Soal tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'type'               => 'required|in:multiple_choice,essay,image_upload',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
            'gclwama_tag'        => 'nullable|in:G,C,L,W,A_animasi,M,A_algoritma',
            'question_text'      => 'required|string',
            'options'            => 'nullable|array',
            'correct_answer'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question->update([
            'type'               => $request->type,
            'time_limit_seconds' => $request->time_limit_seconds ?? 60,
            'gclwama_tag'        => $request->gclwama_tag,
            'question_text'      => $request->question_text,
            'options'            => $request->type === 'multiple_choice' ? $request->options : null,
            'correct_answer'     => $request->type === 'multiple_choice' ? $request->correct_answer : null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Soal berhasil diperbarui',
            'data'    => $question,
        ]);
    }

    public function destroyQuestion($id): JsonResponse
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Soal tidak ditemukan'], 404);
        }

        $question->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Soal berhasil dihapus',
        ]);
    }
}