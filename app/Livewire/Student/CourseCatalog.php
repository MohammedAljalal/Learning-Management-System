<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class CourseCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $difficultyFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'difficultyFilter' => ['except' => ''],
    ];

    /**
     * Reset pagination when filters change.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDifficultyFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $courses = Course::query()
            ->where('status', CourseStatus::Published)
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->difficultyFilter, fn ($q) => $q->where('difficulty', $this->difficultyFilter))
            ->with(['instructor:id,name,avatar', 'category:id,name', 'media'])
            ->withCount('enrollments')
            ->latest()
            ->paginate(12);

        $categories = app(\App\Services\Courses\CategoryService::class)->getRootCategories();

        return view('livewire.student.course-catalog', [
            'courses' => $courses,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
