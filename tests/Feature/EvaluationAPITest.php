<?php

use App\Enums\EvaluationAssessments;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\Measure;
use App\Models\Provision;
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
        ]);

    Evaluation::factory()
        ->for($ra)
        ->create([
            'assessment' => EvaluationAssessments::Fully->value,
            'comment' => $this->faker->paragraph(),
        ]);
});

test('Resource', function () {
    $evaluation = Evaluation::first();
    $evaluationResource = new EvaluationResource($evaluation);

    expect($evaluationResource->toJson())
        ->json()
        ->toHaveCount(5)
        ->id->toBe($evaluation->id)
        ->assessment->toBe($evaluation->assessment->value)
        ->comment->toBe($evaluation->comment)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api', 'evaluationAPI');

test('Resource - relationships loaded', function () {
    $evaluation = Evaluation::first();
    $evaluation->loadMissing(['measure', 'provision', 'regimeAssessment']);
    $evaluationResource = new EvaluationResource($evaluation);

    expect($evaluationResource->toJson())
        ->json()
        ->toHaveCount(8)
        ->id->toBe($evaluation->id)
        ->assessment->toBe($evaluation->assessment->value)
        ->comment->toBe($evaluation->comment)
        ->regimeAssessment->toBeArray()
        ->measure->toBeArray()
        ->provision->toBeArray()
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api', 'evaluationAPI');

test('Route - show', function () {
    Sanctum::actingAs(User::factory()->create());
    $evaluation = Evaluation::first();

    $response = $this->get(route('api.evaluations.show', $evaluation));

    $response->assertOk()
        ->assertJsonPath('data.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.id',
                'data.assessment',
                'data.comment',
                'data.regimeAssessment',
                'data.measure',
                'data.provision',
                'data.created_at',
                'data.updated_at',
            ])
        );
})->group('api', 'evaluationAPI');

test('Route - show - unauthenticated throws AuthenticationException', function () {
    $evaluation = Evaluation::first();

    $this->withoutExceptionHandling()->get(route('api.evaluations.show', $evaluation));
})->throws(AuthenticationException::class)
    ->group('api', 'evaluationAPI');

test('Route - index', function () {
    Sanctum::actingAs(User::factory()->create());

    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'US-NY',
    ]);

    $evaluation = Evaluation::factory()
        ->for($regimeAssessment)
        ->create([
            'comment' => $this->faker->paragraph(),
        ]);

    $response = $this->get(route('api.evaluations.index'));

    $response->assertOk()
        ->assertJsonPath('data.1.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.0.id',
                'data.0.assessment',
                'data.0.comment',
                'data.0.regimeAssessment',
                'data.0.measure',
                'data.0.provision',
                'data.0.created_at',
                'data.0.updated_at',
                'data.1.id',
                'data.1.assessment',
                'data.1.comment',
                'data.1.regimeAssessment',
                'data.1.measure',
                'data.1.provision',
                'data.1.created_at',
                'data.1.updated_at',
                'links',
                'meta',
            ])
        );
})->group('api', 'evaluationAPI');

test('Route - index - filtered by ra_id ', function () {
    Sanctum::actingAs(User::factory()->create());

    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'US-NY',
    ]);

    $evaluation = Evaluation::factory()
        ->for($regimeAssessment)
        ->create([
            'comment' => $this->faker->paragraph(),
        ]);

    $response = $this->get(route('api.evaluations.index', ['ra_id' => $regimeAssessment->ra_id]));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.0.id',
                'data.0.assessment',
                'data.0.comment',
                'data.0.regimeAssessment',
                'data.0.measure',
                'data.0.provision',
                'data.0.created_at',
                'data.0.updated_at',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'evaluationAPI');

test('Route - index - filtered by measureCode ', function () {
    Sanctum::actingAs(User::factory()->create());

    $measure = Measure::factory()->create([
        'code' => '1.1.1.1',
    ]);

    $evaluation = Evaluation::factory()
        ->for($measure)
        ->create([
            'comment' => $this->faker->paragraph(),
        ]);

    $response = $this->get(route('api.evaluations.index', ['measureCode' => $measure->code]));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.0.id',
                'data.0.assessment',
                'data.0.comment',
                'data.0.regimeAssessment',
                'data.0.measure',
                'data.0.provision',
                'data.0.created_at',
                'data.0.updated_at',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'evaluationAPI');

test('Route - index - filtered by provisionID ', function () {
    Sanctum::actingAs(User::factory()->create());

    $provision = Provision::factory()->create();

    $evaluation = Evaluation::factory()
        ->for($provision)
        ->create([
            'comment' => $this->faker->paragraph(),
        ]);

    $response = $this->get(route('api.evaluations.index', ['provisionID' => $provision->id]));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.0.id',
                'data.0.assessment',
                'data.0.comment',
                'data.0.regimeAssessment',
                'data.0.measure',
                'data.0.provision',
                'data.0.created_at',
                'data.0.updated_at',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'evaluationAPI');

test('Route - index - filtered by assessment ', function () {
    Sanctum::actingAs(User::factory()->create());

    $assessment = EvaluationAssessments::Partially->value;

    $evaluation = Evaluation::factory()
        ->create([
            'assessment' => $assessment,
            'comment' => $this->faker->paragraph(),
        ]);

    $response = $this->get(route('api.evaluations.index', ['assessment' => $assessment]));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.0.id',
                'data.0.assessment',
                'data.0.comment',
                'data.0.regimeAssessment',
                'data.0.measure',
                'data.0.provision',
                'data.0.created_at',
                'data.0.updated_at',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'evaluationAPI');

test('Route - index - filtered by all fields ', function () {
    Sanctum::actingAs(User::factory()->create());

    $regimeAssessment = RegimeAssessment::factory()->create();
    $measure = Measure::factory()->create(['code' => '1.1.2.1']);
    $provision = Provision::factory()->create();
    $assessment = EvaluationAssessments::Partially->value;

    Evaluation::factory(2)
        ->for($regimeAssessment)
        ->create();

    Evaluation::factory(2)
        ->for($measure)
        ->create();

    Evaluation::factory(2)
        ->for($provision)
        ->create([
            'assessment' => $assessment,
        ]);

    $evaluation = Evaluation::factory()
        ->for($regimeAssessment)
        ->for($measure)
        ->for($provision)
        ->create([
            'assessment' => $assessment,
        ]);

    $response = $this->get(route('api.evaluations.index', [
        'ra_id' => $regimeAssessment->ra_id,
        'measureCode' => $measure->code,
        'provisionID' => $provision->id,
        'assessment' => $assessment,
    ]));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $evaluation->id)
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll([
                'data.0.id',
                'data.0.assessment',
                'data.0.comment',
                'data.0.regimeAssessment',
                'data.0.measure',
                'data.0.provision',
                'data.0.created_at',
                'data.0.updated_at',
                'links',
                'meta',
            ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'evaluationAPI');

test('Route - index - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(route('api.evaluations.index'));
})->throws(AuthenticationException::class)
    ->group('api', 'evaluationAPI');
