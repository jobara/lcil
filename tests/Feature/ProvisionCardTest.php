<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('render - all fields', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => 12,
            'decision_type' => [ProvisionDecisionTypes::Financial->value],
            'legal_capacity_approach' => LegalCapacityApproaches::Outcome->value,
            'decision_making_capability' => [DecisionMakingCapabilities::Independent->value],
            'reference' => $this->faker->unique()->url(),
            'court_challenge' => ProvisionCourtChallenges::ResultOf->value,
            'decision_citation' => $this->faker->paragraph(),
        ]);

    $toSee = [
        "Section / Subsection: {$provision->section}",
        $provision->body,
        "href=\"{$provision->reference}\"",
        "Section / Subsection: {$provision->section} Reference",
        'Other Information',
        "{$provision->legal_capacity_approach->labels()[$provision->legal_capacity_approach->value]} approach to legal capacity",
        'Recognizes Independent Only decision making capability',
        'Legal Information',
        'This provision is the result of a court challenge.',
        'Type of Decision: Financial Property',
        "Decision Citation: {$provision->decision_citation}",
    ];

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    $view->assertSeeInOrder($toSee, false);
});

test('render - minimum fields', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => 12,
            'decision_type' => null,
            'legal_capacity_approach' => null,
            'decision_making_capability' => null,
            'reference' => null,
            'court_challenge' => null,
            'decision_citation' => null,
        ]);

    $toSee = [
        "Section / Subsection: {$provision->section}",
        $provision->body,
    ];

    $dontSee = [
        "href=\"{$provision->reference}\"",
        "Section / Subsection: {$provision->section} Reference",
        'Other Information',
        'approach to legal capacity',
        'Recognizes Independent Only decision making capability',
        'Legal Information',
        'This provision is the result of a court challenge.',
        'Type of Decision: Financial Property',
        'Decision Citation:',
    ];

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAnyText($view, $dontSee, false);
});

test('render - decision making capabilities', function ($data, $expected) {
    $lawPolicySource = LawPolicySource::factory()->create();

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create($data);

    $toSee = [
        'Other Information',
        "Recognizes {$expected} decision making capability",
    ];

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    $view->assertSeeTextInOrder($toSee);
})->with([
    'independent' => [
        ['decision_making_capability' => [DecisionMakingCapabilities::Independent->value]],
        'Independent Only',
    ],
    'interdependent' => [
        ['decision_making_capability' => [DecisionMakingCapabilities::Interdependent->value]],
        'Interdependent Only',
    ],
    'independent and interdependent' => [
        ['decision_making_capability' => [DecisionMakingCapabilities::Independent->value, DecisionMakingCapabilities::Interdependent->value]],
        'Independent and Interdependent',
    ],
]);

test('render - court challenges', function ($data, $expected) {
    $lawPolicySource = LawPolicySource::factory()->create();

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create($data);

    $toSee = [
        'Legal Information',
        $expected,
    ];

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    if (isset($expected)) {
        $view->assertSeeTextInOrder($toSee);
    } else {
        $view->assertDontSeeText('Legal Information');
    }
})->with([
    'not related' => [
        ['court_challenge' => ProvisionCourtChallenges::NotRelated->value],
        null,
    ],
    'result of' => [
        ['court_challenge' => ProvisionCourtChallenges::ResultOf->value],
        'This provision is the result of a court challenge.',
    ],
    'subject to' => [
        ['court_challenge' => ProvisionCourtChallenges::SubjectTo->value],
        'This provision is, or has been, subject to a constitutional or other court challenge.',
    ],
]);

test('render - decision types', function ($data, $expected) {
    $lawPolicySource = LawPolicySource::factory()->create();

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create(array_merge(
            ['court_challenge' => ProvisionCourtChallenges::ResultOf->value],
            $data
        ));

    $toSee = [
        'Legal Information',
        "Type of Decision: {$expected}",
    ];

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    $view->assertSeeTextInOrder($toSee);
})->with([
    'single decision type' => [
        ['decision_type' => [ProvisionDecisionTypes::Financial->value]],
        'Financial Property',
    ],
    'multiple decision types' => [
        ['decision_type' => ProvisionDecisionTypes::values()],
        'Personal Life and Care, Health Care, Financial Property',
    ],
]);

test('show route render - Legal capacity approach', function ($data, $expected) {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create(array_merge([
            'decision_making_capability' => null,
        ], $data));

    $toSee = [];

    if (isset($expected)) {
        $toSee[] = 'Other Information';
    }

    $toSee = [
        'Legal Information',
        "Type of Decision: {$expected}",
    ];

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    if (isset($expected)) {
        $toSee = [
            'Other Information',
            $expected,
        ];

        $view->assertSeeTextInOrder($toSee);
    }

    if (empty($expected)) {
        $view->assertDontSee('Other Information');
    }
})->with([
    'has an approach' => [
        ['legal_capacity_approach' => LegalCapacityApproaches::Outcome->value],
        'Outcome approach to legal capacity',
    ],
    'not applicable approach' => [
        ['legal_capacity_approach' => LegalCapacityApproaches::NotApplicable->value],
        null,
    ],
    'null approach' => [
        ['legal_capacity_approach' => null],
        null,
    ],
]);

test('render - authenticated', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create();

    $view = $this->actingAs($user)->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    $view->assertSee('<a href="'.\localized_route('provisions.edit', [$lawPolicySource, $provision->slug]).'">Edit</a>', false);
});

test('render - unauthenticated', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create();

    $view = $this->blade(
        '<x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]
    );

    $view->assertDontSee('<a href="'.\localized_route('provisions.edit', [$lawPolicySource, $provision->slug]).'">Edit</a>', false);
});
