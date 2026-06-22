<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'enrolled_at'];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
        ];
    }

    /**
     * Get the student (user) of this enrollment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
