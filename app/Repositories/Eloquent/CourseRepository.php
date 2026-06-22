<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryContract;

/**
 * Course Repository
 *
 * @extends BaseRepository<Course>
 */
class CourseRepository extends BaseRepository implements CourseRepositoryContract
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }
}
