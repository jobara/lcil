<?php

namespace App\Enums;

enum RegimeAssessmentStatuses: string
{
    use Values;

    case Draft = 'draft';
    case NeedsReview = 'needs_review';
    case Published = 'published';

    public static function labels(): array
    {
        return [
            'draft' => __('Draft'),
            'needs_review' => __('Needs Review'),
            'published' => __('Published'),
        ];
    }
}
