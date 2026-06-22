<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Enums\InstructorStatus;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Instructor Verification Service
 *
 * Handles the logic for instructor verification requests and status updates.
 */
class InstructorVerificationService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly UserRepositoryContract $userRepository
    ) {
        parent::__construct($logger);
    }

    /**
     * Update the instructor verification status for a user.
     *
     * @throws Throwable
     */
    public function updateStatus(User $user, InstructorStatus $status): User
    {
        return DB::transaction(function () use ($user, $status) {
            $user = $this->userRepository->update($user->id, [
                'instructor_status' => $status->value
            ]);

            if ($status === InstructorStatus::Approved) {
                // Ensure the user has the instructor role if approved
                if (!$user->hasRole('Instructor')) {
                    $user->assignRole('Instructor');
                }
            } elseif ($status === InstructorStatus::Rejected) {
                // Optionally remove the role if rejected and they had it previously
                if ($user->hasRole('Instructor')) {
                    $user->removeRole('Instructor');
                }
            }

            $this->logger->info("Instructor status updated.", [
                'user_id' => $user->id,
                'status' => $status->value
            ]);

            return $user;
        });
    }
}
