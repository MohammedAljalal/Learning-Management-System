<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\Students\LessonProgressService;
use App\Traits\VerifiesEnrollment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

class CoursePlayer extends Component
{
    use VerifiesEnrollment;
    public Course $course;
    public Lesson $lesson;

    /** Saved position in seconds (restored on mount). */
    public int $savedPosition = 0;

    /** Signed URL for the video source. */
    public ?string $videoUrl = null;

    /** @var array<int, array> */
    /** @var array<int, array> */
    public array $interactiveQuestions = [];

    /** Array of unlocked lesson IDs for the sidebar UI */
    public array $unlockedLessonIds = [];

    public function mount(Course $course, Lesson $lesson, LessonProgressService $progressService): void
    {
        $this->course = $course;
        $this->lesson = $lesson->load('section');

        // Check if the lesson belongs to the requested course
        abort_unless($this->lesson->section->course_id === $this->course->id, 404, 'Lesson not found in this course.');

        // Enforce enrollment and publish status for students
        $this->verifyEnrollment($this->course);

        $this->calculateUnlockedLessons();

        if (!in_array($this->lesson->id, $this->unlockedLessonIds)) {
            session()->flash('error', 'يجب إكمال الدرس السابق قبل الانتقال إلى هذا الدرس.');
            $lastUnlocked = end($this->unlockedLessonIds);
            $this->redirectRoute('courses.learn', ['course' => $this->course->slug, 'lesson' => $lastUnlocked], navigate: true);
            return;
        }

        // Restore saved position
        $this->savedPosition = $progressService->getPosition(Auth::id(), $lesson->id);

        // Generate a signed URL for the video (valid for 2 hours)
        $mediaItem = $lesson->getFirstMedia('lesson_video');
        if ($mediaItem) {
            $this->videoUrl = URL::temporarySignedRoute(
                'lesson.video.stream',
                now()->addHours(2),
                ['media' => $mediaItem->id]
            );

            // Get interactive questions for the course/lesson
            $this->interactiveQuestions = \App\Models\Question::with('options')
                ->whereHas('quiz', function ($q) {
                    $q->where('section_id', $this->lesson->section_id);
                })
                ->whereNotNull('video_timestamp')
                ->get()
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'text' => $question->text,
                        'timestamp' => $question->video_timestamp,
                        'options' => $question->options->map(fn($opt) => ['id' => $opt->id, 'text' => $opt->text])->toArray(),
                        'answered' => false
                    ];
                })->toArray();
        }
    }

    protected function calculateUnlockedLessons(): void
    {
        $lessonIdsInOrder = DB::table('lessons')
            ->join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $this->course->id)
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->pluck('lessons.id')
            ->toArray();

        $completedLessonIds = \App\Models\LessonProgress::where('user_id', Auth::id())
            ->where('completed', true)
            ->whereIn('lesson_id', $lessonIdsInOrder)
            ->pluck('lesson_id')
            ->toArray();

        $this->unlockedLessonIds = [];
        
        foreach ($lessonIdsInOrder as $index => $id) {
            // First lesson is always unlocked
            if ($index === 0) {
                $this->unlockedLessonIds[] = $id;
                continue;
            }

            $previousLessonId = $lessonIdsInOrder[$index - 1];
            // Unlocked if the previous lesson is completed
            if (in_array($previousLessonId, $completedLessonIds)) {
                $this->unlockedLessonIds[] = $id;
            } else {
                // Since this one is locked, all subsequent ones are locked too
                break;
            }
        }
    }

    /**
     * Save the student's current playback position.
     * Called via Livewire dispatch from Alpine.js timeupdate event.
     */
    public function saveProgress(int $seconds, LessonProgressService $progressService): void
    {
        $progressService->savePosition(Auth::id(), $this->lesson->id, $seconds);
    }

    /**
     * Mark the lesson as completed and auto-navigate to the next step
     * (section quiz if last lesson in section, or next lesson, or course completion).
     */
    public function markCompleted(LessonProgressService $progressService): void
    {
        $progressService->markCompleted(Auth::id(), $this->lesson->id);

        // Find all lessons with their section info in order for this course
        $lessonsInOrder = DB::table('lessons')
            ->join('sections', 'lessons.section_id', '=', 'sections.id')
            ->where('sections.course_id', $this->course->id)
            ->orderBy('sections.order')
            ->orderBy('lessons.order')
            ->select('lessons.id', 'lessons.section_id')
            ->get()
            ->toArray();

        $lessonIds = array_column($lessonsInOrder, 'id');
        $currentIndex = array_search($this->lesson->id, $lessonIds);

        // Check if this is the LAST lesson in the current section
        $currentSectionId = $this->lesson->section_id;
        $nextLessonSectionId = isset($lessonsInOrder[$currentIndex + 1])
            ? $lessonsInOrder[$currentIndex + 1]->section_id
            : null;

        $isLastInSection = ($nextLessonSectionId !== $currentSectionId);

        // If last lesson in section, check for a section quiz first
        if ($isLastInSection) {
            $sectionQuiz = \App\Models\Quiz::where('section_id', $currentSectionId)
                ->whereNull('course_id')
                ->first();

            if ($sectionQuiz) {
                // Redirect to section quiz
                $this->redirectRoute('quizzes.take', $sectionQuiz->id);
                return;
            }
        }

        // If there is a next lesson (in next section or same section), go to it
        if (isset($lessonIds[$currentIndex + 1])) {
            $this->redirectRoute('courses.learn', [
                'course' => $this->course->slug,
                'lesson' => $lessonIds[$currentIndex + 1],
            ]);
            return;
        }

        // All lessons done — check for a final exam
        $finalExam = \App\Models\Quiz::where('course_id', $this->course->id)
            ->where('is_final_exam', true)
            ->first();

        if ($finalExam) {
            session()->flash('status', 'تهانينا! لقد أكملت جميع دروس الدورة. يمكنك الآن أداء الاختبار النهائي. 🎉');
            $this->redirectRoute('quizzes.take', $finalExam->id);
            return;
        }

        // No final exam — just show completion message
        session()->flash('status', 'تهانينا! لقد أكملت جميع دروس الدورة. 🎉');
        $this->redirectRoute('courses.show', $this->course->slug);
    }

    public function render()
    {
        $this->course->loadMissing(['sections.lessons:id,section_id,title,order']);

        return view('livewire.student.course-player')
            ->layout('layouts.app');
    }
}
