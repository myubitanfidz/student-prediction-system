<?php

namespace App\Models;

use App\Helpers\SecureId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'time_limit_seconds',
        'gclwama_tag',
        'question_text',
        'options',
        'correct_answer',
    ];

    protected $hidden = [
        'correct_answer', // Secara default disembunyikan
    ];

    protected $casts = [
        'options'            => 'array',
        'time_limit_seconds' => 'integer',
    ];

    protected $appends = ['hash_id'];

    public function getHashIdAttribute(): string
    {
        return SecureId::encode($this->id, 'question');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}