<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',          // 'multiple_choice', 'essay', 'image_upload'
        'gclwama_tag',   // 'G', 'C', 'L', 'W', 'A_animasi', 'M', 'A_algoritma'
        'question_text',
        'options',
        'correct_answer',
    ];

    protected $hidden = [
        'correct_answer',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}