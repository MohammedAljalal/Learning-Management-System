<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Eloquent Repository Contract
 *
 * Extends RepositoryInterface with write operations and pagination.
 * Domain repositories (e.g., CourseRepositoryContract) extend this interface.
 *
 * @template TModel of Model
 * @extends  RepositoryInterface<TModel>
 */
interface EloquentRepositoryContract extends RepositoryInterface
{
    /**
     * Persist a new model to the database.
     *
     * @param  array<string, mixed> $attributes
     * @return TModel
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing model by primary key.
     *
     * @param  array<string, mixed> $attributes
     * @return TModel
     */
    public function update(int|string $id, array $attributes): Model;

    /**
     * Delete a model by primary key.
     */
    public function delete(int|string $id): bool;

    /**
     * Return a paginated result set.
     *
     * @return LengthAwarePaginator<TModel>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
