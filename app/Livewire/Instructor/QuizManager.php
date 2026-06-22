<?php

namespace App\Livewire\Instructor;

use App\Models\Quiz;
use Livewire\Component;

class QuizManager extends Component
{
    public Quiz $quiz;

    // Quiz settings
    public string $title = '';
    public string $description = '';
    public ?int $timeLimit = null;

    // New question state
    public string $newQuestionText = '';
    public string $newQuestionType = 'single_choice';

    public function mount(Quiz $quiz)
    {
        // Authorize: Only the instructor of the course can edit
        $course = $quiz->is_final_exam ? $quiz->course : $quiz->section->course;
        if ($course->instructor_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $this->quiz = $quiz;
        $this->title = $quiz->title;
        $this->description = $quiz->description ?? '';
        $this->timeLimit = $quiz->time_limit_minutes;
    }

    public function saveSettings()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'timeLimit' => 'nullable|integer|min:1',
        ]);

        $this->quiz->update([
            'title' => $this->title,
            'description' => $this->description,
            'time_limit_minutes' => $this->timeLimit,
        ]);

        session()->flash('success', 'تم حفظ إعدادات الاختبار.');
    }

    public function addQuestion()
    {
        $this->validate([
            'newQuestionText' => 'required|string',
            'newQuestionType' => 'required|in:single_choice,multiple_choice,true_false',
        ]);

        $this->quiz->questions()->create([
            'text' => $this->newQuestionText,
            'type' => $this->newQuestionType,
            'points' => 1,
        ]);

        $this->newQuestionText = '';
        $this->quiz->load('questions.options');
    }

    public function deleteQuestion(int $id)
    {
        $this->quiz->questions()->where('id', $id)->delete();
        $this->quiz->load('questions.options');
    }

    public function addOption(int $questionId)
    {
        $question = $this->quiz->questions()->find($questionId);
        if ($question) {
            $question->options()->create([
                'text' => 'خيار جديد',
                'is_correct' => false,
            ]);
            $this->quiz->load('questions.options');
        }
    }

    public function deleteOption(int $optionId)
    {
        \App\Models\QuestionOption::where('id', $optionId)->delete();
        $this->quiz->load('questions.options');
    }

    public function updateOptionText(int $optionId, string $text)
    {
        \App\Models\QuestionOption::where('id', $optionId)->update(['text' => $text]);
    }

    public function toggleOptionCorrectness(int $questionId, int $optionId)
    {
        $question = $this->quiz->questions()->find($questionId);
        if (!$question) return;

        if ($question->type->value === 'single_choice' || $question->type->value === 'true_false') {
            // Unmark others
            $question->options()->update(['is_correct' => false]);
            \App\Models\QuestionOption::where('id', $optionId)->update(['is_correct' => true]);
        } else {
            // Toggle
            $option = \App\Models\QuestionOption::find($optionId);
            $option->update(['is_correct' => !$option->is_correct]);
        }
        $this->quiz->load('questions.options');
    }

    public function render()
    {
        $this->quiz->loadMissing('questions.options');
        return view('livewire.instructor.quiz-manager')->layout('layouts.app');
    }
}
