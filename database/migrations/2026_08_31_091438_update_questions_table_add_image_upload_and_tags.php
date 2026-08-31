<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah enum kolom type agar menerima 'image_upload'
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM('multiple_choice', 'essay', 'image_upload') NOT NULL");

        // 2. Pastikan kolom gclwama_tag sudah ada dan bertipe enum lengkap
        if (!Schema::hasColumn('questions', 'gclwama_tag')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->enum('gclwama_tag', ['G', 'C', 'L', 'W', 'A_animasi', 'M', 'A_algoritma'])
                      ->nullable()
                      ->after('type');
            });
        } else {
            DB::statement("ALTER TABLE questions MODIFY COLUMN gclwama_tag ENUM('G', 'C', 'L', 'W', 'A_animasi', 'M', 'A_algoritma') NULL");
        }

        // 3. Pastikan kolom file_path ada di student_answers untuk menyimpan upload gambar santri
        if (!Schema::hasColumn('student_answers', 'file_path')) {
            Schema::table('student_answers', function (Blueprint $table) {
                $table->string('file_path')->nullable()->after('answer_text');
            });
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM('multiple_choice', 'essay') NOT NULL");
    }
};