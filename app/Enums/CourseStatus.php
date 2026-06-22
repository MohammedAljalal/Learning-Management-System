<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Course Status Enum
 *
 * Defines the lifecycle state of a Course.
 */
enum CourseStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Published = 'published';
}
