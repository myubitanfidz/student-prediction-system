<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGradingService
{
    public static function grade(string $reference, string $studentAnswer): ?int
    {
        if (empty(trim($studentAnswer))) {
            Log::info('AI Grading Dilewati: Jawaban santri kosong.');
            return 0;
        }

        try {
            // Ambil URL dari .env, default ke 127.0.0.1:8000
            $url = env('AI_SERVICE_URL', 'http://127.0.0.1:8000');
            
            // Catat ke log bahwa Laravel mencoba memanggil Python
            Log::info("Mengirim data ke AI Service ({$url}/grade)...", [
                'referensi_kunci' => $reference,
                'jawaban_santri'  => $studentAnswer
            ]);

            // Timeout diperpanjang jadi 15 detik untuk antisipasi proses ML yang berat
            $response = Http::timeout(15)->post("{$url}/grade", [
                'reference'      => $reference,
                'student_answer' => $studentAnswer,
            ]);

            if ($response->successful()) {
                $score = (int) $response->json('final_score', 0);
                Log::info("AI Grading Berhasil! Skor yang didapat: {$score}");
                return $score;
            }

            // Jika gagal, catat error dari Python
            Log::error("AI Grading Gagal. Status HTTP: " . $response->status(), [
                'response' => $response->body()
            ]);
            
            return null; // Kembalikan null (Belum Dinilai) jika gagal

        } catch (\Exception $e) {
            Log::error("AI Service Error/Offline: " . $e->getMessage());
            return null;
        }
    }
}