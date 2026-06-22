<?php

declare(strict_types=1);

namespace App\Livewire\Instructor;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Services\Courses\LessonService;
use App\Services\Courses\SectionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Quiz;

class CourseBuilder extends Component
{
    use WithFileUploads;

    public Course $course;

    // Section Form State
    public bool $showSectionModal = false;
    public ?int $editingSectionId = null;
    public string $sectionTitle = '';
    public string $sectionDescription = '';
    public int $sectionOrder = 0;

    // Lesson Form State
    public bool $showLessonModal = false;
    public ?int $editingLessonId = null;
    public ?int $lessonSectionId = null;
    public string $lessonTitle = '';
    public string $lessonContent = '';
    public int $lessonOrder = 0;
    public $videoFile; // Livewire uploaded file

    public function mount(Course $course): void
    {
        // Authorize: Instructor must own the course
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
        $this->course = $course;
    }

    // --- Section Management ---

    public function openSectionModal(?int $sectionId = null): void
    {
        $this->resetValidation();
        if ($sectionId) {
            $section = Section::findOrFail($sectionId);
            $this->editingSectionId = $section->id;
            $this->sectionTitle = $section->title;
            $this->sectionDescription = $section->description ?? '';
            $this->sectionOrder = $section->order;
        } else {
            $this->reset(['editingSectionId', 'sectionTitle', 'sectionDescription', 'sectionOrder']);
            $this->sectionOrder = $this->course->sections()->max('order') + 1;
        }
        $this->showSectionModal = true;
    }

    public function saveSection(SectionService $sectionService): void
    {
        $this->validate([
            'sectionTitle' => 'required|string|max:255',
            'sectionDescription' => 'nullable|string',
            'sectionOrder' => 'required|integer|min:0',
        ]);

        $data = [
            'course_id' => $this->course->id,
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription ?: null,
            'order' => $this->sectionOrder,
        ];

        if ($this->editingSectionId) {
            $section = Section::findOrFail($this->editingSectionId);
            $sectionService->updateSection($section, $data);
            session()->flash('success', 'تم تحديث الوحدة.');
        } else {
            $sectionService->createSection($data);
            session()->flash('success', 'تم إضافة الوحدة.');
        }

        $this->showSectionModal = false;
        $this->course->refresh();
    }

    public function deleteSection(int $id, SectionService $sectionService): void
    {
        $section = Section::findOrFail($id);
        $sectionService->deleteSection($section);
        session()->flash('success', 'تم حذف الوحدة.');
        $this->course->refresh();
    }

    public function createSectionQuiz(int $sectionId)
    {
        $quiz = Quiz::create([
            'section_id' => $sectionId,
            'title' => 'اختبار وحدة جديد',
            'time_limit_minutes' => 15,
        ]);
        
        return redirect()->route('instructor.quizzes.manage', $quiz->id);
    }

    public function createFinalExam()
    {
        // Check if final exam already exists
        $existingExam = Quiz::where('course_id', $this->course->id)->where('is_final_exam', true)->first();
        if ($existingExam) {
            return redirect()->route('instructor.quizzes.manage', $existingExam->id);
        }

        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'is_final_exam' => true,
            'title' => 'الاختبار النهائي للدورة',
            'time_limit_minutes' => 60,
        ]);
        
        return redirect()->route('instructor.quizzes.manage', $quiz->id);
    }

    // --- Lesson Management ---

    public function openLessonModal(int $sectionId, ?int $lessonId = null): void
    {
        $this->resetValidation();
        $this->lessonSectionId = $sectionId;

        if ($lessonId) {
            $lesson = Lesson::findOrFail($lessonId);
            $this->editingLessonId = $lesson->id;
            $this->lessonTitle = $lesson->title;
            $this->lessonContent = $lesson->content ?? '';
            $this->lessonOrder = $lesson->order;
        } else {
            $this->reset(['editingLessonId', 'lessonTitle', 'lessonContent', 'videoFile']);
            $this->lessonOrder = Section::find($sectionId)->lessons()->max('order') + 1;
        }
        $this->showLessonModal = true;
    }

    public function saveLesson(LessonService $lessonService): void
    {
        $this->validate([
            'lessonTitle' => 'required|string|max:255',
            'lessonContent' => 'nullable|string',
            'lessonOrder' => 'required|integer|min:0',
            'videoFile' => 'nullable|mimes:mp4,mov,avi|max:51200', // max 50MB
        ]);

        $data = [
            'section_id' => $this->lessonSectionId,
            'title' => $this->lessonTitle,
            'content' => $this->lessonContent ?: null,
            'order' => $this->lessonOrder,
        ];

        if ($this->editingLessonId) {
            $lesson = Lesson::findOrFail($this->editingLessonId);
            $lessonService->updateLesson($lesson, $data, $this->videoFile);
            session()->flash('success', 'تم تحديث الدرس.');
        } else {
            $lessonService->createLesson($data, $this->videoFile);
            session()->flash('success', 'تم إضافة الدرس.');
        }

        $this->showLessonModal = false;
        $this->reset(['videoFile']);
        $this->course->refresh();
    }

    public function deleteLesson(int $id, LessonService $lessonService): void
    {
        $lesson = Lesson::findOrFail($id);
        $lessonService->deleteLesson($lesson);
        session()->flash('success', 'تم حذف الدرس.');
        $this->course->refresh();
    }

    public function render()
    {
        $this->course->load(['sections.lessons.media']);

        return view('livewire.instructor.course-builder')
            ->layout('layouts.app');
    }
}
