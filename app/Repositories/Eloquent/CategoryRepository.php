<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryContract;

/**
 * Category Repository
 *
 * Eloquent implementation of the CategoryRepositoryContract.
 *
 * @extends BaseRepository<Category>
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryContract
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
