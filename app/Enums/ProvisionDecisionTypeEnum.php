<?php

namespace App\Enums;

enum ProvisionDecisionTypeEnum: string
{
    use Values;

    case FINANCIAL = 'financial and property';
    case HEALTH = 'health care';
    case PERSONAL = 'personal life and care';
}
