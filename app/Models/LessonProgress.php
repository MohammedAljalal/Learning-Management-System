<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'last_position_seconds',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'last_position_seconds' => 'integer',
        ];
    }

    /**
     * Get the user this progress belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson this progress belongs to.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
