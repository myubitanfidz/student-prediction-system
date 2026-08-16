<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCompletion extends Model
{
    protected $fillable = ['user_id', 'exam_id', 'completed_at'];

    protected $casts = ['completed_at' => 'datetime'];
}
