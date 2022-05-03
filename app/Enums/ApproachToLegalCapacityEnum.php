<?php

namespace App\Enums;

enum ApproachToLegalCapacityEnum: string
{
    use Values;

    case DECISION_MAKING_CAPABILITY = 'decision-making capability';
    case NOT_APPLICABLE = 'not applicable';
    case OUTCOME = 'outcome';
    case OUTCOME_COGNITIVE = 'outcome/cognitive';
    case STATUS = 'status';
    case STATUS_COGNITIVE = 'status/cognitive';
    case STATUS_OUTCOME = 'status/outcome';
}
