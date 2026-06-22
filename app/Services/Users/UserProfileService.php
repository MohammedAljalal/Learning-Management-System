<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * User Profile Service
 *
 * Handles operations related to the user's profile, including
 * updating profile information, changing passwords, and uploading avatars.
 */
class UserProfileService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly UserRepositoryContract $userRepository
    ) {
        parent::__construct($logger);
    }

    /**
     * Update user profile information.
     *
     * @param array<string, mixed> $data
     * @throws Throwable
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user = $this->userRepository->update($user->id, $data);
            $this->logger->info("User profile updated.", ['user_id' => $user->id]);
            return $user;
        });
    }

    /**
     * Update user password.
     *
     * @throws Throwable
     */
    public function updatePassword(User $user, string $newPassword): User
    {
        return DB::transaction(function () use ($user, $newPassword) {
            $user = $this->userRepository->update($user->id, [
                'password' => Hash::make($newPassword)
            ]);
            $this->logger->info("User password updated.", ['user_id' => $user->id]);
            return $user;
        });
    }

    /**
     * Upload and update user avatar.
     *
     * @throws Throwable
     */
    public function updateAvatar(User $user, UploadedFile $file): User
    {
        return DB::transaction(function () use ($user, $file) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $file->store('avatars', 'public');
            
            if (!$path) {
                throw new \RuntimeException('Failed to store avatar file.');
            }

            $user = $this->userRepository->update($user->id, [
                'avatar' => $path
            ]);

            $this->logger->info("User avatar updated.", ['user_id' => $user->id]);
            
            return $user;
        });
    }
}
