<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // All courses by this instructor
        $courses = Course::where('instructor_id', $user->id)
            ->withCount(['enrollments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $courses->transform(function ($course) {
            $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail');
            return $course;
        });

        $totalCourses    = $courses->count();
        $publishedCourses = $courses->where('status', 'published')->count();
        $draftCourses    = $courses->where('status', 'draft')->count();
        $totalStudents   = $courses->sum('enrollments_count');

        // Recent enrollments across instructor courses
        $courseIds = $courses->pluck('id');
        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user:id,name', 'course:id,title'])
            ->orderByDesc('enrolled_at')
            ->take(8)
            ->get();

        // Total earnings from transactions
        $totalEarnings = DB::table('transactions')
            ->whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->sum('amount');

        return Inertia::render('instructor/InstructorDashboard', [
            'stats' => [
                'totalCourses'    => $totalCourses,
                'publishedCourses' => $publishedCourses,
                'draftCourses'    => $draftCourses,
                'totalStudents'   => $totalStudents,
                'totalEarnings'   => (float) $totalEarnings,
            ],
            'courses'           => $courses,
            'recentEnrollments' => $recentEnrollments,
        ]);
    }
}
