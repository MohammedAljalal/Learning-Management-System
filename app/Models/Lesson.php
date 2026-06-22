<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['section_id', 'title', 'content', 'order'];

    /**
     * Get the section that owns the lesson.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get progress records for this lesson.
     */
    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function registerMediaCollections(): void
    {
        // For video uploads (Stage 6 custom player)
        $this->addMediaCollection('lesson_video')->singleFile();

        // For standard file attachments
        $this->addMediaCollection('lesson_attachments');
    }
}
