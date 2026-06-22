<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Models\Course;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $featuredCourses = Course::query()
            ->where('status', CourseStatus::Published)
            ->with(['instructor:id,name,avatar', 'category:id,name'])
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take(6)
            ->get();

        return Inertia::render('Welcome', [
            'featuredCourses' => $featuredCourses,
        ]);
    }
}
