<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\Assessment\QuizService;
use App\Traits\VerifiesEnrollment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizTaking extends Component
{
    use VerifiesEnrollment;
    public Quiz $quiz;
    public ?QuizAttempt $attempt = null;

    /**
     * User's answers. Key is question_id, Value is selected option_id (or array of ids, or text string).
     */
    public array $answers = [];

    /**
     * Set to true when time is up to force submission on the next render.
     */
    public bool $timeExpired = false;

    /** URL to navigate to after completing the quiz */
    public ?string $nextStepUrl = null;

    /** Label for the next step button */
    public string $nextStepLabel = 'المتابعة';

    /** Error message shown when student tries to submit without answering all questions */
    public ?string $unansweredError = null;

    public function mount(Quiz $quiz): void
    {
        $this->quiz = $quiz->load('questions.options', 'section.course', 'course');
        $course = $this->quiz->is_final_exam ? $this->quiz->course : $this->quiz->section->course;

        // Enforce enrollment and publish status for students
        $this->verifyEnrollment($course);

        // Resume existing incomplete attempt if any
        $this->attempt = \App\Models\QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('user_id', Auth::id())
            ->whereNull('completed_at')
            ->latest()
            ->first();
        
        // Initialize answers array
        foreach ($this->quiz->questions as $question) {
            $this->answers[$question->id] = $question->type->value === 'multiple_choice' ? [] : null;
        }
    }

    public function startQuiz(QuizService $quizService): void
    {
        $this->attempt = $quizService->startAttempt(Auth::id(), $this->quiz);
        $this->timeExpired = false;
        
        // Reset answers array
        foreach ($this->quiz->questions as $question) {
            $this->answers[$question->id] = $question->type->value === 'multiple_choice' ? [] : null;
        }
    }

    public function submitQuiz(QuizService $quizService): void
    {
        if (!$this->attempt || $this->attempt->completed_at) {
            return;
        }

        // Validate all questions are answered (skip for time-up auto-submit)
        if (!$this->timeExpired) {
            $unanswered = [];
            foreach ($this->quiz->questions as $index => $question) {
                $answer = $this->answers[$question->id] ?? null;
                $isEmpty = is_null($answer) || $answer === '' || $answer === [];
                if ($isEmpty) {
                    $unanswered[] = $index + 1;
                }
            }

            if (!empty($unanswered)) {
                $this->unansweredError = 'يرجى الإجابة على جميع الأسئلة قبل التسليم. الأسئلة غير المجاب عليها: ' . implode(', ', $unanswered);
                return;
            }
        }

        $this->unansweredError = null;
        $this->attempt = $quizService->submitAttempt($this->attempt, $this->answers);
        $this->computeNextStep();
    }

    /**
     * Calculate where the student should go after completing this quiz.
     */
    protected function computeNextStep(): void
    {
        if ($this->quiz->is_final_exam) {
            $course = $this->quiz->course;
            $this->nextStepUrl = route('courses.show', $course->slug);
            $this->nextStepLabel = 'العودة لصفحة الدورة';
            return;
        }

        // For section quiz: find first lesson of the NEXT section
        $course = $this->quiz->section->course;
        $nextSection = \App\Models\Section::where('course_id', $course->id)
            ->where('order', '>', $this->quiz->section->order)
            ->orderBy('order')
            ->first();

        if ($nextSection) {
            $firstLesson = DB::table('lessons')
                ->where('section_id', $nextSection->id)
                ->orderBy('order')
                ->first();

            if ($firstLesson) {
                $this->nextStepUrl = route('courses.learn', [$course->slug, $firstLesson->id]);
                $this->nextStepLabel = 'الانتقال إلى الوحدة التالية';
                return;
            }
        }

        // No next section - all done, check for final exam
        $finalExam = \App\Models\Quiz::where('course_id', $course->id)
            ->where('is_final_exam', true)
            ->first();

        if ($finalExam) {
            $this->nextStepUrl = route('quizzes.take', $finalExam->id);
            $this->nextStepLabel = 'أداء الاختبار النهائي للدورة';
            return;
        }

        $this->nextStepUrl = route('courses.show', $course->slug);
        $this->nextStepLabel = 'العودة لصفحة الدورة';
    }

    /**
     * Triggered by AlpineJS when timer reaches 0.
     */
    public function timeUp(QuizService $quizService): void
    {
        $this->timeExpired = true;
        $this->submitQuiz($quizService);
    }

    /**
     * Triggered by AlpineJS when student leaves the tab in a final exam.
     */
    public function failExam(QuizService $quizService): void
    {
        if ($this->quiz->is_final_exam && $this->attempt && !$this->attempt->completed_at) {
            // Force submit with no answers to fail
            $this->answers = [];
            $this->submitQuiz($quizService);
            session()->flash('error', 'تم إلغاء الاختبار ورسوبك بسبب خروجك من نافذة الاختبار. نظام الحماية يمنع مغادرة الصفحة أثناء الاختبار النهائي.');
            $this->redirectRoute('courses.show', $this->quiz->course->slug);
        }
    }

    public function render()
    {
        // Always eager-load relationships to prevent lazy loading violations
        // after Livewire re-hydrates the component between requests.
        $this->quiz->loadMissing('questions.options');

        if ($this->attempt) {
            $this->attempt->loadMissing('answers.option');
        }

        return view('livewire.student.quiz-taking')->layout('layouts.app');
    }
}
