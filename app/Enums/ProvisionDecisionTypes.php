<?php

namespace App\Enums;

enum ProvisionDecisionTypes: string
{
    use Values;

    case Personal = 'personal_life_care';
    case Health = 'healthcare';
    case Financial = 'financial_property';

    public static function labels(): array
    {
        return [
            'personal_life_care' => __('Personal Life and Care'),
            'healthcare' => __('Health Care'),
            'financial_property' => __('Financial and Property'),
        ];
    }
}
