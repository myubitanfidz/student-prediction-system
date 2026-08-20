<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Mengambil daftar semua santri beserta status ujiannya
    public function getStudents()
    {
        $students = User::where('role', 'student')
            ->with(['portfolio', 'answers'])
            ->get()
            ->map(function ($student) {
                return [
                    'id'               => $student->id,
                    'name'             => $student->name,
                    'email'            => $student->email,
                    'total_answered'   => $student->answers->count(),
                    'total_score'      => $student->answers->sum('score'),
                    'has_portfolio'    => !is_null($student->portfolio),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $students,
        ]);
    }

    // Mengambil detail jawaban santri untuk dikoreksi admin/guru
    public function getStudentAnswers($userId)
    {
        $student = User::with(['portfolio', 'answers.question.exam'])->find($userId);

        if (!$student || $student->role !== 'student') {
            return response()->json(['message' => 'Santri tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'student'   => $student->only(['id', 'name', 'email']),
                'portfolio' => $student->portfolio,
                'answers'   => $student->answers->map(function ($ans) {
                    return [
                        'answer_id'     => $ans->id,
                        'exam_title'    => $ans->question->exam->title ?? '-',
                        'question_type' => $ans->question->type,
                        'question_text' => $ans->question->question_text,
                        'student_answer'=> $ans->answer_text,
                        'current_score' => $ans->score,
                    ];
                }),
            ],
        ]);
    }

    // Input/update nilai esai manual oleh admin
    public function gradeAnswer(Request $request)
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
}