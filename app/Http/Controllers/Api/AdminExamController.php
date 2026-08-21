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

    public function storeExam(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category'    => 'required|string|max:100',
            'subcategory' => 'required|string|max:100',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exam = Exam::create($request->only(['category', 'subcategory', 'title', 'description']));

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
            'category'    => 'required|string|max:100',
            'subcategory' => 'required|string|max:100',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exam->update($request->only(['category', 'subcategory', 'title', 'description']));

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
    
        // Tampilkan kunci jawaban hanya untuk admin
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
            'exam_id'        => 'required|exists:exams,id',
            'type'           => 'required|in:multiple_choice,essay',
            'question_text'  => 'required|string',
            'options'        => 'nullable|array',
            'correct_answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = Question::create([
            'exam_id'        => $request->exam_id,
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'options'        => $request->type === 'multiple_choice' ? $request->options : null,
            'correct_answer' => $request->type === 'multiple_choice' ? $request->correct_answer : null,
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
            'type'           => 'required|in:multiple_choice,essay',
            'question_text'  => 'required|string',
            'options'        => 'nullable|array',
            'correct_answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question->update([
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'options'        => $request->type === 'multiple_choice' ? $request->options : null,
            'correct_answer' => $request->type === 'multiple_choice' ? $request->correct_answer : null,
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