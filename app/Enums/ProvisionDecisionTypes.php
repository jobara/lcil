<?php

namespace App\Enums;

enum ProvisionDecisionTypes: string
{
    use Values;

    case Financial = 'financial_property';
    case Health = 'healthcare';
    case Personal = 'personal_life_care';
}
