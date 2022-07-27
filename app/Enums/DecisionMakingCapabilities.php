<?php

namespace App\Enums;

enum DecisionMakingCapabilities: string
{
    use Values;

    case Independent = 'independent';
    case Interdependent = 'interdependent';

    public static function labels(): array
    {
        return [
            'independent' => __('Independent'),
            'interdependent' => __('Interdependent'),
        ];
    }
}
