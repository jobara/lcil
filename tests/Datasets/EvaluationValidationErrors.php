<?php

use App\Enums\EvaluationAssessments;

dataset('evaluationValidationErrors', function () {
    return [
        'assessment not in EvaluationAssessments' => [
            function () {
                return function (int $provision_id) {
                    return [
                        'evaluations' => [
                            $provision_id => [
                                'assessment' => 'fake',
                                'comment' => 'Test comment',
                                'provision_id' => "{$provision_id}",
                            ],
                        ],
                    ];
                };
            },
            ['evaluations.%s.assessment' => 'The Measure Evaluation must only include the following: '.implode(', ', EvaluationAssessments::values()).'.'],
            ['evaluations.%s.assessment' => 'evaluations%sassessment'],
        ],
        'comment without assessment' => [
            function () {
                return function (int $provision_id) {
                    return [
                        'evaluations' => [
                            $provision_id => [
                                'comment' => 'Test comment',
                                'provision_id' => "{$provision_id}",
                            ],
                        ],
                    ];
                };
            },
            ['evaluations.%s.assessment' => 'The Measure Evaluation field is required when Measure Evaluation Remarks is present.'],
            ['evaluations.%s.assessment' => 'evaluations%sassessment'],
        ],
    ];
});
