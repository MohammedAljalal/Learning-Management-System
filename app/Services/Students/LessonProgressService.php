<?php

declare(strict_types=1);

namespace App\Services\Students;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Repositories\Contracts\LessonProgressRepositoryContract;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class LessonProgressService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly LessonProgressRepositoryContract $progressRepository
    ) {
        parent::__construct($logger);
    }

    /**
     * Save (upsert) the video playback position for a user on a lesson.
     * Called periodically from the Livewire CoursePlayer component.
     */
    public function savePosition(int $userId, int $lessonId, int $positionSeconds): LessonProgress
    {
        return DB::transaction(function () use ($userId, $lessonId, $positionSeconds) {
            $progress = LessonProgress::updateOrCreate(
                ['user_id' => $userId, 'lesson_id' => $lessonId],
                ['last_position_seconds' => $positionSeconds]
            );

            $this->logger->info('Lesson progress saved.', [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'position' => $positionSeconds,
            ]);

            return $progress;
        });
    }

    /**
     * Mark a lesson as fully completed for a user.
     */
    public function markCompleted(int $userId, int $lessonId): LessonProgress
    {
        return DB::transaction(function () use ($userId, $lessonId) {
            $progress = LessonProgress::updateOrCreate(
                ['user_id' => $userId, 'lesson_id' => $lessonId],
                ['completed' => true]
            );

            $this->logger->info('Lesson marked as completed.', [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ]);

            return $progress;
        });
    }

    /**
     * Retrieve the saved progress position for a user on a lesson.
     */
    public function getPosition(int $userId, int $lessonId): int
    {
        $progress = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        return $progress?->last_position_seconds ?? 0;
    }
}
