<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\LessonProgress;
use App\Repositories\Contracts\LessonProgressRepositoryContract;

class LessonProgressRepository extends BaseRepository implements LessonProgressRepositoryContract
{
    public function __construct(LessonProgress $model)
    {
        parent::__construct($model);
    }
}
