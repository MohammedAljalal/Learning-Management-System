<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'description',
        'time_limit_minutes',
        'is_practice',
        'is_final_exam',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_practice' => 'boolean',
            'is_final_exam' => 'boolean',
            'time_limit_minutes' => 'integer',
            'order' => 'integer',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
