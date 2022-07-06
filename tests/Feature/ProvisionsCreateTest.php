<?php

use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create route display', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();

    $response = $this->actingAs($user)->get(\localized_route('provisions.create', $lawPolicySource));

    $response->assertStatus(200);
    $response->assertViewIs('provisions.create');
})->group('Provisions', 'LawPolicySources');

test('create route render', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);

    $toSee = [
        'Law and Policy Sources',
        $lawPolicySource->name,
        'Add Provision',
        '<h1 itemprop="name">Add Provision</h1>',
        '<form',
        'method="POST"',
        'action="' . route('provisions.store', $lawPolicySource),

        '<label id="section-label" for="section">Section or Subsection (required)</label>',
        '<input',
        'name="section" id="section" type="text"',
        'required',
        '<label id="body-label" for="body">Provision Text (required)</label>',
        '<textarea',
        'name="body" id="body"',
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
        '<option value="decision-making capability" >Decision-Making Capability</option>',
        '<option value="status/outcome" >Status/Outcome</option>',
        '<option value="status/cognitive" >Status/Cognitive</option>',
        '<option value="outcome/cognitive" >Outcome/Cognitive</option>',
        '<option value="not applicable" >Not Applicable</option>',
        '<legend id="decision_making_capability-label">How does this provision recognize decision making capability? Check all that apply.</legend>',
        '<input  type="checkbox" name="decision_making_capability[]" id="decision_making_capability-independent" value="independent"',
        '<label for="decision_making_capability-independent">',
        'Independent',
        '<input  type="checkbox" name="decision_making_capability[]" id="decision_making_capability-interdependent" value="interdependent"',
        '<label for="decision_making_capability-interdependent">',
        'Interdependent',
        '<h2>Legal Information</h2>',
        '<legend id="court_challenge-label">Court Challenge Details. Choose the option that best describes this provision.</legend>',
        '<input  type="radio" name="court_challenge" id="court_challenge-not_related" value="not_related"',
        '<label for="court_challenge-not_related">',
        'Not related to a court challenge.',
        '<input  type="radio" name="court_challenge" id="court_challenge-result_of" value="result_of"',
        '<label for="court_challenge-result_of">',
        'Is or has been subject to a constitutional or other court challenge',
        '<input  type="radio" name="court_challenge" id="court_challenge-subject_to" value="subject_to"',
        '<label for="court_challenge-subject_to">',
        'Is the result of a court challenge.',
        '<legend id="decision_type-label">Type of Decision</legend>',
        '<input  type="checkbox" name="decision_type[]" id="decision_type-financial_property" value="financial_property"',
        '<label for="decision_type-financial_property">',
        'Financial and Property',
        '<input  type="checkbox" name="decision_type[]" id="decision_type-healthcare" value="healthcare"',
        '<label for="decision_type-healthcare">',
        'Health Care',
        '<input  type="checkbox" name="decision_type[]" id="decision_type-personal_life_care" value="personal_life_care"',
        '<label for="decision_type-personal_life_care">',
        'Personal Life and Care',
        '<label id="decision_citation-label" for="decision_citation">Decision Citation</label>',
        '<textarea',
        'name="decision_citation" id="decision_citation"',

        '<a href="' . \localized_route('lawPolicySources.show', $lawPolicySource) . '">Cancel</a>',
        '<button type="submit">Submit</button>',
        '<aside>',
        '<h2>',
        '<a href="' . \localized_route('lawPolicySources.show', $lawPolicySource) . "\">{$lawPolicySource->name}</a>",
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('provisions.create', ['lawPolicySource' => $lawPolicySource]);

    $view->assertSeeInOrder($toSee, false);
})->group('Provisions', 'LawPolicySources');

test('create route render errors', function ($data, $errors, $anchors = []) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);

    $toSee = ['<div id="error-summary" role="alert">'];

    foreach ($errors as $key => $message) {
        $anchor = $anchors[$key] ?? $key;
        $toSee[] = "<li><a href=\"#{$anchor}\">{$message}</a></li>";
    }

    foreach ($errors as $key => $message) {
        $id = $anchors[$key] ?? $key;
        $toSee[] = "id=\"{$id}";
        $toSee[] = 'aria-describedby';
        $toSee[] = "{$key}-error";
        $toSee[] = 'aria-invalid="true"';
        $toSee[] = "<p class=\"field__error\" id=\"{$key}-error\">";
        $toSee[] = $message;
    }

    $view = $this->actingAs($user)
        ->withViewErrors($errors)
        ->view('provisions.create', ['lawPolicySource' => $lawPolicySource]);

    $view->assertSeeInOrder($toSee, false);
})->with('provisionValidationErrors')
    ->group('Provisions', 'LawPolicySources');

test('create route - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $this->withoutExceptionHandling()->get(\localized_route('provisions.create', $lawPolicySource));
})->throws(AuthenticationException::class)
    ->group('Provisions', 'LawPolicySources');

test('create route - unauthenticated redirected to login', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $response = $this->get(\localized_route('provisions.create', $lawPolicySource));
    $response->assertRedirect(\localized_route('login'));
})->group('Provisions', 'LawPolicySources');
