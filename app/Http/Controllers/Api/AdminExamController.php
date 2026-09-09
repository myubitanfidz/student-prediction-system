<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SecureId;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminExamController extends Controller
{
    public function index(): JsonResponse
    {
        $exams = Exam::withCount('questions')
            ->orderBy('is_featured', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($exam) {
                $exam->hash_id = $exam->hash_id;
                return $exam;
            });

        return response()->json([
            'status' => 'success',
            'data'   => $exams,
        ]);
    }

    public function bulkStartPeriod(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period_title'   => 'required|string|max:255',
            'start_time'     => 'nullable|date',
            'end_time'       => 'nullable|date|after_or_equal:start_time',
            'exam_ids'       => 'nullable|array',
            'exam_ids.*'     => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $now = now();
        $startTime = $request->start_time ? \Carbon\Carbon::parse($request->start_time) : $now;
        $endTime = $request->end_time ? \Carbon\Carbon::parse($request->end_time) : null;

        $query = Exam::query();

        if ($request->filled('exam_ids') && count($request->exam_ids) > 0) {
            $resolvedIds = collect($request->exam_ids)->map(function ($id) {
                return is_numeric($id) ? (int)$id : SecureId::decode($id, 'exam');
            })->filter()->values()->all();

            $query->whereIn('id', $resolvedIds);
        } else {
            $query->where('period_title', $request->period_title);
        }

        $affected = $query->update([
            'is_active'    => true,
            'period_title' => $request->period_title,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
        ]);

        if ($affected === 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Tidak ada paket ujian yang diperbarui. Pastikan paket sudah dipilih.",
            ], 404);
        }

        return response()->json([
            'status'   => 'success',
            'message'  => "Sebanyak {$affected} paket ujian berhasil dimulai serentak untuk '{$request->period_title}'!",
            'affected' => $affected,
        ]);
    }

    public function storeExam(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'home_slot'    => 'nullable|in:it_gclwama,bahasa_inggris,bahasa_arab',
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

        [$category, $subcategory] = match ($request->home_slot) {
            'it_gclwama'     => ['IT', 'GCLWAMA'],
            'bahasa_inggris' => ['Bahasa', 'Inggris'],
            'bahasa_arab'    => ['Bahasa', 'Arab'],
            default          => [$request->input('category', 'Umum'), $request->input('subcategory', 'Umum')],
        };

        $homeSlot = $request->home_slot ?: null;

        if ($homeSlot) {
            Exam::where('home_slot', $homeSlot)->update([
                'home_slot'   => null,
                'is_featured' => false
            ]);
        }

        $exam = Exam::create([
            'category'         => $category,
            'subcategory'      => $subcategory,
            'home_slot'        => $homeSlot,
            'is_featured'      => (bool) $homeSlot,
            'title'            => $request->title,
            'period_title'     => $request->period_title ?? 'PSB 2026/2027',
            'description'      => $request->description,
            'duration_minutes' => 60,
            'is_active'        => $request->boolean('is_active', true),
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket ujian berhasil disimpan!',
            'data'    => $exam,
        ], 201);
    }

    public function updateExam(Request $request, $id): JsonResponse
    {
        $realId = is_numeric($id) ? (int)$id : SecureId::decode($id, 'exam');
        $exam = Exam::find($realId);

        if (!$exam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'home_slot'    => 'nullable|in:it_gclwama,bahasa_inggris,bahasa_arab',
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

        $targetSlot = $request->home_slot ?: null;
        $previousSlot = $exam->home_slot;

        [$category, $subcategory] = match ($targetSlot) {
            'it_gclwama'     => ['IT', 'GCLWAMA'],
            'bahasa_inggris' => ['Bahasa', 'Inggris'],
            'bahasa_arab'    => ['Bahasa', 'Arab'],
            default          => [$request->input('category', $exam->category), $request->input('subcategory', $exam->subcategory)],
        };

        DB::transaction(function () use ($exam, $targetSlot, $previousSlot, $category, $subcategory, $request) {
            if ($targetSlot) {
                $incumbent = Exam::where('home_slot', $targetSlot)
                    ->where('id', '!=', $exam->id)
                    ->first();

                if ($incumbent) {
                    $incumbent->update([
                        'home_slot'   => $previousSlot ?: null,
                        'is_featured' => (bool) $previousSlot,
                    ]);
                }
            }

            $exam->update([
                'category'     => $category,
                'subcategory'  => $subcategory,
                'home_slot'    => $targetSlot,
                'is_featured'  => (bool) $targetSlot,
                'title'        => $request->title,
                'period_title' => $request->period_title ?? 'PSB 2026/2027',
                'description'  => $request->description,
                'is_active'    => $request->boolean('is_active', true),
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Paket berhasil dialokasikan ke slot tanpa tabrakan!',
            'data'    => $exam->fresh(),
        ]);
    }

    public function destroyExam($id): JsonResponse
    {
        $realId = is_numeric($id) ? (int)$id : SecureId::decode($id, 'exam');
        $exam = Exam::find($realId);

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
        $realId = is_numeric($examId) ? (int)$examId : SecureId::decode($examId, 'exam');
        $exam = Exam::with('questions')->find($realId);
    
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
        $rawExamId = $request->exam_id;
        $realExamId = is_numeric($rawExamId) ? (int)$rawExamId : SecureId::decode($rawExamId, 'exam');

        $request->merge(['exam_id_resolved' => $realExamId]);

        $validator = Validator::make($request->all(), [
            'exam_id_resolved'   => 'required|exists:exams,id',
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

        // Simpan correct_answer baik untuk PG maupun referensi AI esai
        $correctAnswer = in_array($request->type, ['multiple_choice', 'essay']) 
            ? $request->correct_answer 
            : null;

        $question = Question::create([
            'exam_id'            => $realExamId,
            'type'               => $request->type,
            'time_limit_seconds' => $request->time_limit_seconds ?? 60,
            'gclwama_tag'        => $request->gclwama_tag,
            'question_text'      => $request->question_text,
            'options'            => $request->type === 'multiple_choice' ? $request->options : null,
            'correct_answer'     => $correctAnswer,
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

        // Izinkan correct_answer diperbarui untuk PG maupun esai
        $correctAnswer = in_array($request->type, ['multiple_choice', 'essay']) 
            ? $request->correct_answer 
            : null;

        $question->update([
            'type'               => $request->type,
            'time_limit_seconds' => $request->time_limit_seconds ?? 60,
            'gclwama_tag'        => $request->gclwama_tag,
            'question_text'      => $request->question_text,
            'options'            => $request->type === 'multiple_choice' ? $request->options : null,
            'correct_answer'     => $correctAnswer,
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

    public function toggleFeatured(Request $request, $id): JsonResponse
    {
        $realId = is_numeric($id) ? (int)$id : SecureId::decode($id, 'exam');
        $targetExam = Exam::find($realId);

        if (!$targetExam) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        Exam::where('category', $targetExam->category)
            ->where('subcategory', $targetExam->subcategory)
            ->update(['is_featured' => false]);

        $targetExam->is_featured = true;
        $targetExam->save();

        return response()->json([
            'status'  => 'success',
            'message' => "Paket '{$targetExam->title}' berhasil disambungkan ke Beranda Santri!",
            'data'    => $targetExam,
        ]);
    }
}