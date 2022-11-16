<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
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

    $response = $this->get(route('api.lawPolicySources.index', ['keywords' => 'test']));

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
