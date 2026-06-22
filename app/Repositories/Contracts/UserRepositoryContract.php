<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * User Repository Contract
 *
 * Defines domain-specific read/write operations for Users.
 *
 * @extends EloquentRepositoryContract<User>
 */
interface UserRepositoryContract extends EloquentRepositoryContract
{
    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): ?User;
}
