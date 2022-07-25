<?php

namespace App\Enums;

enum EvaluationAssessments: string
{
    use Values;

    case Fully = 'fully';
    case Partially = 'partially ';
    case NotAtAll = 'not_at_all';
}
