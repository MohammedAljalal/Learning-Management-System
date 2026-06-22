<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\App;

/**
 * Locale Helper
 *
 * Provides utility functions for locale and RTL detection across the LMS.
 * Used in Blade layouts, middleware, and service classes.
 */
final class LocaleHelper
{
    /**
     * Languages that are written right-to-left.
     *
     * @var list<string>
     */
    private const RTL_LOCALES = ['ar', 'he', 'fa', 'ur'];

    /**
     * Determine whether the active application locale is RTL.
     */
    public static function isRtl(): bool
    {
        return in_array(App::getLocale(), self::RTL_LOCALES, strict: true);
    }

    /**
     * Return the HTML dir attribute value for the active locale.
     *
     * @return 'rtl'|'ltr'
     */
    public static function direction(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }

    /**
     * Return the IETF BCP 47 language tag for the <html lang=""> attribute.
     */
    public static function htmlLang(): string
    {
        return str_replace('_', '-', App::getLocale());
    }

    /**
     * Return the list of all supported RTL locales.
     *
     * @return list<string>
     */
    public static function rtlLocales(): array
    {
        return self::RTL_LOCALES;
    }
}
