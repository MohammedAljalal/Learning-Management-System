<?php

declare(strict_types=1);

namespace App\Services\Courses;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonRepositoryContract;
use App\Services\MediaService;
use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

class LessonService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly LessonRepositoryContract $lessonRepository,
        private readonly MediaService $mediaService
    ) {
        parent::__construct($logger);
    }

    /**
     * Create a new lesson, optionally attaching a video.
     *
     * @throws Throwable
     */
    public function createLesson(array $data, ?UploadedFile $video = null): Lesson
    {
        return DB::transaction(function () use ($data, $video) {
            $lesson = $this->lessonRepository->create($data);
            
            if ($video) {
                $this->mediaService->attachMedia($lesson, $video, 'lesson_video');
            }

            $this->logger->info("Lesson created.", ['lesson_id' => $lesson->id]);
            return $lesson;
        });
    }

    /**
     * Update an existing lesson, optionally updating the video.
     *
     * @throws Throwable
     */
    public function updateLesson(Lesson $lesson, array $data, ?UploadedFile $video = null): Lesson
    {
        return DB::transaction(function () use ($lesson, $data, $video) {
            $updatedLesson = $this->lessonRepository->update($lesson->id, $data);
            
            if ($video) {
                $this->mediaService->syncMedia($updatedLesson, $video, 'lesson_video');
            }

            $this->logger->info("Lesson updated.", ['lesson_id' => $updatedLesson->id]);
            return $updatedLesson;
        });
    }

    /**
     * Delete a lesson.
     *
     * @throws Throwable
     */
    public function deleteLesson(Lesson $lesson): void
    {
        DB::transaction(function () use ($lesson) {
            $this->lessonRepository->delete($lesson->id);
            $this->logger->info("Lesson deleted.", ['lesson_id' => $lesson->id]);
        });
    }
}
