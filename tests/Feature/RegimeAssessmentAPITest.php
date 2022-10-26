<?php

use App\Enums\EvaluationAssessments;
use App\Enums\RegimeAssessmentStatuses;
use App\Http\Resources\RegimeAssessmentResource;
use App\Models\Evaluation;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $ra = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'status' => RegimeAssessmentStatuses::Draft->value,
        ]);

    Evaluation::factory()
        ->for($ra)
        ->create([
            'assessment' => EvaluationAssessments::Fully->value,
            'comment' => $this->faker->paragraph(),
        ]);
});

test('Resource', function () {
    $regimeAssessment = RegimeAssessment::first();
    $raResource = new RegimeAssessmentResource($regimeAssessment);

    expect($raResource->toJson())
        ->json()
        ->toHaveCount(11)
        ->id->toBe($regimeAssessment->id)
        ->jurisdiction->toBe($regimeAssessment->jurisdiction)
        ->jurisdiction_name->toBe('Toronto, Ontario, Canada')
        ->municipality->toBe($regimeAssessment->municipality)
        ->description->toBe($regimeAssessment->description)
        ->year_in_effect->toBe($regimeAssessment->year_in_effect)
        ->status->toBe($regimeAssessment->status->value)
        ->evaluations->toBeArray()
        ->ra_id->toBe($regimeAssessment->ra_id)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api', 'regimeAssessmentAPI');

test('Resource - relationships loaded', function () {
    $regimeAssessment = RegimeAssessment::first();
    $regimeAssessment->loadMissing('lawPolicySources')
        ->loadCount(['evaluations', 'lawPolicySources']);
    $raResource = new RegimeAssessmentResource($regimeAssessment);

    expect($raResource->toJson())
        ->json()
        ->toHaveCount(14)
        ->id->toBe($regimeAssessment->id)
        ->jurisdiction->toBe($regimeAssessment->jurisdiction)
        ->jurisdiction_name->toBe('Toronto, Ontario, Canada')
        ->municipality->toBe($regimeAssessment->municipality)
        ->description->toBe($regimeAssessment->description)
        ->year_in_effect->toBe($regimeAssessment->year_in_effect)
        ->status->toBe($regimeAssessment->status->value)
        ->evaluations->toBeArray()
        ->evaluations_count->toBeInt()
        ->lawPolicySources->toBeArray()
        ->lawPolicySources_count->toBeInt()
        ->ra_id->toBe($regimeAssessment->ra_id)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api', 'regimeAssessmentAPI');

test('Route - show', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();

    $response = $this->get(route('api.regimeAssessments.show', $regimeAssessment));

    $response->assertOk()
        ->assertJsonPath('data.id', $regimeAssessment->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.id',
                'data.jurisdiction',
                'data.jurisdiction_name',
                'data.municipality',
                'data.description',
                'data.year_in_effect',
                'data.status',
                'data.evaluations',
                'data.evaluations_count',
                'data.lawPolicySources',
                'data.lawPolicySources_count',
                'data.ra_id',
                'data.created_at',
                'data.updated_at',
            ])
        );
})->group('api', 'regimeAssessmentAPI');

test('Route - related evaluations', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();

    $response = $this->get(route('api.regimeAssessments.evaluations', $regimeAssessment));

    $response->assertRedirect(route('api.evaluations.index', ['ra_id' => $regimeAssessment->ra_id]));
})->group('api', 'regimeAssessmentAPI');

test('Route - related evaluations - cannot change filtering by ra_id ', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();

    $response = $this->get(route('api.regimeAssessments.evaluations', [
        'regimeAssessment' => $regimeAssessment,
        'ra_id' => 'ra-change',
    ]));

    $response->assertRedirect(route('api.evaluations.index', ['ra_id' => $regimeAssessment->ra_id]));
})->group('api', 'regimeAssessmentAPI');

