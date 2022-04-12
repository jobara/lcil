<?php

namespace App\Enums;

enum LegalChallengeTypeEnum: string
{
    case PERSONAL = 'personal life and care';
    case HEALTH = 'health care';
    case FINANCIAL = 'financial and property';
}
