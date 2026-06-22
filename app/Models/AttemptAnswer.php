<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'question_option_id',
        'answer_text',
        'is_correct',
        'points_awarded',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'points_awarded' => 'integer',
        ];
    }

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
