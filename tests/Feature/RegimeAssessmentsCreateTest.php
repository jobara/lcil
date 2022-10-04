<?php

use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create route display', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(\localized_route('regimeAssessments.create'));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.create');
    $response->assertViewHas('lawPolicySources');
    expect(is_array($response['lawPolicySources']))->toBeTrue();
})->group('RegimeAssessments');

test('create route render', function () {
    $user = User::factory()->create();

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $toSee = [
        '<title>Create Regime Assessment &mdash; Legal Capacity Inclusion Lens</title>',
        '<li><a href="'.\localized_route('regimeAssessments.index').'">Regime Assessments</a></li>',
        'Create Regime Assessment',
        '<h1 itemprop="name">Create Regime Assessment</h1>',
        '<form',
        'id="ra-form"',
        'method="POST"',
        'action="'.route('regimeAssessments.store'),
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('regimeAssessments.create', ['lawPolicySources' => $lawPolicySources]);

    $view->assertSeeInOrder($toSee, false);
})->group('RegimeAssessments');

test('create route render errors', function ($data, $errors) {
    $user = User::factory()->create();
    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $toSee = ['<div id="error-summary" role="alert">'];

    foreach ($errors as $key => $message) {
        $toSee[] = "<li><a href=\"#{$key}\">{$message}</a></li>";
    }

    $view = $this->actingAs($user)
        ->withViewErrors($errors)
        ->view('regimeAssessments.create', ['lawPolicySources' => $lawPolicySources]);

    $view->assertSeeInOrder($toSee, false);
})->with('regimeAssessmentValidationErrors')
    ->group('RegimeAssessments');

test('create route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(\localized_route('regimeAssessments.create'));
})->throws(AuthenticationException::class)
    ->group('RegimeAssessments');

test('create route - unauthenticated redirected to login', function () {
    $response = $this->get(\localized_route('regimeAssessments.create'));
    $response->assertRedirect(\localized_route('login'));
})->group('RegimeAssessments');
