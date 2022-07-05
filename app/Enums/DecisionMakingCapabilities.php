<?php

namespace App\Enums;

enum DecisionMakingCapabilities: string
{
    use Values;

    case Independent = 'independent';
    case Interdependent = 'interdependent';
}
