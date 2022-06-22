<?php

namespace App\Enums;

enum LawPolicyTypes: string
{
    use Values;

    case CaseLaw = 'case law';
    case Constitutional = 'constitutional';
    case Policy = 'policy';
    case QuasiConstitutional = 'quasi-constitutional';
    case Regulation = 'regulation';
    case Statute = 'statute';
}
