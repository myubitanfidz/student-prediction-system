<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminExamController extends Controller
{
    // 1. Ambil semua ujian beserta total jumlah soalnya
    public function index()
    {
        $exams = Exam::withCount('questions')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $exams,
        ]);
    }

    // 2. Tambah Paket Ujian Baru
    public function storeExam(Request $request)
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

        $exam = Exam::create($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket ujian berhasil ditambahkan',
            'data'    => $exam,
        ], 201);
    }

    // 3. Update Paket Ujian
    public function updateExam(Request $request, $id)
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

        $exam->update($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket ujian berhasil diperbarui',
            'data'    => $exam,
        ]);
    }

    // 4. Hapus Paket Ujian (otomatis menghapus soal di dalamnya)
    public function destroyExam($id)
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

    // 5. Ambil semua soal dalam 1 ujian untuk Admin (PG & Esai lengkap dengan kunci jawaban)
    public function getQuestionsByExam($examId)
    {
        $exam = Exam::with('questions')->find($examId);

        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'exam'      => $exam,
                'questions' => $exam->questions,
            ],
        ]);
    }

    // 6. Tambah Soal Baru (PG atau Esai)
    public function storeQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'        => 'required|exists:exams,id',
            'type'           => 'required|in:multiple_choice,essay',
            'question_text'  => 'required|string',
            'options'        => 'nullable|array', // Diisi array string untuk PG, null untuk esai
            'correct_answer' => 'nullable|string', // Kunci jawaban untuk PG
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

    // 7. Update Soal
    public function updateQuestion(Request $request, $id)
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

    // 8. Hapus Satu Soal
    public function destroyQuestion($id)
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