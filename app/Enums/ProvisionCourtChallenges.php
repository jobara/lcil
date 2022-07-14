<?php

namespace App\Enums;

enum ProvisionCourtChallenges: string
{
    use Values;

    case NotRelated = 'not_related';
    case ResultOf = 'result_of';
    case SubjectTo = 'subject_to';
}
