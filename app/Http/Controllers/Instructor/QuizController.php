<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Store a quiz for a specific section.
     */
    public function storeSectionQuiz(Request $request, Section $section)
    {
        if ($section->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if section already has a quiz
        if ($section->quizzes()->exists()) {
            return back()->with('error', 'هذه الوحدة تحتوي على اختبار مسبقاً.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $quiz = $section->course->quizzes()->create([
            'section_id' => $section->id,
            'title' => $request->title,
            'time_limit_minutes' => 15,
            'is_final_exam' => false,
        ]);

        return redirect()->route('instructor.quizzes.manage', $quiz->id)->with('success', 'تم إنشاء الاختبار، يمكنك الآن إضافة الأسئلة.');
    }

    /**
     * Store a final exam for a course.
     */
    public function storeFinalExam(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if course already has a final exam
        if ($course->quizzes()->where('is_final_exam', true)->exists()) {
            return back()->with('error', 'هذه الدورة تحتوي على اختبار نهائي مسبقاً.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $quiz = $course->quizzes()->create([
            'title' => $request->title,
            'time_limit_minutes' => 60,
            'is_final_exam' => true,
        ]);

        return redirect()->route('instructor.quizzes.manage', $quiz->id)->with('success', 'تم إنشاء الاختبار النهائي، يمكنك الآن إضافة الأسئلة.');
    }

    private function authorizeQuiz(Quiz $quiz)
    {
        $instructorId = $quiz->course_id 
            ? $quiz->course->instructor_id 
            : ($quiz->section ? $quiz->section->course->instructor_id : null);

        if ($instructorId !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display the quiz manager interface.
     */
    public function manage(Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $quiz->load(['questions.options', 'course', 'section']);

        return Inertia::render('instructor/QuizManager', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Store a new question with its options.
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'text' => ['required', 'string'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.text' => ['required', 'string'],
            'correct_option_index' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $quiz) {
            $question = $quiz->questions()->create([
                'text' => $request->text,
                'type' => 'single_choice',
                'points' => 10,
            ]);

            foreach ($request->options as $index => $optionData) {
                $question->options()->create([
                    'text' => $optionData['text'],
                    'is_correct' => $index === (int) $request->correct_option_index,
                ]);
            }
        });

        return back()->with('success', 'تم إضافة السؤال بنجاح.');
    }

    /**
     * Update the timer for a quiz.
     */
    public function updateTimer(Request $request, Quiz $quiz)
    {
        \Illuminate\Support\Facades\Log::info('Update timer hit', ['quiz_id' => $quiz->id, 'time' => $request->time_limit_minutes, 'user' => Auth::id()]);
        
        $this->authorizeQuiz($quiz);

        $request->validate([
            'time_limit_minutes' => ['required', 'integer', 'min:5', 'max:180'],
        ]);

        $quiz->update([
            'time_limit_minutes' => $request->time_limit_minutes,
        ]);

        return back()->with('success', 'تم تحديث مدة الاختبار بنجاح.');
    }

    /**
     * Delete a question.
     */
    public function destroyQuestion(Question $question)
    {
        $this->authorizeQuiz($question->quiz);

        $question->delete();

        return back()->with('success', 'تم حذف السؤال بنجاح.');
    }
}
