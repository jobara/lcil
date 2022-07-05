<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\DatabaseMigrations;

// Can't use the RefreshDatabase trait because full text searches do not work with it. Need to handle the commit and
// cleanup manually instead.
// See: https://laracasts.com/discuss/channels/testing/issue-with-data-persistenceeloquent-query-when-running-tests
// use RefreshDatabase;

// Refresh the DB with migrations
uses(DatabaseMigrations::class);

test('index route - keywords parameter', function ($data, $query, $expectedCount) {
    // create Law and Policy Sources to use for the test
    foreach($data as $attributes)
    LawPolicySource::factory()
        ->create($attributes);

    $response = $this->get(localized_route('lawPolicySources.index', $query));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.index');
    $response->assertViewHas('lawPolicySources');
    $this->assertEquals($expectedCount, $response['lawPolicySources']->count(), "Expected {$expectedCount} law and policy sources returned");
})->with([
    'keyword matches' => [
        [
            ['name' => 'Test Law and Policy Source', 'jurisdiction' => 'CA'],
            ['name' => 'Test LP'],
            ['name' => 'Act Source'],
            ['name' => 'An Act']
        ],
        ['country' => '', 'keywords' => 'Test Source'],
        3,
    ],
    'no keyword matches' => [
        [
            ['name' => 'Test Law and Policy Source', 'jurisdiction' => 'CA']
        ],
        ['country' => '', 'keywords' => 'None'],
        0,
    ],
    'no country matches' => [
        [
            ['name' => 'Test Law and Policy Source', 'jurisdiction' => 'CA']
        ],
        ['country' => 'US', 'keywords' => 'Test'],
        0,
    ],
  ])
  ->group('LawPolicySources');

test('index route with keywords parameter, without country parameter', function () {
    // create a Law and Policy Source to use for the test
    LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
        ]);

    $response = $this->get(localized_route('lawPolicySources.index', ['keywords' => 'test source']));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.index');
    $response->assertViewMissing('lawPolicySources');
})->group('LawPolicySources');
