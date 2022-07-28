<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index route without parameters', function () {
    $response = $this->get(localized_route('regimeAssessments.index'));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewMissing('regimeAssessments');
})->group('RegimeAssessments');

test('index route with all countries', function () {
    $count = 2;
    RegimeAssessment::factory($count)->create([
        'status' => RegimeAssessmentStatuses::Published,
    ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    expect($response['regimeAssessments'])->toHaveCount($count);
    foreach ($response['regimeAssessments'] as $regimeAssessment) {
        expect($regimeAssessment)->toBeInstanceOf(RegimeAssessment::class);
    }
})->group('RegimeAssessments');

test('index route with country parameter', function () {
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US',
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => 'CA']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // only 2 are from the CA country code
    expect($response['regimeAssessments'])->toHaveCount(2);
    foreach ($response['regimeAssessments'] as $regimeAssessment) {
        $country = explode('-', $regimeAssessment->jurisdiction)[0];

        expect($regimeAssessment)->toBeInstanceOf(RegimeAssessment::class);
        expect($country)->toBe('CA');
    }
})->group('RegimeAssessments');

test('index route with country parameter - no matches', function () {
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US',
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => 'CA']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // none have the CA country code
    expect($response['regimeAssessments'])->toHaveCount(0);
})->group('RegimeAssessments');

test('index route with subdivision parameter for all countries', function () {
    $count = 2;
    RegimeAssessment::factory($count)->create([
        'status' => RegimeAssessmentStatuses::Published,
    ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '', 'subdivision' => 'any']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    expect($response['regimeAssessments'])->toHaveCount($count);
    foreach ($response['regimeAssessments'] as $regimeAssessment) {
        expect($regimeAssessment)->toBeInstanceOf(RegimeAssessment::class);
    }
})->group('RegimeAssessments');

test('index route with country and subdivision parameters', function () {
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US',
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => 'CA', 'subdivision' => 'ON']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // only 1 are from the CA country code and subdivision ON
    expect($response['regimeAssessments'])->toHaveCount(1);
    foreach ($response['regimeAssessments'] as $regimeAssessment) {
        $jurisdiction = explode('-', $regimeAssessment->jurisdiction);

        expect($regimeAssessment)->toBeInstanceOf(RegimeAssessment::class);
        expect($jurisdiction[0])->toBe('CA');
        expect($jurisdiction[1])->toBe('ON');
    }
})->group('RegimeAssessments');

test('index route with country and subdivision parameters - no matches', function () {
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA',
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => 'CA', 'subdivision' => 'BC']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // none have the CA country code and Subdivision BC
    expect($response['regimeAssessments'])->toHaveCount(0);
})->group('RegimeAssessments');

test('index route without published Regime Assessments', function () {
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::Draft,
        ]);
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::NeedsReview,
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // none are published
    expect($response['regimeAssessments'])->toHaveCount(0);
})->group('RegimeAssessments');

test('index route without published Regime Assessments - authenticated', function () {
    $user = User::factory()->create();
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::Draft,
        ]);
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::NeedsReview,
        ]);

    $response = $this->actingAs($user)->get(localized_route('regimeAssessments.index', ['country' => '']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // none are published
    expect($response['regimeAssessments'])->toHaveCount(2);
})->group('RegimeAssessments');

test('index route with status', function () {
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::Draft,
        ]);
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '', 'status' => RegimeAssessmentStatuses::Draft]));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // none are published
    expect($response['regimeAssessments'])->toHaveCount(1);
    expect($response['regimeAssessments'][0]->status)->toBe(RegimeAssessmentStatuses::Published);
})->group('RegimeAssessments');

test('index route with status - authenticated', function () {
    $user = User::factory()->create();
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::Draft,
        ]);
    RegimeAssessment::factory()
        ->create([
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $response = $this->actingAs($user)->get(localized_route('regimeAssessments.index', ['country' => '', 'status' => RegimeAssessmentStatuses::Draft]));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    // none are published
    expect($response['regimeAssessments'])->toHaveCount(1);
    expect($response['regimeAssessments'][0]->status)->toBe(RegimeAssessmentStatuses::Draft);
})->group('RegimeAssessments');

