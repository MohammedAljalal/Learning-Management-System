<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\Students\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class CourseLearnController extends Controller
{
    public function show(Course $course, Lesson $lesson, LessonProgressService $progressService)
    {
        $lesson->load('section');

        // Check if the lesson belongs to the requested course
        abort_unless($lesson->section->course_id === $course->id, 404, 'Lesson not found in this course.');

        // Verify Enrollment or Admin/Instructor access
        $user = auth()->user();
        $isEnrolled = $user->enrollments()->where('course_id', $course->id)->exists();
        $canAccess = $isEnrolled || $user->hasRole('Super Admin') || $course->instructor_id === $user->id;
        abort_unless($canAccess, 403, 'You are not enrolled in this course.');

        $course->load(['sections.lessons:id,section_id,title,order', 'instructor:id,name,avatar']);

        // Calculate unlocked lessons
        $lessonIdsInOrder = DB::table('lessons')
            ->join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->pluck('lessons.id')
            ->toArray();

        $completedLessonIds = LessonProgress::where('user_id', Auth::id())
            ->where('completed', true)
            ->whereIn('lesson_id', $lessonIdsInOrder)
            ->pluck('lesson_id')
            ->toArray();

        $unlockedLessonIds = [];
        foreach ($lessonIdsInOrder as $index => $id) {
            if ($index === 0) {
                $unlockedLessonIds[] = $id;
                continue;
            }
            $previousLessonId = $lessonIdsInOrder[$index - 1];
            if (in_array($previousLessonId, $completedLessonIds)) {
                $unlockedLessonIds[] = $id;
            } else {
                break;
            }
        }

        if (!in_array($lesson->id, $unlockedLessonIds)) {
            $lastUnlocked = end($unlockedLessonIds) ?: $lessonIdsInOrder[0];
            return redirect()->route('courses.learn', ['course' => $course->slug, 'lesson' => $lastUnlocked])
                ->with('error', 'يجب إكمال الدرس السابق قبل الانتقال إلى هذا الدرس.');
        }

        $savedPosition = $progressService->getPosition(Auth::id(), $lesson->id);

        $videoUrl = null;
        $mediaItem = $lesson->getFirstMedia('lesson_video');
        if ($mediaItem) {
            $videoUrl = URL::temporarySignedRoute(
                'lesson.video.stream',
                now()->addHours(2),
                ['media' => $mediaItem->id]
            );
        }

        return Inertia::render('student/CoursePlayer', [
            'course' => $course,
            'currentLesson' => $lesson,
            'sections' => $course->sections,
            'videoUrl' => $videoUrl,
            'savedPosition' => $savedPosition,
            'unlockedLessonIds' => $unlockedLessonIds,
            'completedLessonIds' => $completedLessonIds,
        ]);
    }

    public function markCompleted(Course $course, Lesson $lesson, LessonProgressService $progressService)
    {
        $progressService->markCompleted(Auth::id(), $lesson->id);

        $lessonsInOrder = DB::table('lessons')
            ->join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $course->id)
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->select('lessons.id', 'lessons.section_id')
            ->get()
            ->toArray();

        $lessonIds = array_column($lessonsInOrder, 'id');
        $currentIndex = array_search($lesson->id, $lessonIds);

        $currentSectionId = $lesson->section_id;
        $nextLessonSectionId = isset($lessonsInOrder[$currentIndex + 1])
            ? $lessonsInOrder[$currentIndex + 1]->section_id
            : null;

        $isLastInSection = ($nextLessonSectionId !== $currentSectionId);

        if ($isLastInSection) {
            $sectionQuiz = \App\Models\Quiz::where('section_id', $currentSectionId)
                ->where('course_id', $course->id)
                ->first();

            if ($sectionQuiz) {
                return redirect()->route('quizzes.take', $sectionQuiz->id);
            }
        }

        if (isset($lessonIds[$currentIndex + 1])) {
            return redirect()->route('courses.learn', [
                'course' => $course->slug,
                'lesson' => $lessonIds[$currentIndex + 1],
            ]);
        }

        $finalExam = \App\Models\Quiz::where('course_id', $course->id)
            ->where('is_final_exam', true)
            ->first();

        if ($finalExam) {
            return redirect()->route('quizzes.take', $finalExam->id)
                ->with('success', 'تهانينا! لقد أكملت جميع دروس الدورة. يمكنك الآن أداء الاختبار النهائي.');
        }

        return redirect()->route('courses.show', $course->slug)
            ->with('success', 'تهانينا! لقد أكملت جميع دروس الدورة.');
    }
}
