<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\EloquentRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract Base Eloquent Repository
 *
 * Provides a reusable implementation of EloquentRepositoryContract.
 * Domain repositories extend this and inject their specific Model class.
 *
 * SOLID: Open/Closed — extend, never modify this base for domain logic.
 *
 * @template TModel of Model
 * @implements EloquentRepositoryContract<TModel>
 */
abstract class BaseRepository implements EloquentRepositoryContract
{
    /**
     * The Eloquent model managed by this repository.
     *
     * @var TModel
     */
    protected readonly Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     *
     * @return TModel|null
     */
    public function findById(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * {@inheritDoc}
     *
     * @return Collection<int, TModel>
     */
    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * {@inheritDoc}
     *
     * @param  array<string, mixed> $attributes
     * @return TModel
     */
    public function create(array $attributes): Model
    {
        return $this->model->newQuery()->create($attributes);
    }

    /**
     * {@inheritDoc}
     *
     * @param  array<string, mixed> $attributes
     * @return TModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int|string $id, array $attributes): Model
    {
        $record = $this->model->newQuery()->findOrFail($id);
        $record->update($attributes);

        return $record->fresh();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int|string $id): bool
    {
        return (bool) $this->model->newQuery()->findOrFail($id)->delete();
    }

    /**
     * {@inheritDoc}
     *
     * @return LengthAwarePaginator<TModel>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage);
    }
}
