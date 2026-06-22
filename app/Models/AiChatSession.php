<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class);
    }
}
