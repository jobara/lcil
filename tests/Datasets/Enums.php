<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\EvaluationAssessments;
use App\Enums\LawPolicyTypes;
use App\Enums\LegalCapacityApproaches;
use App\Enums\LegalCapacityEffects;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use App\Enums\RegimeAssessmentStatuses;

dataset('enums', function () {
    return [
        'DecisionMakingCapabilities' => [
            DecisionMakingCapabilities::class,
            [
                'independent' => 'Independent',
                'interdependent' => 'Interdependent',
            ],
        ],
        'EvaluationAssessments' => [
            EvaluationAssessments::class,
            [
                'fully' => 'Fully',
                'partially' => 'Partially',
                'not_at_all' => 'Not at all',
            ],
        ],
        'LawPolicyTypes' => [
            LawPolicyTypes::class,
            [
                'statute' => 'Statute',
                'policy' => 'Policy',
                'constitutional' => 'Constitutional',
                'case_law' => 'Case Law',
                'regulation' => 'Regulation',
                'quasi-constitutional' => 'Quasi-Constitutional',
            ],
        ],
        'LegalCapacityApproaches' => [
            LegalCapacityApproaches::class,
            [
                'status' => 'Status',
                'outcome' => 'Outcome',
                'cognitive' => 'Cognitive',
                'decision-making_capability' => 'Decision-making capability',
                'status/outcome' => 'Status/Outcome',
                'status/cognitive' => 'Status/Cognitive',
                'outcome/cognitive' => 'Outcome/Cognitive',
                'not_applicable' => 'Not applicable',
            ],
        ],
        'LegalCapacityEffects' => [
            LegalCapacityEffects::class,
            [
                '1' => 'Core - directly affects legal capacity',
                '0' => 'Supplemental - indirectly affects legal capacity',
            ],
        ],
        'ProvisionCourtChallenges' => [
            ProvisionCourtChallenges::class,
            [
                'not_related' => 'Not related to a court challenge',
                'subject_to' => 'Is or has been subject to a constitutional or other court challenge',
                'result_of' => 'Is the result of a court challenge',
            ]
        ],
        'ProvisionDecisionTypes' => [
            ProvisionDecisionTypes::class,
            [
                'personal_life_care' => 'Personal Life and Care',
                'healthcare' => 'Health Care',
                'financial_property' => 'Financial and Property',
            ]
        ],
        'RegimeAssessmentStatuses' => [
            RegimeAssessmentStatuses::class,
            [
                'draft' => 'Draft',
                'needs_review' => 'Needs Review',
                'published' => 'Published',
            ]
        ],
    ];
});
