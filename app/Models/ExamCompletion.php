<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'exam_id', 
        'started_at',     // Baru
        'session_nonce',  // Baru
        'completed_at', 
        'retake_allowed'
    ];

    protected $casts = [
        'started_at'     => 'datetime',
        'completed_at'   => 'datetime',
        'retake_allowed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}