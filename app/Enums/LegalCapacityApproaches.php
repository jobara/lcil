<?php

namespace App\Enums;

enum LegalCapacityApproaches: string
{
    use Values;

    case DecisionMakingCapability = 'decision-making capability';
    case NotApplicable = 'not applicable';
    case Outcome = 'outcome';
    case OutcomeCognitive = 'outcome/cognitive';
    case Status = 'status';
    case StatusCognitive = 'status/cognitive';
    case StatusOutcome = 'status/outcome';
}
