<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Services\Courses\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseCatalogController extends Controller
{
    public function __invoke(Request $request, CategoryService $categoryService)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category');
        $difficultyFilter = $request->input('difficulty');

        $courses = Course::query()
            ->where('status', CourseStatus::Published)
            ->when($search, fn ($q) => $q->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%'))
            ->when($categoryFilter, fn ($q) => $q->where('category_id', $categoryFilter))
            ->when($difficultyFilter, fn ($q) => $q->where('difficulty', $difficultyFilter))
            ->with(['instructor:id,name,avatar', 'category:id,name', 'media'])
            ->withCount('enrollments')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $courses->getCollection()->transform(function ($course) {
            $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail');
            if ($course->instructor && $course->instructor->avatar) {
                $course->instructor->avatar_url = asset('storage/' . $course->instructor->avatar);
            }
            return $course;
        });

        $categories = $categoryService->getRootCategories();

        return Inertia::render('student/CourseCatalog', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $categoryFilter,
                'difficulty' => $difficultyFilter,
            ]
        ]);
    }
}
