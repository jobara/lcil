<?php

namespace App\Enums;

use Spatie\LaravelOptions\Options;

trait Values
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): Options
    {
        return Options::forEnum(self::class);
    }
}
