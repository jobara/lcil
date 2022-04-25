<?php

namespace App\Enums;

enum LawPolicyTypeEnum: string
{
    case CASE_LAW = 'case law';
    case CONSTITUTIONAL = 'constitutional';
    case POLICY = 'policy';
    case QUASI_CONSTITUTIONAL = 'quasi-constitutional';
    case REGULATION = 'regulation';
}
