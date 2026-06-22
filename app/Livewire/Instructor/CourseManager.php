<?php

declare(strict_types=1);

namespace App\Livewire\Instructor;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Services\Courses\CourseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CourseManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $categoryId = '';
    public string $price = '0.00';
    public string $difficulty = 'beginner';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'categoryId' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'difficulty' => 'required|in:beginner,intermediate,expert',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['title', 'description', 'categoryId', 'price', 'difficulty', 'editingId']);
        $this->difficulty = 'beginner';
        $this->price = '0.00';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($id);
        $this->editingId = $course->id;
        $this->title = $course->title;
        $this->description = $course->description;
        $this->categoryId = (string) $course->category_id;
        $this->price = (string) $course->price;
        $this->difficulty = $course->difficulty->value;
        $this->showModal = true;
    }

    public function save(CourseService $courseService): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => (int) $this->categoryId,
            'price' => (float) $this->price,
            'difficulty' => $this->difficulty,
            'instructor_id' => Auth::id(),
        ];

        if ($this->editingId) {
            $course = Course::where('instructor_id', Auth::id())->findOrFail($this->editingId);
            $courseService->updateCourse($course, $data);
            session()->flash('success', 'تم تحديث الدورة بنجاح.');
        } else {
            $courseService->createCourse($data);
            session()->flash('success', 'تم إنشاء الدورة بنجاح.');
        }

        $this->reset(['title', 'description', 'categoryId', 'price', 'difficulty', 'editingId', 'showModal']);
        $this->resetPage();
    }

    public function toggleStatus(int $id, CourseService $courseService): void
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($id);
        
        $newStatus = $course->status === CourseStatus::Published
            ? CourseStatus::Draft
            : CourseStatus::Published;

        $courseService->changeStatus($course, $newStatus);
        session()->flash('success', 'تم تغيير حالة الدورة.');
    }

    public function delete(int $id, CourseService $courseService): void
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($id);
        $courseService->deleteCourse($course);
        session()->flash('success', 'تم حذف الدورة.');
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['title', 'description', 'categoryId', 'price', 'difficulty', 'editingId']);
    }

    public function render()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->with(['category:id,name'])
            ->withCount(['sections', 'enrollments'])
            ->latest()
            ->paginate(15);

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('livewire.instructor.course-manager', compact('courses', 'categories'))
            ->layout('layouts.app');
    }
}
