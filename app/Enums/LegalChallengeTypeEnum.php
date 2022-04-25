<?php

namespace App\Enums;

enum LegalChallengeTypeEnum: string
{
    case FINANCIAL = 'financial and property';
    case HEALTH = 'health care';
    case PERSONAL = 'personal life and care';
}
