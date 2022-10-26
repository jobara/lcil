<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use App\Http\Resources\ProvisionResource;
use App\Models\Provision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    Provision::factory()
        ->hasEvaluations(2)
        ->create([
            'legal_capacity_approach' => LegalCapacityApproaches::StatusOutcome->value,
            'decision_making_capability' => $this->faker->randomElements(DecisionMakingCapabilities::values(), $this->faker->numberBetween(1, count(DecisionMakingCapabilities::values()))),
            'reference' => $this->faker->unique()->url(),
            'court_challenge' => ProvisionCourtChallenges::SubjectTo->value,
            'decision_citation' => $this->faker->paragraph(),
            'decision_type' => $this->faker->randomElements(ProvisionDecisionTypes::values(), $this->faker->numberBetween(1, count(ProvisionDecisionTypes::values()))),
        ]);
});

test('Resource', function () {
    $provision = Provision::first();
    $provisionResource = new ProvisionResource($provision);

    expect($provisionResource->toJson())
        ->json()
        ->toHaveCount(12)
        ->id->toBe($provision->id)
        ->section->toBe($provision->section)
        ->reference->toBe($provision->reference)
        ->decision_type->toBe($provision->decision_type)
        ->legal_capacity_approach->toBe($provision->legal_capacity_approach->value)
        ->decision_making_capability->toBe($provision->decision_making_capability)
        ->slug->toBe($provision->slug)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api');

test('Resource - relationships loaded', function () {
    $provision = Provision::first();
    $provision->loadMissing(['evaluations', 'lawPolicySource'])
        ->loadCount('evaluations');
    $provisionResource = new ProvisionResource($provision);

    expect($provisionResource->toJson())
        ->json()
        ->toHaveCount(15)
        ->id->toBe($provision->id)
        ->section->toBe($provision->section)
        ->reference->toBe($provision->reference)
        ->decision_type->toBe($provision->decision_type)
        ->legal_capacity_approach->toBe($provision->legal_capacity_approach->value)
        ->decision_making_capability->toBe($provision->decision_making_capability)
        ->lawPolicySource->toBeArray()
        ->evaluations->toBeArray()
        ->evaluations_count->toBeInt()
        ->slug->toBe($provision->slug)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api');
