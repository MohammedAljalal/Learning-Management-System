<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Section;
use App\Repositories\Contracts\SectionRepositoryContract;

class SectionRepository extends BaseRepository implements SectionRepositoryContract
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }
}
