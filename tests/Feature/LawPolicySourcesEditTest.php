<?php

use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('edit route display', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();

    $response = $this->actingAs($user)->get(\localized_route('lawPolicySources.edit', $lawPolicySource));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.edit');
    $response->assertViewHas('lawPolicySource');

    expect($response['lawPolicySource'])->toBeInstanceOf(LawPolicySource::class);
})->group('LawPolicySources');

test('edit route render', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();

    $toSee = [
        '<h1 itemprop="name">Edit Law or Policy Source</h1>',
        '<form',
        'method="POST" action="' . route('lawPolicySources.update', $lawPolicySource),
        '<a href="' . \localized_route('lawPolicySources.show', $lawPolicySource) . '">Cancel</a>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('lawPolicySources.edit', ['lawPolicySource' => $lawPolicySource]);

    $view->assertSeeInOrder($toSee, false);
})->group('LawPolicySources');

test('edit route render errors', function ($data, $errors, $anchors = []) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();

    $toSee = ['<div id="error-summary" role="alert">'];

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
        ->view('lawPolicySources.edit', ['lawPolicySource' => $lawPolicySource]);

    $view->assertSeeInOrder($toSee, false);
})->with('lawPolicySourceValidationErrors')
    ->group('LawPolicySources');

test('edit route - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $this->withoutExceptionHandling()->get(\localized_route('lawPolicySources.edit', $lawPolicySource));
})->throws(AuthenticationException::class)
    ->group('LawPolicySources');

test('edit route - unauthenticated redirected to login', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $response = $this->get(\localized_route('lawPolicySources.edit', $lawPolicySource));

    $response->assertRedirect(\localized_route('login'));
})->group('LawPolicySources');