test('index route paged', function () {
    $count = 20;
    RegimeAssessment::factory($count)->create([
        'status' => RegimeAssessmentStatuses::Published,
    ]);

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '']));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertViewHas('regimeAssessments');

    expect($response['regimeAssessments'])->toHaveCount(10);
    expect($response['regimeAssessments']->total())->toBe($count);
})->group('RegimeAssessments');

test('index route rendered - without parameters', function () {
    $toSee = [
        'Regime Assessments',
        'Search for regime assessments',
        'Country:',
        'All countries',
        'Province / Territory:',
        'Choose a country first',
        'Description contains keywords:',
        'Search',
        'Search results will appear here',
    ];

    $dontSee = [
        'Create new regime assessment if it does not already exist',
        'Status:',
        'Found',
        'Previous',
        'Next',
    ];

    $response = $this->get(localized_route('regimeAssessments.index'));

    $response->assertSeeTextInOrder($toSee);
    assertDontSeeAnyText($response, $dontSee, false);
})->group('RegimeAssessments');

test('index route rendered - with parameters', function () {
    RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'status' => RegimeAssessmentStatuses::Published,
    ]);
    $toSee = [
        'Found',
        'Ontario, Canada',
    ];

    $dontSee = [
        'Search results will appear here',
        'Previous',
        'Next',
    ];

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '']));

    $response->assertSeeTextInOrder($toSee);
    assertDontSeeAnyText($response, $dontSee, false);
})->group('RegimeAssessments');

test('index route rendered - paged', function () {
    RegimeAssessment::factory(25)->create([
        'status' => RegimeAssessmentStatuses::Published,
    ]);
    $toSee = [
        'Found',
        'Previous',
        'Next',
    ];

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '', 'page' => 2]));

    $response->assertSeeTextInOrder($toSee);
    $response->assertDontSee('Search results will appear here', false);
})->group('RegimeAssessments');

test('index route rendered - authenticated', function () {
    $user = User::factory()->create();

    $toSee = [
        'Search for regime assessments',
        'Create new regime assessment if it does not already exist',
        'Status:',
    ];

    $response = $this->actingAs($user)->get(localized_route('regimeAssessments.index'));

    $response->assertSeeTextInOrder($toSee);
})->group('RegimeAssessments');

test('index route rendered - regime assessments', function () {
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US-NY',
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $toSee = [
        '<h2>Canada</h2>',
        '<h3>Federal</h3>',
        '<h4>',
        'Canada',
        '<h3>Ontario</h3>',
        '<h4>',
        'Ontario, Canada',
        '<h2>United States</h2>',
        '<h3>Federal</h3>',
        '<h4>',
        'United States',
        '<h3>New York</h3>',
        '<h4>',
        'New York, United States',
    ];

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '']));

    $response->assertSeeInOrder($toSee, false);
})->group('RegimeAssessments');

test('index route item sort order', function () {
    $third = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    $second = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    $fifth = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    $fourth = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Markham',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    $first = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA',
            'status' => RegimeAssessmentStatuses::Published,
        ]);
    $sixth = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'US',
            'status' => RegimeAssessmentStatuses::Published,
        ]);

    $order = [
        $first->id => get_jurisdiction_name($first->jurisdiction, $first->municipality),
        $second->id => get_jurisdiction_name($second->jurisdiction, $second->municipality),
        $third->id => get_jurisdiction_name($third->jurisdiction, $third->municipality),
        $fourth->id => get_jurisdiction_name($fourth->jurisdiction, $fourth->municipality),
        $fifth->id => get_jurisdiction_name($fifth->jurisdiction, $fifth->municipality),
        $sixth->id => get_jurisdiction_name($sixth->jurisdiction, $sixth->municipality),
    ];

    $response = $this->get(localized_route('regimeAssessments.index', ['country' => '']));

    expect($response->viewData('regimeAssessments')->pluck('id')->toArray())->toBe(array_keys($order));
    $response->assertSeeTextInOrder(array_values($order));
})->group('RegimeAssessments');
