<?php

namespace App\Enums;

enum LawPolicyTypes: string
{
    use Values;

    case Statute = 'statute';
    case Policy = 'policy';
    case Constitutional = 'constitutional';
    case CaseLaw = 'case_law';
    case Regulation = 'regulation';
    case QuasiConstitutional = 'quasi-constitutional';

    public static function labels(): array
    {
        return [
            'statute' => __('Statute'),
            'policy' => __('Policy'),
            'constitutional' => __('Constitutional'),
            'case_law' => __('Case Law'),
            'regulation' => __('Regulation'),
            'quasi-constitutional' => __('Quasi-Constitutional'),
        ];
    }
}
