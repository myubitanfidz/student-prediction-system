<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'subcategory',
        'title',
        'description',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(ExamCompletion::class);
    }
}