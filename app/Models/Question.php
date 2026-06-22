<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'type',
        'text',
        'points',
        'video_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'points' => 'integer',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }
}
