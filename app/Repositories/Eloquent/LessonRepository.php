<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonRepositoryContract;

class LessonRepository extends BaseRepository implements LessonRepositoryContract
{
    public function __construct(Lesson $model)
    {
        parent::__construct($model);
    }
}
