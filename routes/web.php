<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\CourseCatalogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CertificateController;
use Inertia\Inertia;

// Landing Page
Route::get('/', WelcomeController::class)->name('home');

// Authenticated dashboard
Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// User Profile
Route::get('profile', fn() => Inertia::render('Profile'))
    ->middleware(['auth'])
    ->name('profile');

Route::patch('profile', [\App\Http\Controllers\ProfileController::class, 'update'])
    ->middleware(['auth'])
    ->name('profile.update');

Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'update'])
    ->middleware(['auth'])
    ->name('profile.update.post');

Route::put('password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
    ->middleware(['auth'])
    ->name('password.update');

// ─────────────────────────────────────────────
// Public Student-Facing Routes
// ─────────────────────────────────────────────

// Course Catalog
Route::get('/courses', CourseCatalogController::class)->name('courses.catalog');

// Single Course Detail Page
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

// ─────────────────────────────────────────────
// Course Learn Routes (auth + verified, multiple roles)
// Accessible by Students, Instructors, and Admins
// ─────────────────────────────────────────────

Route::middleware(['auth', 'verified', 'role:Student|Super Admin|Instructor'])->group(function () {
    // Course Lesson Player (accessible by all roles for browsing)
    Route::get('/courses/{course:slug}/learn/{lesson}', [\App\Http\Controllers\Student\CourseLearnController::class, 'show'])
        ->name('courses.learn');

    // Mark lesson complete (controller checks enrollment, so only Students are affected)
    Route::post('/courses/{course:slug}/learn/{lesson}/complete', [\App\Http\Controllers\Student\CourseLearnController::class, 'markCompleted'])
        ->name('courses.learn.complete');

    // AI Chatbot APIs
    Route::get('/api/ai-chat', [\App\Http\Controllers\Student\AiChatController::class, 'index'])->name('ai-chat.index');
    Route::post('/api/ai-chat', [\App\Http\Controllers\Student\AiChatController::class, 'store'])->name('ai-chat.store');

    // Signed video stream route
    Route::get('/lesson/video/{media}', function (\Spatie\MediaLibrary\MediaCollections\Models\Media $media) {
        abort_unless(
            \Illuminate\Support\Facades\URL::hasValidSignature(request()),
            403
        );
        return response()->file($media->getPath());
    })->name('lesson.video.stream');

    // Quiz taking routes
    Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Student\QuizTakingController::class, 'show'])
        ->name('quizzes.take');
    Route::post('/quizzes/{quiz}/start', [\App\Http\Controllers\Student\QuizTakingController::class, 'start'])
        ->name('quizzes.start');
    Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\Student\QuizTakingController::class, 'submit'])
        ->name('quizzes.submit');

    // Download certificate
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])
        ->name('certificates.download');

    // Enroll in free course
    Route::post('/courses/{course:slug}/enroll', [CourseController::class, 'enroll'])
        ->name('courses.enroll');

});

Route::middleware(['auth'])->group(function () {
    // Checkout for paid course
    Route::get('/checkout/{course:slug}', [\App\Http\Controllers\Student\CheckoutController::class, 'show'])
        ->name('checkout');
    Route::post('/checkout/{course:slug}', [\App\Http\Controllers\Student\CheckoutController::class, 'process'])
        ->name('checkout.process');
});

// Public verification route for certificates
Route::get('/verify/{uuid}', [CertificateController::class, 'verify'])->name('certificates.verify');

// ─────────────────────────────────────────────
// Instructor Application Routes (auth only - any role)
// ─────────────────────────────────────────────

Route::middleware(['auth'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/apply', [\App\Http\Controllers\Instructor\ApplicationController::class, 'show'])
        ->name('apply');
    Route::post('/apply/basic-info', [\App\Http\Controllers\Instructor\ApplicationController::class, 'saveBasicInfo'])
        ->name('apply.basic-info');
    Route::post('/apply/upload', [\App\Http\Controllers\Instructor\ApplicationController::class, 'uploadDocument'])
        ->name('apply.upload');
    Route::post('/apply/submit', [\App\Http\Controllers\Instructor\ApplicationController::class, 'submit'])
        ->name('apply.submit');
    Route::post('/apply/reapply', [\App\Http\Controllers\Instructor\ApplicationController::class, 'reapply'])
        ->name('apply.reapply');
});

// ─────────────────────────────────────────────
// Admin Routes (Super Admin role)
// ─────────────────────────────────────────────

Route::middleware(['auth', 'role:Super Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Instructor Applications
    Route::get('/instructor-applications', [\App\Http\Controllers\Admin\InstructorApplicationsController::class, 'index'])
        ->name('instructor-applications');
    Route::post('/instructor-applications/{user}/approve', [\App\Http\Controllers\Admin\InstructorApplicationsController::class, 'approve'])
        ->name('instructor-applications.approve');
    Route::post('/instructor-applications/{user}/reject', [\App\Http\Controllers\Admin\InstructorApplicationsController::class, 'reject'])
        ->name('instructor-applications.reject');
});

// ─────────────────────────────────────────────
// Instructor Routes (Instructor role)
// ─────────────────────────────────────────────

Route::middleware(['auth', 'role:Instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', \App\Http\Controllers\Instructor\DashboardController::class)->name('dashboard');
    Route::get('/courses', [\App\Http\Controllers\Instructor\CourseController::class, 'index'])->name('courses');
    Route::get('/courses/create', [\App\Http\Controllers\Instructor\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\Instructor\CourseController::class, 'store'])->name('courses.store');
    Route::post('/courses/{course}/update', [\App\Http\Controllers\Instructor\CourseController::class, 'update'])->name('courses.update');
    
    Route::get('/courses/{course}/builder', function(\App\Models\Course $course) {
        $course->load(['quizzes' => function($q) { $q->where('is_final_exam', true); }]);
        $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail');
        return Inertia::render('instructor/CourseBuilder', [
            'course' => $course,
            'sections' => $course->sections()->with(['lessons', 'quizzes'])->get()
        ]);
    })->name('courses.builder');

    Route::post('/courses/{course}/sections', [\App\Http\Controllers\Instructor\CourseController::class, 'storeSection'])->name('courses.sections.store');
    Route::post('/sections/{section}/lessons', [\App\Http\Controllers\Instructor\CourseController::class, 'storeLesson'])->name('sections.lessons.store');
    
    // Quizzes Management
    Route::post('/sections/{section}/quizzes', [\App\Http\Controllers\Instructor\QuizController::class, 'storeSectionQuiz'])->name('sections.quizzes.store');
    Route::post('/courses/{course}/final-exam', [\App\Http\Controllers\Instructor\QuizController::class, 'storeFinalExam'])->name('courses.final-exam.store');
    
    Route::get('/quizzes/{quiz}/manage', [\App\Http\Controllers\Instructor\QuizController::class, 'manage'])->name('quizzes.manage');
    Route::post('/quizzes/{quiz}/questions', [\App\Http\Controllers\Instructor\QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::post('/quizzes/{quiz}/update-timer', [\App\Http\Controllers\Instructor\QuizController::class, 'updateTimer'])->name('quizzes.update-timer');
    Route::delete('/questions/{question}', [\App\Http\Controllers\Instructor\QuizController::class, 'destroyQuestion'])->name('questions.destroy');
    
    Route::get('/financials', \App\Http\Controllers\Instructor\FinancialController::class)->name('financials');
});

require __DIR__.'/auth.php';
