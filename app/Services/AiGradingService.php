<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGradingService
{
    public static function grade(string $reference, string $studentAnswer): int
    {
        if (empty(trim($studentAnswer))) {
            return 0;
        }

        try {
            $url = config('services.ai.url', env('AI_SERVICE_URL', 'http://127.0.0.1:8000'));
            $response = Http::timeout(4)->post("{$url}/grade", [
                'reference'      => $reference,
                'student_answer' => $studentAnswer,
            ]);

            if ($response->successful()) {
                return (int) $response->json('final_score', 0);
            }
        } catch (\Exception $e) {
            Log::warning("AI Grading offline: " . $e->getMessage());
        }

        return 0; // Nilai default jika service sedang offline
    }
}