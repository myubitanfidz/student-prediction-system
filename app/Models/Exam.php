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
        'period_title',
        'description',
        'duration_minutes',
        'is_active',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}