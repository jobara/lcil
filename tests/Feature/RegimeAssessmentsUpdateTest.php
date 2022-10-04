<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
        'description' => 'test ra description',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);
});

test('update route', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::first();

    $data = [
        'country' => 'CA',
        'subdivision' => 'ON',
        'status' => RegimeAssessmentStatuses::Published->value,
    ];

    $response = $this->actingAs($user)->patch(route('regimeAssessments.update', $regimeAssessment), $data);

    $update = RegimeAssessment::firstWhere('id', $regimeAssessment->id);
    $response->assertRedirect(\localized_route('regimeAssessments.show', $update));
    $response->assertSessionHasNoErrors();

    expect($update->jurisdiction)->toBe($regimeAssessment->jurisdiction);
    expect($update->municipality)->toBe($regimeAssessment->municipality);
    expect($update->year_in_effect)->toBe($regimeAssessment->year_in_effect);
    expect($update->description)->toBe($regimeAssessment->description);
    expect($update->status)->toBe(RegimeAssessmentStatuses::Published);
    expect($update->ra_id)->toBe($regimeAssessment->ra_id);
    expect(RegimeAssessment::all())->toHaveCount(1);
})->group('RegimeAssessments');

test('update route - all values', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::first();

    $data = [
        'country' => 'US',
        'subdivision' => 'NY',
        'municipality' => null,
        'year_in_effect' => 2020,
        'description' => 'updated ra description',
        'status' => RegimeAssessmentStatuses::Published->value,
    ];

    $response = $this->actingAs($user)->patch(route('regimeAssessments.update', $regimeAssessment), $data);

    $update = RegimeAssessment::firstWhere('id', $regimeAssessment->id);
    $response->assertRedirect(\localized_route('regimeAssessments.show', $update));
    $response->assertSessionHasNoErrors();

    expect($update->jurisdiction)->toBe('US-NY');
    expect($update->municipality)->toBeNull();
    expect($update->year_in_effect)->toBe($data['year_in_effect']);
    expect($update->description)->toBe($data['description']);
    expect($update->status)->toBe(RegimeAssessmentStatuses::Published);
    expect($update->ra_id)->toBe($regimeAssessment->ra_id);
    expect(RegimeAssessment::all())->toHaveCount(1);
})->group('RegimeAssessments');

test('update route - no updates', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::first();

    $data = [
        'country' => 'CA',
        'subdivision' => 'ON',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ];

    $response = $this->actingAs($user)->patch(route('regimeAssessments.update', $regimeAssessment), $data);

    $update = RegimeAssessment::firstWhere('id', $regimeAssessment->id);
    $response->assertRedirect(\localized_route('regimeAssessments.show', $update));
    $response->assertSessionHasNoErrors();

    expect($update->updated_at->toDateTimeString())->toBe($regimeAssessment->created_at->toDateTimeString());
})->group('RegimeAssessments');

test('update route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::first();

    $response = $this->actingAs($user)->patch(route('regimeAssessments.update', $regimeAssessment), $data);
    $response->assertSessionHasErrors($errors);
})->with('regimeAssessmentValidationErrors')
    ->group('RegimeAssessments');

test('update route - unauthenticated throws AuthenticationException', function () {
    $regimeAssessment = RegimeAssessment::first();
    $this->withoutExceptionHandling()->patch(route('regimeAssessments.update', $regimeAssessment), [
        'country' => 'CA',
        'subdivision' => 'ON',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);
})->throws(AuthenticationException::class)
    ->group('RegimeAssessments');

test('update route - unauthenticated redirected to login', function () {
    $regimeAssessment = RegimeAssessment::first();
    $response = $this->patch(route('regimeAssessments.update', $regimeAssessment), [
        'country' => 'US',
        'subdivision' => 'NY',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);

    $response->assertRedirect(\localized_route('login'));

    $regimeAssessments = RegimeAssessment::where('jurisdiction', 'US-NY')->get();
    expect($regimeAssessments)->toHaveCount(0);
})->group('RegimeAssessments');
