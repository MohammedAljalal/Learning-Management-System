<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;

/**
 * User Repository
 *
 * Eloquent implementation of the UserRepositoryContract.
 *
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository implements UserRepositoryContract
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }
}
