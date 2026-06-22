<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * HasUuid Trait
 *
 * Automatically generates a UUID v4 for the model's primary key
 * before creation. Suitable for models that use UUID as their PK.
 *
 * Usage:
 *   class Course extends Model
 *   {
 *       use HasUuid;
 *
 *       protected $keyType = 'string';
 *       public $incrementing = false;
 *   }
 */
trait HasUuid
{
    /**
     * Boot the trait and attach the creating model event listener.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(static function (self $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * The primary key is not auto-incremented.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * The primary key is a string (UUID).
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
