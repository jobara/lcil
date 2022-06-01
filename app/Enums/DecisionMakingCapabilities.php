<?php

namespace App\Enums;

enum DecisionMakingCapabilities: string
{
    use Values;

    case Independent = 'independent only';
    case IndependentAndInterdependent = 'independent and interdependent only';
    case Interdependent = 'interdependent only';
    case NotApplicable = 'not applicable';
}
