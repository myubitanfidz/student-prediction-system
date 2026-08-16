<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    // Mengambil semua ujian dikelompokkan berdasarkan kategori
    public function index()
    {
        $exams = Exam::all()->groupBy('category');

        return response()->json([
            'status' => 'success',
            'data'   => $exams,
        ]);
    }

    // Mengambil detail ujian beserta soal PG dan Esai
    public function show($id)
    {
        $exam = Exam::with('questions')->find($id);

        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'exam'      => $exam->only(['id', 'category', 'subcategory', 'title', 'description']),
                'questions' => $exam->questions->map(function ($q) {
                    return [
                        'id'            => $q->id,
                        'type'          => $q->type,
                        'question_text' => $q->question_text,
                        'options'       => $q->options, // null jika esai
                    ];
                }),
            ],
        ]);
    }

    // Submit jawaban ujian santri
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'             => 'required|exists:users,id',
            'answers'             => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $questions = Question::whereIn('id', collect($request->answers)->pluck('question_id'))
            ->get()
            ->keyBy('id');

        foreach ($request->answers as $ans) {
            $question = $questions->get($ans['question_id']);
            $score = null;

            // Multiple-choice answers can be marked immediately. Essays keep a
            // null score so a teacher can assess them later.
            if ($question?->type === 'multiple_choice') {
                $score = mb_strtolower(trim($ans['answer_text'])) === mb_strtolower(trim((string) $question->correct_answer))
                    ? 100
                    : 0;
            }

            StudentAnswer::updateOrCreate(
                [
                    'user_id'     => $request->user_id,
                    'question_id' => $ans['question_id'],
                ],
                [
                    'answer_text' => $ans['answer_text'],
                    'score'       => $score,
                ]
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Jawaban ujian berhasil dikirim',
        ]);
    }
    // Tambahkan di dalam ExamController.php

    // Endpoint untuk guru menginput/mengupdate nilai jawaban siswa
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
