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

test('render create', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $toSee = [
        '<form',
        'method="POST"',
        'action="' . route('provisions.store', $lawPolicySource),
        '<label id="section-label" for="section">Section or Subsection (required)</label>',
        '<input',
        'name="section" id="section" type="text"',
        'required',
        '<label id="body-label" for="body">Provision Text (required)</label>',
        'id="body-editor"',
        '\'aria-required\': true',
        '\'aria-labelledby\': \'body-label\'',
        '\'id\': \'body-editable\'',
        'id="body-toolbar"',
        'aria-labelledby="body-label"',
        '<input',
        'name="body" id="body" type="hidden" x-model="content"',
        'required',
        '<label id="reference-label" for="reference">Reference / Link</label>',
        '<input',
        'name="reference" id="reference" type="url"',
        'aria-describedby="reference-hint"',
        '<p class="field__hint" id="reference-hint">',
        'Web link or URL to source. Example: https://www.example.com/',
        '<h2>Additional Information</h2>',
        '<label id="legal_capacity_approach-label" for="legal_capacity_approach">Approach to Legal Capacity</label>',
        '<select',
        'name="legal_capacity_approach" id="legal_capacity_approach"',
        '<option value="" selected></option>',
        '<option value="status" >Status</option>',
        '<option value="outcome" >Outcome</option>',
        '<option value="cognitive" >Cognitive</option>',
        '<option value="decision-making_capability" >Decision-making capability</option>',
        '<option value="status/outcome" >Status/Outcome</option>',
        '<option value="status/cognitive" >Status/Cognitive</option>',
        '<option value="outcome/cognitive" >Outcome/Cognitive</option>',
        '<option value="not_applicable" >Not applicable</option>',
        '<legend id="decision_making_capability-label">How does this provision recognize decision making capability? Check all that apply.</legend>',
        '<input  type="checkbox" name="decision_making_capability[]" id="decision_making_capability-independent" value="independent"',
        '<label for="decision_making_capability-independent">',
        'Independent',
        '<input  type="checkbox" name="decision_making_capability[]" id="decision_making_capability-interdependent" value="interdependent"',
        '<label for="decision_making_capability-interdependent">',
        'Interdependent',
        '<h2>Legal Information</h2>',
        '<ul x-data="{',
        'courtChallenge: \'\',',
        'get hasChallenge() { return this.courtChallenge && this.courtChallenge !== \'not_related\' },',
        '<legend id="court_challenge-label">Court Challenge Details. Choose the option that best describes this provision.</legend>',
        '<input x-model="courtChallenge" type="radio" name="court_challenge" id="court_challenge-not-related" value="not_related"',
        '<label for="court_challenge-not-related">',
        'Not related to a court challenge',
        '<input x-model="courtChallenge" type="radio" name="court_challenge" id="court_challenge-subject-to" value="subject_to"',
        '<label for="court_challenge-subject-to">',
        'Is or has been subject to a constitutional or other court challenge',
        '<input x-model="courtChallenge" type="radio" name="court_challenge" id="court_challenge-result-of" value="result_of"',
        '<label for="court_challenge-result-of">',
        'Is the result of a court challenge',
        '<fieldset x-bind:disabled="!hasChallenge">',
        '<legend id="decision_type-label">Type of Decision</legend>',
        '<input  type="checkbox" name="decision_type[]" id="decision_type-personal-life-care" value="personal_life_care"',
        '<label for="decision_type-personal-life-care">',
        'Personal Life and Care',
        '<input  type="checkbox" name="decision_type[]" id="decision_type-healthcare" value="healthcare"',
        '<label for="decision_type-healthcare">',
        'Health Care',
        '<input  type="checkbox" name="decision_type[]" id="decision_type-financial-property" value="financial_property"',
        '<label for="decision_type-financial-property">',
        'Financial and Property',
        '<label id="decision_citation-label" for="decision_citation">Decision Citation</label>',
        '<textarea',
        'name="decision_citation" id="decision_citation" x-bind:disabled="!hasChallenge"',

        '<a href="' . \localized_route('lawPolicySources.show', $lawPolicySource) . '">Cancel</a>',
        '<button type="submit">Submit</button>',
    ];

    $view = $this->withViewErrors([])
                 ->blade(
                     '<x-forms.provision :lawPolicySource="$lawPolicySource" />',
                     ['lawPolicySource' => $lawPolicySource]
                 );

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<input type="hidden" name="_method" value="patch">', false);
});

test('render edit', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create([
        'section' => '1.2.3',
        'decision_type' => [ProvisionDecisionTypes::Financial->value],
        'legal_capacity_approach' => LegalCapacityApproaches::Status->value,
        'decision_making_capability' => [DecisionMakingCapabilities::Independent->value],
        'reference' => $this->faker->unique()->url(),
        'court_challenge' => ProvisionCourtChallenges::SubjectTo->value,
        'decision_citation' => $this->faker->paragraph(),
    ]);

    $toSee = [
        '<form',
        'method="POST"',
        'action="' . route('provisions.update', ['lawPolicySource' => $lawPolicySource, 'slug' => $provision->slug]),
        '<input type="hidden" name="_method" value="patch">',
        '<input',
        'name="section" id="section" type="text" value="' . $provision->section,
        'id="body-editor"',
        'x-data="editor(\'' . $provision->body,
        '<input',
        'name="reference" id="reference" type="url"',
        'value="' . $provision->reference,
        '<label id="legal_capacity_approach-label" for="legal_capacity_approach">Approach to Legal Capacity</label>',
        '<select',
        'name="legal_capacity_approach" id="legal_capacity_approach"',
        '<option value="' . $provision->legal_capacity_approach->value . '" selected>',
        '<input  type="checkbox" name="decision_making_capability[]"',
        'value="' . $provision->decision_making_capability[0] . '"  checked  />',
        '<ul x-data="{',
        "courtChallenge: '{$provision->court_challenge->value}'",
        'value="' . $provision->court_challenge->value . '"  checked  />',
        'name="decision_citation" id="decision_citation"',
        "{$provision->decision_citation}</textarea>",
    ];

    $view = $this->withViewErrors([])
                 ->blade(
                     '<x-forms.provision :lawPolicySource="$lawPolicySource" :provision="$provision" />',
                     ['lawPolicySource' => $lawPolicySource, 'provision' => $provision]
                 );

    $view->assertSeeInOrder($toSee, false);
});

test('render errors', function ($data, $errors, $anchors = [], $isAlpineComponent = false) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);

    $toSee = [];

    foreach ($errors as $key => $message) {
        $id = $anchors[$key] ?? $key;

        if ($isAlpineComponent) {
            $toSee[] = "'aria-describedby': '{$key}-error'";
            $toSee[] = '\'aria-invalid\': true';
            $toSee[] = '\'id\': \'body-editable\'';
        }

        if (! $isAlpineComponent) {
            $toSee[] = "id=\"{$id}";
            $toSee[] = 'aria-describedby';
            $toSee[] = "{$key}-error";
            $toSee[] = 'aria-invalid="true"';
        }

        $toSee[] = "<p class=\"field__error\" id=\"{$key}-error\">";
        $toSee[] = $message;
    }

    $view = $this->withViewErrors($errors)
        ->blade(
            '<x-forms.provision :lawPolicySource="$lawPolicySource" />',
            ['lawPolicySource' => $lawPolicySource],
        );

    $view->assertSeeInOrder($toSee, false);
})->with('provisionValidationErrors')
->group('Provisions', 'LawPolicySources');
