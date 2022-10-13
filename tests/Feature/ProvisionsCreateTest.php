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

    $escapedName = htmlentities($lawPolicySource->name);

    $toSee = [
        "<title>Add Provision: {$escapedName} &mdash; Legal Capacity Inclusion Lens</title>",
        'Law and Policy Sources',
        $lawPolicySource->name,
        'Add Provision',
        '<h1 itemprop="name">Add Provision</h1>',
        '<aside>',
        '<h2>',
        '<a href="'.\localized_route('lawPolicySources.show', $lawPolicySource)."\">{$lawPolicySource->name}</a>",
        '<form',
        'method="POST"',
        'action="'.route('provisions.store', $lawPolicySource),
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

    $toSee = ['<div id="error-summary" role="alert" class="error-summary">'];

    foreach ($errors as $key => $message) {
        $anchor = $anchors[$key] ?? $key;
        $toSee[] = "<li><a href=\"#{$anchor}\">{$message}</a></li>";
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
