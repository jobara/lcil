<?php

namespace App\Enums;

enum LegalCapacityApproaches: string
{
    use Values;

    case Status = 'status';
    case Outcome = 'outcome';
    case Cognitive = 'cognitive';
    case DecisionMakingCapability = 'decision-making_capability';
    case StatusOutcome = 'status/outcome';
    case StatusCognitive = 'status/cognitive';
    case OutcomeCognitive = 'outcome/cognitive';
    case NotApplicable = 'not_applicable';

    public static function labels(): array
    {
        return [
            'status' => __('Status'),
            'outcome' => __('Outcome'),
            'cognitive' => __('Cognitive'),
            'decision-making_capability' => __('Decision-making capability'),
            'status/outcome' => __('Status/Outcome'),
            'status/cognitive' => __('Status/Cognitive'),
            'outcome/cognitive' => __('Outcome/Cognitive'),
            'not_applicable' => __('Not applicable'),
        ];
    }
}
