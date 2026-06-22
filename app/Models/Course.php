<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'difficulty',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'difficulty' => CourseDifficulty::class,
            'status' => CourseStatus::class,
        ];
    }

    /**
     * Get the instructor that owns the course.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the category that the course belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the sections for the course.
     */
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    /**
     * Get all enrollments for this course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all transactions for this course.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all quizzes for this course.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }
}
