<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Show the single course detail page.
     */
    public function show(Course $course): \Inertia\Response
    {
        // Only published courses are publicly visible
        abort_unless($course->status === CourseStatus::Published, 404);

        $course->load([
            'instructor:id,name,avatar,bio',
            'category:id,name,slug',
            'sections.lessons:id,section_id,title,order',
        ]);

        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->exists();
        }

        $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail');

        // Add full avatar URL to instructor
        if ($course->instructor && $course->instructor->avatar) {
            $course->instructor->avatar_url = asset('storage/' . $course->instructor->avatar);
        } else {
            $course->instructor->avatar_url = null;
        }

        return \Inertia\Inertia::render('student/CourseShow', [
            'course' => array_merge($course->toArray(), [
                'instructor_id' => $course->instructor_id,
                'sections' => $course->sections,
            ]),
            'isEnrolled' => $isEnrolled,
        ]);
    }

    /**
     * Enroll a student in the course.
     */
    public function enroll(Course $course)
    {
        $user = auth()->user();
        
        abort_unless($user->hasRole('Student'), 403, 'Only students can enroll.');
        
        if (! $user->enrollments()->where('course_id', $course->id)->exists()) {
            $user->enrollments()->create([
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);
        }
        
        return redirect()->route('courses.show', $course->slug)->with('status', 'تم تسجيلك في الدورة بنجاح!');
    }
}
