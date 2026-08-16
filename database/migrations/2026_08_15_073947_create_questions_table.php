<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'essay']);
            $table->text('question_text');
            $table->json('options')->nullable(); // Untuk menyimpan pilihan A, B, C, D (hanya untuk multiple_choice)
            $table->string('correct_answer')->nullable(); // Opsional: kunci jawaban untuk PG
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};