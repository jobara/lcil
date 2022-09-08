<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\RegimeAssessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('store route - required values', function () {
    $user = User::factory()->create();
    $id = 'ra-'.Carbon::now()->format('Ymd');
    $data = [
        'country' => 'CA',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ];

    $response = $this->actingAs($user)->post(route('regimeAssessments.store'), $data);
    $regimeAssessment = RegimeAssessment::first();

    $response->assertRedirect(\localized_route('regimeAssessments.show', $regimeAssessment));
    $response->assertSessionHasNoErrors();

    expect($regimeAssessment->jurisdiction)->toBe($data['country']);
    expect($regimeAssessment->status)->toBe(RegimeAssessmentStatuses::Draft);
    expect($regimeAssessment->ra_id)->toBe($id);
})->group('RegimeAssessments');

test('store route - all values', function () {
    $user = User::factory()->create();
    $id = 'ra-'.Carbon::now()->format('Ymd');
    $data = [
        'country' => 'CA',
        'subdivision' => 'ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
        'description' => 'test ra description',
        'status' => RegimeAssessmentStatuses::Published->value,
    ];

    $response = $this->actingAs($user)->post(route('regimeAssessments.store'), $data);
    $regimeAssessment = RegimeAssessment::first();

    $response->assertRedirect(\localized_route('regimeAssessments.show', $regimeAssessment));
    $response->assertSessionHasNoErrors();

    expect($regimeAssessment->jurisdiction)->toBe('CA-ON');
    expect($regimeAssessment->municipality)->toBe($data['municipality']);
    expect($regimeAssessment->year_in_effect)->toBe($data['year_in_effect']);
    expect($regimeAssessment->description)->toBe($data['description']);
    expect($regimeAssessment->status)->toBe(RegimeAssessmentStatuses::Published);
    expect($regimeAssessment->ra_id)->toBe($id);
})->group('RegimeAssessments');

test('store route - multiple RA entries on same date', function () {
    $user = User::factory()->create();
    $id = 'ra-'.Carbon::now()->format('Ymd');
    $data = [
        'country' => 'CA',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ];

    $this->actingAs($user)->post(route('regimeAssessments.store'), $data);
    $this->actingAs($user)->post(route('regimeAssessments.store'), $data);

    $regimeAssessments = RegimeAssessment::all();

    expect($regimeAssessments)->toHaveCount(2);
    expect($regimeAssessments[0]->ra_id)->toBe($id);
    expect($regimeAssessments[1]->ra_id)->toBe("{$id}-1");
})->group('RegimeAssessments');

test('store route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('regimeAssessments.store'), $data);

    $response->assertSessionHasErrors($errors);
})->with('regimeAssessmentValidationErrors')
    ->group('RegimeAssessments');

test('store route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->post(route('regimeAssessments.store'), [
        'country' => 'CA',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);
})->throws(AuthenticationException::class)
    ->group('RegimeAssessments');

test('store route - unauthenticated redirected to login', function () {
    $response = $this->post(route('regimeAssessments.store'), [
        'country' => 'CA',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);

    $response->assertRedirect(\localized_route('login'));

    $regimeAssessments = RegimeAssessment::all();
    expect($regimeAssessments)->toHaveCount(0);
})->group('RegimeAssessments');
