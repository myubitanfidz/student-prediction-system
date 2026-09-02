<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🌟 Batasi pengambilan soal: Maksimal 20 kali per menit per santri/IP
        RateLimiter::for('exam-fetch', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        // 🌟 Batasi pengumpulan ujian: Maksimal 3 kali per 5 menit per santri/IP (Anti-Spam/Race Condition)
        RateLimiter::for('exam-submit', function (Request $request) {
            return Limit::perMinutes(5, 3)->by($request->user()?->id ?: $request->ip());
        });
    }
}