test('Route - related evaluations - filtered by measureCode ', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();
    $measureCode = '1.1.1.2';

    $response = $this->get(route('api.regimeAssessments.evaluations', [
        'regimeAssessment' => $regimeAssessment,
        'measureCode' => $measureCode,
    ]));

    $response->assertRedirect(route('api.evaluations.index', [
        'ra_id' => $regimeAssessment->ra_id,
        'measureCode' => $measureCode,
    ]));
})->group('api', 'regimeAssessmentAPI');

test('Route - related evaluations - filtered by provisionID ', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();
    $provisionID = 1;

    $response = $this->get(route('api.regimeAssessments.evaluations', [
        'regimeAssessment' => $regimeAssessment,
        'provisionID' => $provisionID,
    ]));

    $response->assertRedirect(route('api.evaluations.index', [
        'ra_id' => $regimeAssessment->ra_id,
        'provisionID' => $provisionID,
    ]));
})->group('api', 'regimeAssessmentAPI');

test('Route - related evaluations - filtered by assessment ', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();
    $assessment = EvaluationAssessments::Partially->value;

    $response = $this->get(route('api.regimeAssessments.evaluations', [
        'regimeAssessment' => $regimeAssessment,
        'assessment' => $assessment,
    ]));

    $response->assertRedirect(route('api.evaluations.index', [
        'ra_id' => $regimeAssessment->ra_id,
        'assessment' => $assessment,
    ]));
})->group('api', 'regimeAssessmentAPI');

test('Route - related evaluations - filtered by all fields ', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::first();
    $measureCode = '1.2.3.4';
    $provisionID = 2;
    $assessment = EvaluationAssessments::Partially->value;

    $response = $this->get(route('api.regimeAssessments.evaluations', [
        'regimeAssessment' => $regimeAssessment,
        'measureCode' => $measureCode,
        'provisionID' => $provisionID,
        'assessment' => $assessment,
    ]));

    $response->assertRedirect(route('api.evaluations.index', [
        'ra_id' => $regimeAssessment->ra_id,
        'measureCode' => $measureCode,
        'provisionID' => $provisionID,
        'assessment' => $assessment,
    ]));
})->group('api', 'regimeAssessmentAPI');

test('Route - show - unauthenticated throws AuthenticationException', function () {
    $regimeAssessment = RegimeAssessment::first();

    $this->withoutExceptionHandling()->get(route('api.regimeAssessments.show', $regimeAssessment));
})->throws(AuthenticationException::class)
    ->group('api', 'regimeAssessmentAPI');

test('Route - index - filtered by country', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessmentNY = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US-NY',
        ]);
    $regimeAssessmentAK = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US-AK',
        ]);

    $response = $this->get(route('api.regimeAssessments.index', ['country' => 'US']));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $regimeAssessmentAK->id)
        ->assertJsonPath('data.1.id', $regimeAssessmentNY->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data',
                'links',
                'meta',
            ])
                ->missingAll(['data.2.id'])
        );
})->group('api', 'regimeAssessmentAPI');

test('Route - index - filtered by jurisdiction', function () {
    Sanctum::actingAs(User::factory()->create());
    $regimeAssessment = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US-NY',
        ]);

    $response = $this->get(route('api.regimeAssessments.index', ['country' => 'US', 'subdivision' => 'NY']));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $regimeAssessment->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'regimeAssessmentAPI');

test('Route - index - filtered by status', function () {
    Sanctum::actingAs(User::factory()->create());
    $status = RegimeAssessmentStatuses::Published->value;
    $regimeAssessment = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US-NY',
            'status' => $status,
        ]);

    $response = $this->get(route('api.regimeAssessments.index', ['status' => $status]));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $regimeAssessment->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'regimeAssessmentAPI');

test('Route - index - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(route('api.evaluations.index'));
})->throws(AuthenticationException::class)
    ->group('api', 'regimeAssessmentAPI');
