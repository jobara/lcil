<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\DatabaseMigrations;

// Can't use the RefreshDatabase trait because full text searches do not work with it. Need to handle the commit and
// cleanup manually instead.
// See: https://laracasts.com/discuss/channels/testing/issue-with-data-persistenceeloquent-query-when-running-tests
// use RefreshDatabase;

// Refresh the DB with migrations
uses(DatabaseMigrations::class);

test('index route with keywords parameter', function () {
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'jurisdiction' => 'CA',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'Test LP',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'Act Source',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'An Act',
        ]);

    $response = $this->get(localized_route('lawPolicySources.index', ['country' =>'', 'keywords' => 'Test Source']));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.index');
    $response->assertViewHas('lawPolicySources');
    // Should find 3 Law and Policy Sources
    $this->assertEquals(3, $response['lawPolicySources']->count(), 'Expected 3 law and policy sources returned');
})->group('LawPolicySources');

test('index route with keywords parameter - no keyword matches', function () {
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'jurisdiction' => 'CA',
        ]);

    $response = $this->get(localized_route('lawPolicySources.index', ['country' =>'', 'keywords' => 'None']));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.index');
    $response->assertViewHas('lawPolicySources');
    $this->assertEquals(0, $response['lawPolicySources']->count(), 'Expected 0 law and policy sources returned');
})->group('LawPolicySources');

test('index route with keywords parameter - no country matches', function () {
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'jurisdiction' => 'CA',
        ]);

    $response = $this->get(localized_route('lawPolicySources.index', ['country' =>'US', 'keywords' => 'Test']));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.index');
    $response->assertViewHas('lawPolicySources');
    $this->assertEquals(0, $response['lawPolicySources']->count(), 'Expected 0 law and policy sources returned');
})->group('LawPolicySources');

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
