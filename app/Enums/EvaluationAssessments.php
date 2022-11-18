<?php

namespace App\Enums;

enum EvaluationAssessments: string
{
    use Values;

    case Fully = 'fully';
    case Mostly = 'mostly';
    case Somewhat = 'somewhat';
    case NotAtAll = 'not_at_all';

    public static function labels(): array
    {
        return [
            'fully' => __('Fully'),
            'mostly' => __('Mostly'),
            'somewhat' => __('Somewhat'),
            'not_at_all' => __('Not at all'),
        ];
    }
}
