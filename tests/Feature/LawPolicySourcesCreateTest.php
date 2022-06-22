<?php

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create route display', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(\localized_route('lawPolicySources.create'));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.create');
})->group('LawPolicySources');

test('create route render', function () {
    $user = User::factory()->create();

    $toSee = [
        '<h1 itemprop="name">Create a Law or Policy Source</h1>',
        '<form method="POST" action="http://lcil.test/law-policy-sources">',
        '<label id="name-label" for="name">Law or Policy Name (required)</label>',
        '<input',
        'name="name" id="name" type="text"',
        'required',
        '<label id="country-label" for="country">Country (required)</label>',
        '<select',
        'name="country"',
        'id="country"',
        '<option value="" selected></option>',
        '<label id="subdivision-label" for="subdivision">Province / Territory</label>',
        '<select',
        'id="subdivision"',
        'name="subdivision"',
        '<label id="municipality-label" for="municipality">Municipality</label>',
        '<input',
        'name="municipality" id="municipality" type="text"',
        'aria-describedby="municipality-hint"',
        '<p class="field__hint" id="municipality-hint">',
        'Requires a Province / Territory to be selected',
        '<label id="year_in_effect-label" for="year_in_effect">Year in Effect</label>',
        '<input',
        'name="year_in_effect" id="year_in_effect" type="number" min="1800" max="2030"',
        'aria-describedby="year_in_effect-hint"',
        '<p class="field__hint" id="year_in_effect-hint">',
        'YYYY format. Example: 2022.',
        '<label id="reference-label" for="reference">Reference / Link</label>',
        '<input',
        'name="reference" id="reference" type="url"',
        'aria-describedby="reference-hint"',
        '<p class="field__hint" id="reference-hint">',
        'Web link or URL to source. Example: https://www.example.com/',
        '<label id="type-label" for="type">Type</label>',
        '<select',
        'name="type" id="type"',
        '<option value="" selected></option>',
        '<option value="case law" >Case Law</option>',
        '<option value="constitutional" >Constitutional</option>',
        '<option value="policy" >Policy</option>',
        '<option value="quasi-constitutional" >Quasi-Constitutional</option>',
        '<option value="regulation" >Regulation</option>',
        '<option value="statute" >Statute</option>',
        '<legend id="is_core-label">Effect on Legal Capacity</legend>',
        '<input  type="radio" name="is_core" id="is_core-1" value="1"',
        '<label for="is_core-1">',
        'Core - directly affects legal capacity',
        '<input  type="radio" name="is_core" id="is_core-0" value="0"',
        '<label for="is_core-0">',
        'Supplemental - indirectly affects legal capacity',
        '<a href="http://lcil.test/law-policy-sources">Cancel</a>',
        '<button type="submit">Submit</button>',
    ];

    $view = $this->withViewErrors([])
                 ->view('lawPolicySources.create');

    $view->assertSeeInOrder($toSee, false);
})->group('LawPolicySources');

test('create route render errors', function ($data, $errors) {
    $user = User::factory()->create();

    $toSee = ['<div role="alert">'];

    foreach ($errors as $key => $message) {
        $toSee[] = "<li><a href=\"#{$key}-label\">{$message}</a></li>";
    }

    foreach ($errors as $key => $message) {
        $toSee[] = "id=\"{$key}";
        $toSee[] = 'aria-describedby';
        $toSee[] = "{$key}-error";
        $toSee[] = 'aria-invalid="true"';
        $toSee[] = "<p class=\"field__error\" id=\"{$key}-error\">";
        $toSee[] = $message;
    }

    $view = $this->withViewErrors($errors)
                 ->view('lawPolicySources.create');

    $view->assertSeeInOrder($toSee, false);
})->with('lawPolicySourceValidationErrors')
  ->group('LawPolicySources');

test('create route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(\localized_route('lawPolicySources.create'));
})->throws(AuthenticationException::class)
  ->group('LawPolicySources');

test('create route - unauthenticated redirected to login', function () {
    $response = $this->get(\localized_route('lawPolicySources.create'));

    $response->assertRedirect(\localized_route('login'));
})->group('LawPolicySources');
