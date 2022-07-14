<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LawPolicyTypes;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('show route display', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create();

    $response = $this->get(localized_route('lawPolicySources.show', $lawPolicySource));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.show');
    $response->assertViewHas('lawPolicySource');

    expect($response['lawPolicySource'])->toBeInstanceOf(LawPolicySource::class);
})->group('LawPolicySources');

test('show route render - authenticated', function () {
    // create a Law and Policy Source and user to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();
    $user = User::factory()->create();

    $toSee = [
        'Provisions',
        '<a href="' . \localized_route('provisions.create', $lawPolicySource) . '">Add Provision</a>',
    ];

    $view = $this->actingAs($user)->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeInOrder($toSee, false);
})->group('LawPolicySources');

test('show route render - all fields', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'type' => $this->faker->randomElement(LawPolicyTypes::values()),
            'is_core' => $this->faker->boolean(),
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => ucfirst($this->faker->word()),
        ]);

    $provisionConfig = [
        'decision_type' => [ProvisionDecisionTypes::Financial->value],
        'legal_capacity_approach' => $this->faker->randomElement(LegalCapacityApproaches::values()),
        'decision_making_capability' => [DecisionMakingCapabilities::Independent->value],
        'reference' => $this->faker->unique()->url(),
        'court_challenge' => ProvisionCourtChallenges::ResultOf->value,
        'decision_citation' => $this->faker->paragraph(),
    ];

    Provision::factory()
        ->for($lawPolicySource)
        ->create($provisionConfig);

    $toSee = [
        $lawPolicySource->name,
        'Jurisdiction',
        "{$lawPolicySource->municipality}, Ontario, Canada",
        'Year in Effect',
        $lawPolicySource->year_in_effect,
        'Reference',
        $lawPolicySource->reference,
        'Type',
        $lawPolicySource->type->value,
        'Effect on Legal Capacity',
        $lawPolicySource->is_core ? 'Core - directly affects legal capacity' : 'Supplemental - indirectly affects legal capacity',
        'Provisions',
    ];

    $dontSee = [
        'No provisions have been added.',
        'Add Provision',
    ];

    $markupToSee = [
        "href=\"{$lawPolicySource->reference}\"",
    ];

    foreach ($lawPolicySource->provisions as $provision) {
        $toSee[] = "Section / Subsection: {$provision->section}";
        $toSee[] = "Section / Subsection: {$provision->section} Reference";
        $toSee[] = 'Other Information';
        $toSee[] = "{$provision->legal_capacity_approach->value} approach to legal capacity";
        $toSee[] = 'Recognizes Independent Only decision making capability';
        $toSee[] = 'Legal Information';
        $toSee[] = 'This provision is the result of a court challenge.';
        $toSee[] = 'Type of Decision: Financial Property';
        $toSee[] = "Decision Citation: {$provision->decision_citation}";

        $markupToSee[] = $provision->body;
        $markupToSee[] = "href=\"{$provision->reference}\"";
    }

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeTextInOrder($toSee);
    $view->assertSeeInOrder($markupToSee, false);
    assertDontSeeAnyText($view, $dontSee, false);
})->group('LawPolicySources');

test('show route render - minimum fields', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'type' => null,
            'is_core' => null,
            'reference' => null,
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
        ]);

    $dontSee = [
        'Reference',
        'Type',
        'Effect on Legal Capacity',
        'Add Provision',
    ];

    $toSee = [
        $lawPolicySource->name,
        'Jurisdiction',
        'Ontario, Canada',
        'Year in Effect',
        $lawPolicySource->year_in_effect,
        'No provisions have been added.',
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeTextInOrder($toSee);
    assertDontSeeAny($view, $dontSee, false);
})->group('LawPolicySources');

test('show route render - minimum provision fields', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'test policy',
            'type' => null,
            'is_core' => null,
            'reference' => null,
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
        ]);

    Provision::factory(3)
        ->for($lawPolicySource)
        ->create([
            'decision_type' => null,
            'legal_capacity_approach' => null,
            'decision_making_capability' => null,
            'reference' => null,
            'court_challenge' => null,
            'decision_citation' => null,
        ]);

    $dontSee = [
        'Reference',
        'Type',
        'Effect on Legal Capacity',
        'Other Information',
        'Legal Information',
        'No provisions have been added.',
        'Add Provision',
    ];

    $toSee = [
        $lawPolicySource->name,
        'Jurisdiction',
        'Ontario, Canada',
        'Year in Effect',
        $lawPolicySource->year_in_effect,
        'Provisions',
    ];

    foreach ($lawPolicySource->provisions->sortBy('section') as $provision) {
        $toSee[] = "Section / Subsection: {$provision->section}";
        $toSee[] = $provision['body'];
        $dontSee[] = "Section / Subsection: {$provision->section} Reference";
    }

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAnyText($view, $dontSee, false);
})->group('LawPolicySources');

test('show route render - provision order', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 2]);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 'ab 3']);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => '10']);

    $toSee = [
        'Provisions',
        'Section / Subsection: 2',
        'Section / Subsection: 10',
        'Section / Subsection: ab 3',
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeTextInOrder($toSee);
})->group('LawPolicySources');

test('show route render - decision making capabilities', function ($data, $expected) {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();

    Provision::factory()
        ->for($lawPolicySource)
        ->create($data);

    $toSee = [
        'Provisions',
        'Other Information',
        "Recognizes {$expected} decision making capability",
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
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
])
  ->group('LawPolicySources');

test('show route render - court challenges', function ($data, $expected) {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();

    Provision::factory()
        ->for($lawPolicySource)
        ->create($data);

    $toSee = [
        'Provisions',
        'Legal Information',
        $expected,
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);

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
])
  ->group('LawPolicySources');

test('show route render - decision types', function ($data, $expected) {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();

    Provision::factory()
        ->for($lawPolicySource)
        ->create(array_merge(
            ['court_challenge' => ProvisionCourtChallenges::ResultOf->value],
            $data
        ));

    $toSee = [
        'Provisions',
        'Legal Information',
        "Type of Decision: {$expected}",
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeTextInOrder($toSee);
})->with([
    'single decision type' => [
        ['decision_type' => [ProvisionDecisionTypes::Financial->value]],
        'Financial Property',
    ],
    'multiple decision types' => [
        ['decision_type' => ProvisionDecisionTypes::values()],
        'Financial Property, Health Care, Personal Life and Care',
    ],
])
  ->group('LawPolicySources');
