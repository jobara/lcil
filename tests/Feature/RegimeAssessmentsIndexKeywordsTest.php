<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\RegimeAssessment;
use Illuminate\Foundation\Testing\DatabaseMigrations;

// Can't use the RefreshDatabase trait because full text searches do not work with it. Need to handle the commit and
// cleanup manually instead.
// See: https://laracasts.com/discuss/channels/testing/issue-with-data-persistenceeloquent-query-when-running-tests
// use RefreshDatabase;

// Refresh the DB with migrations
uses(DatabaseMigrations::class);

test('index route - keywords parameter', function ($data, $query, $expectedCount) {
    foreach ($data as $attributes) {
        RegimeAssessment::factory()
            ->create(array_merge([
                'status' => RegimeAssessmentStatuses::Published,
            ], $attributes));
    }

    $response = $this->get(localized_route('regimeAssessments.index', $query));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');
    $this->assertEquals($expectedCount, $response['regimeAssessments']->count(), "Expected {$expectedCount} regime assessments returned");
})->with([
    'keyword matches' => [
        [
            ['description' => 'Test Regime Assessment', 'jurisdiction' => 'CA'],
            ['description' => 'Test RA'],
            ['description' => 'An Assessment for this jurisdiciton'],
            ['description' => 'A preliminary investigation'],
        ],
        ['country' => '', 'keywords' => 'Test Assessment'],
        3,
    ],
    'no keyword matches' => [
        [
            ['description' => 'Test Regime Assessment', 'jurisdiction' => 'CA'],
        ],
        ['country' => '', 'keywords' => 'None'],
        0,
    ],
    'no country matches' => [
        [
            ['description' => 'Test Regime Assessment', 'jurisdiction' => 'CA'],
        ],
        ['country' => 'US', 'keywords' => 'Test'],
        0,
    ],
])
    ->group('RegimeAssessments');

test('index route with keywords parameter, without country parameter', function () {
    RegimeAssessment::factory()
        ->create([
            'description' => 'Test Regime Assessment',
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['keywords' => 'test source']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewMissing('regimeAssessments');
})->group('RegimeAssessments');
