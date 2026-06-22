<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Course Difficulty Enum
 *
 * Defines the difficulty level of a Course.
 */
enum CourseDifficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Expert = 'expert';
}
