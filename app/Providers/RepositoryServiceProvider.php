<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryContract::class,
            \App\Repositories\Eloquent\CategoryRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\CourseRepositoryContract::class,
            \App\Repositories\Eloquent\CourseRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\EnrollmentRepositoryContract::class,
            \App\Repositories\Eloquent\EnrollmentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LessonProgressRepositoryContract::class,
            \App\Repositories\Eloquent\LessonProgressRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LessonRepositoryContract::class,
            \App\Repositories\Eloquent\LessonRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\SectionRepositoryContract::class,
            \App\Repositories\Eloquent\SectionRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryContract::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
