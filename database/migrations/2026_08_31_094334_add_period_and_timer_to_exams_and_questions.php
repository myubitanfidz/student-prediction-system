<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('period_title')->nullable()->default('PSB 2026/2027')->after('title');
            $table->boolean('is_active')->default(true)->after('period_title');
            $table->dateTime('start_time')->nullable()->after('is_active');
            $table->dateTime('end_time')->nullable()->after('start_time');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedInteger('time_limit_seconds')->default(60)->after('type'); // Default 60 detik per soal
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['period_title', 'is_active', 'start_time', 'end_time']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('time_limit_seconds');
        });
    }
};