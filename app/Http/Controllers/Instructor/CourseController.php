<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the instructor's courses.
     */
    public function index()
    {
        $courses = Course::with('category')
            ->where('instructor_id', Auth::id())
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Map media URLs
        $courses->getCollection()->transform(function ($course) {
            $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail');
            return $course;
        });

        return Inertia::render('instructor/CourseManager', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return Inertia::render('instructor/CourseCreate', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:20'],
            'price'       => ['required', 'numeric', 'min:0'],
            'difficulty'  => ['required', 'in:beginner,intermediate,expert'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'title.required'       => 'عنوان الدورة مطلوب.',
            'category_id.required' => 'يجب اختيار تصنيف.',
            'description.required' => 'وصف الدورة مطلوب.',
            'description.min'      => 'يجب أن لا يقل وصف الدورة عن 20 حرفاً.',
            'price.required'       => 'السعر مطلوب.',
            'price.numeric'        => 'السعر يجب أن يكون رقماً.',
        ]);

        $slug = Str::slug($request->title);
        if (Course::where('slug', $slug)->exists()) {
            $slug .= '-' . uniqid();
        }

        $course = Course::create([
            'instructor_id' => Auth::id(),
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'slug'          => $slug,
            'description'   => $request->description,
            'price'         => $request->price,
            'difficulty'    => $request->difficulty,
            'status'        => 'draft',
        ]);

        if ($request->hasFile('cover_image')) {
            $course->addMediaFromRequest('cover_image')->toMediaCollection('thumbnail');
        }

        return redirect('/instructor/courses/' . $course->id . '/builder')
                         ->with('success', 'تم إنشاء الدورة بنجاح. يمكنك الآن إضافة المحتوى.');
    }

    /**
     * Store a new section for the course.
     */
    public function storeSection(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $order = $course->sections()->max('order') ?? 0;

        $course->sections()->create([
            'title' => $request->title,
            'order' => $order + 1,
        ]);

        return back()->with('success', 'تم إضافة القسم بنجاح.');
    }

    /**
     * Store a new lesson for a specific section.
     */
    public function storeLesson(Request $request, \App\Models\Section $section)
    {
        if ($section->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video'   => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm', 'max:512000'],
        ], [
            'title.required' => 'عنوان الدرس مطلوب.',
            'video.required' => 'ملف الفيديو مطلوب.',
            'video.file'     => 'يجب أن يكون ملف فيديو صحيح.',
            'video.mimetypes'=> 'صيغة الفيديو غير مدعومة. استخدم MP4 أو MOV.',
            'video.max'      => 'حجم الفيديو يجب أن لا يتجاوز 500 ميجابايت.',
        ]);

        $order = $section->lessons()->max('order') ?? 0;

        $lesson = $section->lessons()->create([
            'title'   => $request->title,
            'content' => $request->content,
            'order'   => $order + 1,
        ]);

        if ($request->hasFile('video')) {
            $lesson->addMediaFromRequest('video')->toMediaCollection('lesson_video');
        }

        return back()->with('success', 'تم إضافة الدرس ورفع الفيديو بنجاح.');
    }

    /**
     * Update the course details.
     */
    public function update(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'price'       => ['required', 'numeric', 'min:0'],
            'difficulty'  => ['required', 'in:beginner,intermediate,expert'],
            'status'      => ['required', 'in:draft,published,archived'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $course->update([
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'difficulty'  => $request->difficulty,
            'status'      => $request->status,
        ]);

        if ($request->hasFile('cover_image')) {
            $course->clearMediaCollection('thumbnail');
            $course->addMediaFromRequest('cover_image')->toMediaCollection('thumbnail');
        }

        return back()->with('success', 'تم تحديث الدورة بنجاح.');
    }
}
