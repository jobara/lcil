<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\LawPolicySource;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('edit route display', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'description' => 'Test Description',
        'year_in_effect' => 2022,
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);

    $response = $this->actingAs($user)->get(\localized_route('regimeAssessments.edit', $regimeAssessment));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.edit');
    $response->assertViewHas('regimeAssessment');
    $response->assertViewHas('lawPolicySources');
    expect($response['regimeAssessment'])->toBeInstanceOf(RegimeAssessment::class);
    expect(is_array($response['lawPolicySources']))->toBeTrue();
})->group('RegimeAssessments');

test('edit route render', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'description' => 'Test Description',
        'year_in_effect' => 2022,
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $toSee = [
        '<title>Edit Regime Assessment: Toronto, Ontario, Canada &mdash; Legal Capacity Inclusion Lens</title>',
        '<li><a href="'.\localized_route('regimeAssessments.index').'">Regime Assessments</a></li>',
        '<li><a href="'.\localized_route('regimeAssessments.show', $regimeAssessment).'">Toronto, Ontario, Canada</a></li>',
        'Edit Regime Assessment',
        '<h1 itemprop="name">Edit Regime Assessment</h1>',
        '<form',
        'id="ra-form"',
        'method="POST"',
        'action="'.route('regimeAssessments.update', $regimeAssessment),
        '<input type="hidden" name="_method" value="patch">',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('regimeAssessments.edit', [
            'regimeAssessment' => $regimeAssessment,
            'lawPolicySources' => $lawPolicySources,
        ]);

    $view->assertSeeInOrder($toSee, false);
})->group('RegimeAssessments');

test('edit route render errors', function ($data, $errors) {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();

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
        ->view('regimeAssessments.edit', [
            'regimeAssessment' => $regimeAssessment,
            'lawPolicySources' => $lawPolicySources,
        ]);

    $view->assertSeeInOrder($toSee, false);
})->with('regimeAssessmentValidationErrors')
    ->group('RegimeAssessments');

test('edit route - unauthenticated throws AuthenticationException', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();

    $this->withoutExceptionHandling()->get(\localized_route('regimeAssessments.edit', $regimeAssessment));
})->throws(AuthenticationException::class)
    ->group('RegimeAssessments');

test('edit route - unauthenticated redirected to login', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();

    $response = $this->get(\localized_route('regimeAssessments.create', $regimeAssessment));
    $response->assertRedirect(\localized_route('login'));
})->group('RegimeAssessments');
