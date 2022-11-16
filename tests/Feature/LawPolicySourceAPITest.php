<?php

use App\Enums\LawPolicyTypes;
use App\Http\Resources\LawPolicySourceResource;
use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    LawPolicySource::factory()
        ->hasProvisions(2)
        ->hasRegimeAssessments(2)
        ->create([
            'type' => LawPolicyTypes::Constitutional->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => date('Y'),
        ]);
});

test('Resource', function () {
    $lawPolicySource = LawPolicySource::first();
    $lawPolicySourceResource = new LawPolicySourceResource($lawPolicySource);

    expect($lawPolicySourceResource->toJson())
        ->json()
        ->toHaveCount(13)
        ->id->toBe($lawPolicySource->id)
        ->name->toBe($lawPolicySource->name)
        ->type->toBe($lawPolicySource->type->value)
        ->is_core->toBe($lawPolicySource->is_core)
        ->reference->toBe($lawPolicySource->reference)
        ->jurisdiction->toBe($lawPolicySource->jurisdiction)
        ->jurisdiction_name->toBe('Toronto, Ontario, Canada')
        ->municipality->toBe($lawPolicySource->municipality)
        ->year_in_effect->toBe($lawPolicySource->year_in_effect)
        ->provisions->toBeArray()
        ->slug->toBe($lawPolicySource->slug)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api', 'lawPolicySourceAPI');

test('Resource - relationships loaded', function () {
    $lawPolicySource = lawPolicySource::first();
    $lawPolicySource->loadMissing('regimeAssessments')
        ->loadCount(['provisions', 'regimeAssessments']);
    $lawPolicySourceResource = new LawPolicySourceResource($lawPolicySource);

    expect($lawPolicySourceResource->toJson())
        ->json()
        ->toHaveCount(16)
        ->id->toBe($lawPolicySource->id)
        ->name->toBe($lawPolicySource->name)
        ->type->toBe($lawPolicySource->type->value)
        ->is_core->toBe($lawPolicySource->is_core)
        ->reference->toBe($lawPolicySource->reference)
        ->jurisdiction->toBe($lawPolicySource->jurisdiction)
        ->jurisdiction_name->toBe('Toronto, Ontario, Canada')
        ->municipality->toBe($lawPolicySource->municipality)
        ->year_in_effect->toBe($lawPolicySource->year_in_effect)
        ->provisions->toBeArray()
        ->provisions_count->toBeInt()
        ->regimeAssessments->toBeArray()
        ->regimeAssessments_count->toBeInt()
        ->slug->toBe($lawPolicySource->slug)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api', 'lawPolicySourceAPI');

test('Route - show', function () {
    Sanctum::actingAs(User::factory()->create());
    $lawPolicySource = lawPolicySource::first();

    $response = $this->get(route('api.lawPolicySources.show', $lawPolicySource));

    $response->assertOk()
        ->assertJsonPath('data.id', $lawPolicySource->id)
        ->assertJson(fn (AssertableJson $json) => $json->hasAll([
            'data.id',
            'data.name',
            'data.type',
            'data.is_core',
            'data.reference',
            'data.jurisdiction',
            'data.jurisdiction_name',
            'data.municipality',
            'data.year_in_effect',
            'data.provisions',
            'data.provisions_count',
            'data.slug',
            'data.created_at',
            'data.updated_at',
        ])
        );
})->group('api', 'lawPolicySourceAPI');

test('Route - show - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = lawPolicySource::first();

    $this->withoutExceptionHandling()->get(route('api.lawPolicySources.show', $lawPolicySource));
})->throws(AuthenticationException::class)
  ->group('api', 'lawPolicySourceAPI');

test('Route - index', function () {
    Sanctum::actingAs(User::factory()->create());
    $lawPolicySource = LawPolicySource::factory()
        ->hasProvisions(2)
        ->hasRegimeAssessments(2)
        ->create([
            'type' => LawPolicyTypes::Constitutional->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'US-NY',
            'municipality' => 'New York',
            'year_in_effect' => date('Y'),
        ]);

    $response = $this->get(route('api.lawPolicySources.index'));

    $response->assertOk()
        ->assertJsonPath('data.1.id', $lawPolicySource->id)
        ->assertJson(fn (AssertableJson $json) => $json->hasAll([
            'data.0.id',
            'data.0.name',
            'data.0.type',
            'data.0.is_core',
            'data.0.reference',
            'data.0.jurisdiction',
            'data.0.jurisdiction_name',
            'data.0.municipality',
            'data.0.year_in_effect',
            'data.0.provisions',
            'data.0.provisions_count',
            'data.0.slug',
            'data.0.created_at',
            'data.0.updated_at',
            'data.1.id',
            'data.1.name',
            'data.1.type',
            'data.1.is_core',
            'data.1.reference',
            'data.1.jurisdiction',
            'data.1.jurisdiction_name',
            'data.1.municipality',
            'data.1.year_in_effect',
            'data.1.provisions',
            'data.1.provisions_count',
            'data.1.slug',
            'data.1.created_at',
            'data.1.updated_at',
            'links',
            'meta',
        ])
        );
})->group('api', 'lawPolicySourceAPI');

test('Route - index - filtered by country', function () {
    Sanctum::actingAs(User::factory()->create());
    $lawPolicySourceNY = LawPolicySource::factory()
        ->hasProvisions(2)
        ->hasRegimeAssessments(2)
        ->create([
            'name' => 'test',
            'type' => LawPolicyTypes::Constitutional->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'US-NY',
            'municipality' => 'New York',
            'year_in_effect' => date('Y'),
        ]);

    $lawPolicySourceAK = LawPolicySource::factory()
        ->hasProvisions(2)
        ->hasRegimeAssessments(2)
        ->create([
            'name' => 'test',
            'type' => LawPolicyTypes::Constitutional->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'US-AK',
            'municipality' => null,
            'year_in_effect' => date('Y'),
        ]);

    $response = $this->get(route('api.lawPolicySources.index', ['country' => 'US']));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $lawPolicySourceAK->id)
        ->assertJsonPath('data.1.id', $lawPolicySourceNY->id)
        ->assertJson(fn (AssertableJson $json) => $json->hasAll([
            'data',
            'links',
            'meta',
        ])
                ->missingAll(['data.2.id'])
        );
})->group('api', 'lawPolicySourceAPI');

test('Route - index - filtered by jurisdiction', function () {
    Sanctum::actingAs(User::factory()->create());
    $lawPolicySource = LawPolicySource::factory()
        ->hasProvisions(2)
        ->hasRegimeAssessments(2)
        ->create([
            'name' => 'test',
            'type' => LawPolicyTypes::Constitutional->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'US-NY',
            'municipality' => 'New York',
            'year_in_effect' => date('Y'),
        ]);

    $response = $this->get(route('api.lawPolicySources.index', ['country' => 'US', 'subdivision' => 'NY']));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $lawPolicySource->id)
        ->assertJson(fn (AssertableJson $json) => $json->hasAll([
            'data',
            'links',
            'meta',
        ])
                ->missingAll(['data.1.id'])
        );
})->group('api', 'lawPolicySourceAPI');

test('Route - index - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(route('api.lawPolicySources.index'));
})->throws(AuthenticationException::class)
  ->group('api', 'lawPolicySourceAPI');
