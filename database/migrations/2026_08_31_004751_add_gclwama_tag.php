<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('gclwama_tag', [
                'G',          // Gambar
                'C',          // Cerita
                'L',          // Layout
                'W',          // Warna
                'A_animasi',  // Animasi
                'M',          // Matematika
                'A_algoritma' // Algoritma
            ])->nullable()->after('type');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('answer_text');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('gclwama_tag');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};