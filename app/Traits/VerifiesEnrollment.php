<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

trait VerifiesEnrollment
{
    /**
     * Enforce enrollment and publish status for students.
     */
    protected function verifyEnrollment(Course $course): void
    {
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Instructor'])) {
            abort_unless($course->status->value === 'published', 403, 'This course is not published.');
            $isEnrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();
            abort_unless($isEnrolled, 403, 'You are not enrolled in this course.');
        }
    }
}
