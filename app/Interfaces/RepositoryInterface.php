<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Repository Contract (Read)
 *
 * All read-only repository interfaces MUST extend this contract.
 * Concrete implementations live in App\Repositories\Eloquent\.
 *
 * Typed generics are documented via PHPDoc for PHPStan/IDE support.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface RepositoryInterface
{
    /**
     * Find a model by its primary key.
     *
     * @return TModel|null
     */
    public function findById(int|string $id): mixed;

    /**
     * Return all records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function all(): mixed;
}
