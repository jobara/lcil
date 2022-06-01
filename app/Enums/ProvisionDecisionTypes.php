<?php

namespace App\Enums;

enum ProvisionDecisionTypes: string
{
    use Values;

    case Financial = 'financial and property';
    case Health = 'health care';
    case Personal = 'personal life and care';
}
