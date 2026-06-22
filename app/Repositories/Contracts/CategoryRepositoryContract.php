<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;

/**
 * Category Repository Contract
 *
 * Defines domain-specific read/write operations for Categories.
 *
 * @extends EloquentRepositoryContract<Category>
 */
interface CategoryRepositoryContract extends EloquentRepositoryContract
{
    //
}
