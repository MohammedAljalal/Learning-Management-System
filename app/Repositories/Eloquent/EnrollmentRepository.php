<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryContract;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryContract
{
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }
}
