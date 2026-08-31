<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['portfolio', 'answers.question.exam']);

        // 1. Rekap Per Paket Ujian Standar
        $examResults = Exam::all()->map(function ($exam) use ($user) {
            $answers = $user->answers->filter(fn ($ans) => $ans->question && $ans->question->exam_id === $exam->id);
            $mcAnswers = $answers->filter(fn ($ans) => $ans->question->type === 'multiple_choice');

            $mcTotal = $mcAnswers->count();
            $mcCorrect = $mcAnswers->where('score', '>=', 100)->count();
            $percentage = $mcTotal > 0 ? round(($mcCorrect / $mcTotal) * 100, 2) : 0;

            return [
                'exam_id'         => $exam->id,
                'category'        => $exam->category,
                'subcategory'     => $exam->subcategory,
                'exam_title'      => $exam->title,
                'answered_count'  => $answers->count(),
                'mc_accuracy_pct' => $percentage,
                'total_score'     => $answers->sum('score'),
            ];
        });

        // 2. Mesin Agregasi GCLWAMA
        $tagScores = [
            'G'           => [],
            'C'           => [],
            'L'           => [],
            'W'           => [],
            'A_animasi'   => [],
            'M'           => [],
            'A_algoritma' => [],
        ];

        foreach ($user->answers as $ans) {
            $tag = $ans->question?->gclwama_tag;
            if ($tag && isset($tagScores[$tag]) && $ans->score !== null) {
                $tagScores[$tag][] = (float) $ans->score;
            }
        }

        // Hitung rata-rata tiap tag
        $avgTag = [];
        foreach ($tagScores as $tag => $scores) {
            $avgTag[$tag] = count($scores) > 0 ? (array_sum($scores) / count($scores)) : 0;
        }

        // Helper perhitungan rata-rata gabungan
        $calcCareer = function (...$tags) use ($avgTag) {
            $valid = collect($tags)->filter(fn ($t) => isset($avgTag[$t]));
            return $valid->count() > 0 ? round($valid->map(fn ($t) => $avgTag[$t])->average(), 1) : 0;
        };

        // 3. Perhitungan 4 Peminatan Karir IT
        $careerPredictions = [
            'Komik'       => $calcCareer('G', 'W', 'A_animasi'),
            'DKV'         => $calcCareer('L', 'W'),
            'Videografi'  => $calcCareer('C', 'A_animasi'),
            'Programming' => $calcCareer('M', 'A_algoritma'),
        ];

        // Urutkan untuk mendapatkan kecenderungan tertinggi
        arsort($careerPredictions);
        $topCareer = array_key_first($careerPredictions);
        $topScore = reset($careerPredictions);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'student' => [
                    'id'                 => $user->id,
                    'name'               => $user->name,
                    'email'              => $user->email,
                    'top_inclination'    => $topCareer,
                    'top_score'          => $topScore,
                    'career_predictions' => $careerPredictions,
                    'gclwama_breakdown'  => [
                        'Gambar (G)'          => round($avgTag['G'], 1),
                        'Cerita (C)'          => round($avgTag['C'], 1),
                        'Layout (L)'          => round($avgTag['L'], 1),
                        'Warna (W)'           => round($avgTag['W'], 1),
                        'Animasi (A)'         => round($avgTag['A_animasi'], 1),
                        'Matematika (M)'      => round($avgTag['M'], 1),
                        'Algoritma (A)'       => round($avgTag['A_algoritma'], 1),
                    ],
                ],
                'exam_stats' => $examResults,
                'portfolio'  => $user->portfolio,
            ],
        ]);
    }
}