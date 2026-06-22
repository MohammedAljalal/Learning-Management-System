<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Enrollment;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.instructor:id,name', 'course.media'])
            ->get();

        $certificates = Certificate::where('user_id', $user->id)
            ->with('course:id,title')
            ->get();

        $progress = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            
            // Calculate actual progress based on completed lessons
            $totalLessons = \Illuminate\Support\Facades\DB::table('lessons')
                ->join('sections', 'lessons.section_id', '=', 'sections.id')
                ->where('sections.course_id', $courseId)
                ->count();

            if ($totalLessons === 0) {
                $progress[$courseId] = 0;
            } else {
                $completedLessons = \Illuminate\Support\Facades\DB::table('lesson_progress')
                    ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
                    ->join('sections', 'lessons.section_id', '=', 'sections.id')
                    ->where('sections.course_id', $courseId)
                    ->where('lesson_progress.user_id', $user->id)
                    ->where('lesson_progress.completed', 1)
                    ->count();

                $progress[$courseId] = (int) round(($completedLessons / $totalLessons) * 100);
            }

            if ($enrollment->course) {
                $enrollment->course->thumbnail_url = $enrollment->course->getFirstMediaUrl('thumbnail');
            }
        }

        return Inertia::render('Dashboard', [
            'enrollments' => $enrollments,
            'progress' => $progress,
            'certificates' => $certificates,
            'recentXp' => [],
            'recentNotifications' => $user->notifications()->take(5)->get(),
            'totalXp' => 1250,
            'level' => 5,
            'levelProgress' => 65,
            'xpForNextLevel' => 250,
        ]);
    }
}
