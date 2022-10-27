<?php

use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

// Can't use the RefreshDatabase trait because full text searches do not work with it. Need to handle the commit and
// cleanup manually instead.
// See: https://laracasts.com/discuss/channels/testing/issue-with-data-persistenceeloquent-query-when-running-tests
// use RefreshDatabase;

// Refresh the DB with migrations
uses(DatabaseMigrations::class, WithFaker::class);

test('Route - index - filtered', function () {
    Sanctum::actingAs(User::factory()->create());

    RegimeAssessment::factory()
        ->create([
            'description' => 'My first Regime Assessment',
        ]);

    $regimeAssessment = RegimeAssessment::factory()
        ->create([
            'description' => 'My second Regime Assessment',
        ]);

    $response = $this->get(route('api.regimeAssessments.index', ['keywords' => 'second']));

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
