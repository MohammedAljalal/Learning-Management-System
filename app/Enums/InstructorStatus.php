<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Instructor Status Enum
 *
 * Defines the verification status of a user attempting to become an instructor.
 * Backed by strings for direct database storage.
 */
enum InstructorStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
