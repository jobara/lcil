<?php

namespace App\Enums;

enum LegalCapacityApproaches: string
{
    use Values;

    case Status = 'status';
    case Outcome = 'outcome';
    case Cognitive = 'cognitive';
    case DecisionMakingCapability = 'decision-making capability';
    case StatusOutcome = 'status/outcome';
    case StatusCognitive = 'status/cognitive';
    case OutcomeCognitive = 'outcome/cognitive';
    case NotApplicable = 'not applicable';
}
