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
        '<title>Create a Law or Policy Source &mdash; Legal Capacity Inclusion Lens</title>',
        '<h1 itemprop="name">Create a Law or Policy Source</h1>',
        '<form',
        'method="POST" action="'.route('lawPolicySources.store'),
        '<a href="'.\localized_route('lawPolicySources.index').'">Cancel</a>',
    ];

    $view = $this->actingAs($user)
                 ->withViewErrors([])
                 ->view('lawPolicySources.create');

    $view->assertSeeInOrder($toSee, false);
})->group('LawPolicySources');

test('create route render errors', function ($data, $errors, $anchors = []) {
    $user = User::factory()->create();

    $toSee = ['<div id="error-summary" role="alert" class="error-summary">'];

    foreach ($errors as $key => $message) {
        $anchor = $anchors[$key] ?? $key;
        $toSee[] = "<li><a href=\"#{$anchor}\">{$message}</a></li>";
    }

    foreach ($errors as $key => $message) {
        $id = $anchors[$key] ?? $key;
        $toSee[] = "id=\"{$id}";
    }

    $view = $this->actingAs($user)
                 ->withViewErrors($errors)
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
