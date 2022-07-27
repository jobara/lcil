<?php

namespace App\Enums;

enum EvaluationAssessments: string
{
    use Values;

    case Fully = 'fully';
    case Partially = 'partially';
    case NotAtAll = 'not_at_all';

    public static function labels(): array
    {
        return [
            'fully' => __('Fully'),
            'partially' => __('Partially'),
            'not_at_all' => __('Not at all'),
        ];
    }
}
