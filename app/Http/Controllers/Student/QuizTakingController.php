<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Services\Assessment\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuizTakingController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options', 'section.course', 'course');
        $course = $this->quizCourse($quiz);

        // Verify Enrollment
        $isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->exists();
        abort_unless($isEnrolled, 403, 'You are not enrolled in this course.');

        // Get the latest attempt
        $attempt = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($attempt) {
            $attempt->load('answers.option');
        }

        // Calculate next steps for the UI (if completed)
        $nextUrl = null;
        $certificate = null;

        if ($attempt && $attempt->completed_at && $attempt->is_passed) {
            if ($quiz->is_final_exam) {
                $nextUrl = route('courses.show', $course->slug);
                $certificate = \App\Models\Certificate::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->first();
            } else {
                $nextSection = \App\Models\Section::where('course_id', $course->id)
                    ->where('order', '>', $quiz->section->order)
                    ->orderBy('order')
                    ->first();

                if ($nextSection) {
                    $firstLesson = DB::table('lessons')->where('section_id', $nextSection->id)->orderBy('order')->first();
                    if ($firstLesson) {
                        $nextUrl = route('courses.learn', [$course->slug, $firstLesson->id]);
                    }
                } else {
                    $finalExam = \App\Models\Quiz::where('course_id', $course->id)->where('is_final_exam', true)->first();
                    $nextUrl = $finalExam ? route('quizzes.take', $finalExam->id) : route('courses.show', $course->slug);
                }
            }
        }

        return Inertia::render('student/QuizTaking', [
            'quiz' => $quiz,
            'course' => $course,
            'attempt' => $attempt,
            'nextUrl' => $nextUrl,
            'certificate' => $certificate,
        ]);
    }

    public function start(Quiz $quiz)
    {
        $course = $this->quizCourse($quiz);
        $isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->exists();
        abort_unless($isEnrolled, 403, 'You are not enrolled in this course.');

        $this->quizService->startAttempt(Auth::id(), $quiz);

        return redirect()->back();
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $attempt = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()->back()->with('error', 'لا يوجد محاولة نشطة لهذا الاختبار.');
        }

        $answers = $request->input('answers', []);
        $this->quizService->submitAttempt($attempt, $answers);

        return redirect()->back()->with('success', 'تم تسليم الاختبار.');
    }

    protected function quizCourse(Quiz $quiz)
    {
        return $quiz->is_final_exam ? $quiz->course : $quiz->section->course;
    }
}
