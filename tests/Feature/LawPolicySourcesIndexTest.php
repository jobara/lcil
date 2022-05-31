<?php

use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index route without parameters', function () {
    $response = $this->get(localized_route('law-policy-sources.index'));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewMissing('lawPolicySources');
})->group('LawPolicySources');

test('index route with all countries', function () {
    $count = 2;
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory($count)->create();

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    expect($response['lawPolicySources'])->toHaveCount($count);
    foreach ($response['lawPolicySources'] as $lawPolicySource) {
        expect($lawPolicySource)->toBeInstanceOf(LawPolicySource::class);
    }
})->group('LawPolicySources');

test('index route with country parameter', function () {
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
        ]);
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA',
        ]);
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'US',
        ]);

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    // only 2 are from the CA country code
    expect($response['lawPolicySources'])->toHaveCount(2);
    foreach ($response['lawPolicySources'] as $lawPolicySource) {
        $country = explode('-', $lawPolicySource->jurisdiction)[0];

        expect($lawPolicySource)->toBeInstanceOf(LawPolicySource::class);
        expect($country)->toBe('CA');
    }
})->group('LawPolicySources');

test('index route with country parameter - no matches', function () {
    // create a Law and Policy Source to use for the test
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'US',
        ]);

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    // none have the CA country code
    expect($response['lawPolicySources'])->toHaveCount(0);
})->group('LawPolicySources');

test('index route with subdivision parameter for all countries', function () {
    $count = 2;
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory($count)->create();

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all', 'subdivision' => 'any']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    expect($response['lawPolicySources'])->toHaveCount($count);
    foreach ($response['lawPolicySources'] as $lawPolicySource) {
        expect($lawPolicySource)->toBeInstanceOf(LawPolicySource::class);
    }
})->group('LawPolicySources');

test('index route with country and subdivision parameters', function () {
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
        ]);
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA',
        ]);
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'US',
        ]);

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA', 'subdivision' => 'ON']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    // only 1 are from the CA country code and subdivision ON
    expect($response['lawPolicySources'])->toHaveCount(1);
    foreach ($response['lawPolicySources'] as $lawPolicySource) {
        $jurisdiction = explode('-', $lawPolicySource->jurisdiction);

        expect($lawPolicySource)->toBeInstanceOf(LawPolicySource::class);
        expect($jurisdiction[0])->toBe('CA');
        expect($jurisdiction[1])->toBe('ON');
    }
})->group('LawPolicySources');

test('index route with country and subdivision parameters - no matches', function () {
    // create a Law and Policy Source to use for the test
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
        ]);
    LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA',
        ]);

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA', 'subdivision' => 'BC']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    // none have the CA country code and Subdivision BC
    expect($response['lawPolicySources'])->toHaveCount(0);
})->group('LawPolicySources');

test('index route paged', function () {
    // create a Law and Policy Source to use for the test
    $count = 20;
    LawPolicySource::factory($count)->create();

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));

    $response->assertStatus(200);
    $response->assertViewIs('law-policy-sources.index');
    $response->assertViewHas('lawPolicySources');

    expect($response['lawPolicySources'])->toHaveCount(10);
    expect($response['lawPolicySources']->total())->toBe($count);
})->group('LawPolicySources');

test('index route rendered - without parameters', function () {
    $strings = [
        'Law and Policy Sources',
        'Search for sources of law and policy to view',
        'Country:',
        'All countries',
        'Province / Territory:',
        'Select a country first',
        'Law or policy name contains keywords:',
        'Search',
        'Search results will appear here',
    ];

    $strings_not_rendered = [
        'Search for sources of law and policy to view or edit',
        'Create new law or policy source if it does not already exist',
        'Found',
        'Previous',
        'Next',
    ];

    $response = $this->get(localized_route('law-policy-sources.index'));

    $response->assertSeeTextInOrder($strings);
    foreach ($strings_not_rendered as $notRendered) {
        $response->assertDontSeeText($notRendered);
    }
})->group('LawPolicySources');

test('index route rendered - with parameters', function () {
    LawPolicySource::factory()->create([
        'name' => 'test law and policy source',
    ]);
    $strings = [
        'Found',
        'test law and policy source',
    ];

    $strings_not_rendered = [
        'Search results will appear here',
        'Previous',
        'Next',
    ];

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));

    $response->assertSeeTextInOrder($strings);
    foreach ($strings_not_rendered as $notRendered) {
        $response->assertDontSeeText($notRendered);
    }
})->group('LawPolicySources');

test('index route rendered - paged', function () {
    LawPolicySource::factory(25)->create();
    $strings = [
        'Found',
        'Previous',
        'Next',
    ];

    $strings_not_rendered = [
        'Search results will appear here',
    ];

    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all', 'page' => 2]));

    $response->assertSeeTextInOrder($strings);
    foreach ($strings_not_rendered as $notRendered) {
        $response->assertDontSeeText($notRendered);
    }
})->group('LawPolicySources');

test('index route rendered - with authenticated user', function () {
    $strings = [
        'Search for sources of law and policy to view or edit',
        'Create new law or policy source if it does not already exist',
    ];

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(localized_route('law-policy-sources.index'));

    $response->assertSeeTextInOrder($strings);
})->group('LawPolicySources');

test('index route item sort order', function () {
    // create Law and Policy Sources to use for the test
    LawPolicySource::factory()
        ->create([
            'name' => 'Subdivision First',
            'jurisdiction' => 'CA-ON',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'Subdivision Second',
            'jurisdiction' => 'CA-ON',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'City Second',
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'City First',
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Markham',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'Country First',
            'jurisdiction' => 'CA',
        ]);
    LawPolicySource::factory()
        ->create([
            'name' => 'Country Second',
            'jurisdiction' => 'US',
        ]);

    $order = ['Country First', 'Subdivision First', 'Subdivision Second', 'City First', 'City Second', 'Country Second'];
    $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));
    $itemNames = array_column($response->viewData('lawPolicySources')->items(), 'name');

    expect($itemNames)->toBe($order);

})->group('LawPolicySources');
