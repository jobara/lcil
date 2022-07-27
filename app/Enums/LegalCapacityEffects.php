<?php

namespace App\Enums;

enum LegalCapacityEffects: string
{
    use Values;

    case Core = '1';
    case Supplemental = '0';

    public static function labels(): array
    {
        return [
            '1' => __('Core - directly affects legal capacity'),
            '0' => __('Supplemental - indirectly affects legal capacity'),
        ];
    }
}
