<?php

namespace App\Enums;

enum LawPolicyTypeEnum: string
{
    case POLICY = 'policy';
    case CONSTITUTIONAL = 'constitutional';
    case CASE_LAW = 'case law';
    case REGULATION = 'regulation';
    case QUASI_CONSTITUTIONAL = 'quasi-constitutional';
}
