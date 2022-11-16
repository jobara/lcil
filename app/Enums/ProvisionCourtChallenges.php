<?php

namespace App\Enums;

enum ProvisionCourtChallenges: string
{
    use Values;

    case NotRelated = 'not_related';
    case SubjectTo = 'subject_to';
    case ResultOf = 'result_of';

    public static function labels(): array
    {
        return [
            'not_related' => __('Not related to a court challenge'),
            'subject_to' => __('Is or has been subject to a constitutional or other court challenge'),
            'result_of' => __('Is the result of a court challenge'),
        ];
    }
}
