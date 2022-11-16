<?php

use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('edit route display', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create();

    $response = $this->actingAs($user)->get(\localized_route('provisions.edit', [$lawPolicySource, $provision->slug]));

    $response->assertStatus(200);
    $response->assertViewIs('provisions.edit');
})->group('Provisions', 'LawPolicySources');

test('edit route - provision not found', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();

    $response = $this->actingAs($user)->get(\localized_route('provisions.edit', [$lawPolicySource, 'test']));

    $response->assertNotFound();
})->group('Provisions', 'LawPolicySources');

test('edit route render', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);
    $provision = Provision::factory()->for($lawPolicySource)->create();

    $toSee = [
        "<title>Edit Provision: {$provision->section} — {$lawPolicySource->name} &mdash; Legal Capacity Inclusion Lens</title>",
        'Law and Policy Sources',
        $lawPolicySource->name,
        'Edit Provision',
        '<h1 itemprop="name">Edit Provision</h1>',
        '<aside>',
        '<h2>',
        '<a href="'.\localized_route('lawPolicySources.show', $lawPolicySource)."\">{$lawPolicySource->name}</a>",
        '<form',
        'method="POST"',
        'action="'.route('provisions.update', ['lawPolicySource' => $lawPolicySource, 'slug' => $provision->slug]),
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('provisions.edit', ['lawPolicySource' => $lawPolicySource, 'provision' => $provision]);

    $view->assertSeeInOrder($toSee, false);
})->group('Provisions', 'LawPolicySources');

test('edit route render errors', function ($data, $errors, $anchors = []) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);
    $provision = Provision::factory()->for($lawPolicySource)->create();

    $toSee = ['<div id="error-summary" role="alert" class="error-summary">'];

    foreach ($errors as $key => $message) {
        $anchor = $anchors[$key] ?? $key;
        $toSee[] = "<li><a href=\"#{$anchor}\">{$message}</a></li>";
    }

    $view = $this->actingAs($user)
        ->withViewErrors($errors)
        ->view('provisions.edit', ['lawPolicySource' => $lawPolicySource, 'provision' => $provision]);

    $view->assertSeeInOrder($toSee, false);
})->with('provisionValidationErrors')
    ->group('Provisions', 'LawPolicySources');

test('edit route - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create();

    $this->withoutExceptionHandling()->get(\localized_route('provisions.edit', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]));
})->throws(AuthenticationException::class)
    ->group('Provisions', 'LawPolicySources');

test('edit route - unauthenticated redirected to login', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create();

    $response = $this->get(\localized_route('provisions.create', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]));
    $response->assertRedirect(\localized_route('login'));
})->group('Provisions', 'LawPolicySources');
