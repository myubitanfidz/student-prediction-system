<?php

namespace App\Models;

use App\Helpers\SecureId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'subcategory',
        'home_slot', 
        'title',
        'period_title',
        'description',
        'duration_minutes',
        'is_active',
        'is_featured',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean', // 🌟 Cast boolean
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
    ];

    protected $appends = ['hash_id'];

    public function getHashIdAttribute(): string
    {
        return SecureId::encode($this->id, 'exam');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}