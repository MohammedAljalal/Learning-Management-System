<?php

declare(strict_types=1);

namespace App\Services\Courses;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryContract;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Throwable;

class CourseService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly CourseRepositoryContract $courseRepository
    ) {
        parent::__construct($logger);
    }

    /**
     * Create a new course.
     *
     * @throws Throwable
     */
    public function createCourse(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
            
            $course = $this->courseRepository->create($data);
            
            $this->logger->info("Course created.", ['course_id' => $course->id]);
            
            return $course;
        });
    }

    /**
     * Update an existing course.
     *
     * @throws Throwable
     */
    public function updateCourse(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            if (isset($data['title']) && $data['title'] !== $course->title) {
                $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
            }

            $updatedCourse = $this->courseRepository->update($course->id, $data);
            
            $this->logger->info("Course updated.", ['course_id' => $updatedCourse->id]);
            
            return $updatedCourse;
        });
    }

    /**
     * Change the status of a course.
     *
     * @throws Throwable
     */
    public function changeStatus(Course $course, CourseStatus $status): Course
    {
        return DB::transaction(function () use ($course, $status) {
            $updatedCourse = $this->courseRepository->update($course->id, [
                'status' => $status->value
            ]);
            
            $this->logger->info("Course status changed.", [
                'course_id' => $updatedCourse->id,
                'status' => $status->value
            ]);
            
            return $updatedCourse;
        });
    }

    /**
     * Delete a course.
     *
     * @throws Throwable
     */
    public function deleteCourse(Course $course): void
    {
        DB::transaction(function () use ($course) {
            $this->courseRepository->delete($course->id);
            $this->logger->info("Course deleted.", ['course_id' => $course->id]);
        });
    }
}
