<?php

namespace App\Enums;

enum DecisionMakingCapabilityEnum: string
{
    use Values;

    case INDEPENDENT = 'independent only';
    case INDEPENDENT_INTERDEPENDENT = 'independent and interdependent only';
    case INTERDEPENDENT = 'interdependent only';
    case NOT_APPLICABLE = 'not applicable';
}
