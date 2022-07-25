<?php

namespace App\Enums;

enum RegimeAssessmentStatuses: string
{
    use Values;

    case Draft = 'draft';
    case NeedsReview = 'needs_review ';
    case Published = 'published';
}
