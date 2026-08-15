<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // e.g., 'bahasa', 'it', 'karakter'
            $table->string('subcategory'); // e.g., 'inggris', 'arab', 'programming', 'dkv', 'dkf', 'komik', 'videografi', 'portofolio', 'aqidah_akhlak'
            $table->string('title'); // e.g., 'Ujian Masuk Pemrograman Dasar'
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};