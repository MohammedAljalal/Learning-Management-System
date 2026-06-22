<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Services\Gamification\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(GamificationService $gamificationService)
    {
        $user = Auth::user();

        // Enrolled courses with progress — single eager-loaded query
        $enrollments = Enrollment::with(['course' => function ($q) {
                $q->with(['instructor:id,name', 'category:id,name', 'media'])
                  ->withCount('sections');
            }])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Compute progress per course (ratio of completed lessons to total lessons)
        $progress = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            $totalLessons = LessonProgress::where('user_id', $user->id)
                ->whereHas('lesson.section', fn($q) => $q->where('course_id', $courseId))
                ->count();
            $completedLessons = LessonProgress::where('user_id', $user->id)
                ->where('completed', true)
                ->whereHas('lesson.section', fn($q) => $q->where('course_id', $courseId))
                ->count();
            $progress[$courseId] = $totalLessons > 0
                ? (int) round(($completedLessons / $totalLessons) * 100)
                : 0;
        }

        // XP & Level
        $totalXp = $user->total_xp;
        $level = $user->level;
        $xpForNextLevel = $gamificationService->getXpForNextLevel($user);
        $xpInCurrentLevel = $totalXp - (($level - 1) * 500);
        $levelProgress = (int) min(100, round(($xpInCurrentLevel / 500) * 100));

        // Recent XP transactions
        $recentXp = $user->xpTransactions()->latest()->take(5)->get();

        // Recent notifications
        $recentNotifications = $user->notifications()->take(5)->get();

        // Certificates
        $certificates = $user->certificates()->with('course:id,title,slug')->latest('issued_at')->get();

        return view('livewire.student.dashboard', compact(
            'enrollments', 'progress', 'totalXp', 'level',
            'xpForNextLevel', 'levelProgress', 'recentXp',
            'recentNotifications', 'certificates'
        ))->layout('layouts.app');
    }
}
