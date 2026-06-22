<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'description', 'order'];

    /**
     * Get the course that owns the section.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lessons for the section.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get the quizzes for the section.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }
}
