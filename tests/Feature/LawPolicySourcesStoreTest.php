<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('store route - required values', function () {
    $user = User::factory()->create();
    $data = [
        'name' => 'test',
        'country' => 'CA',
    ];

    $response = $this->actingAs($user)->post(route('lawPolicySources.store'), $data);
    $lawPolicySource = LawPolicySource::firstWhere('name', $data['name']);

    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($lawPolicySource->name)->toBe($data['name']);
    expect($lawPolicySource->jurisdiction)->toBe($data['country']);
    expect($lawPolicySource->slug)->toBe('ca-test');
})->group('LawPolicySources');

test('store route - all values', function () {
    $user = User::factory()->create();
    $data = [
        'name' => 'test all values',
        'type' => 'policy',
        'is_core' => true,
        'reference' => 'http://example.com',
        'country' => 'CA',
        'subdivision' => 'ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
    ];

    $response = $this->actingAs($user)->post(route('lawPolicySources.store'), $data);
    $lawPolicySource = LawPolicySource::firstWhere('name', $data['name']);

    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($lawPolicySource->name)->toBe($data['name']);
    expect($lawPolicySource->type)->toBe(LawPolicyTypes::from($data['type']));
    expect($lawPolicySource->is_core)->toBe($data['is_core'] ? 1 : 0);
    expect($lawPolicySource->reference)->toBe($data['reference']);
    expect($lawPolicySource->jurisdiction)->toBe('CA-ON');
    expect($lawPolicySource->municipality)->toBe($data['municipality']);
    expect($lawPolicySource->year_in_effect)->toBe($data['year_in_effect']);
    expect($lawPolicySource->slug)->toBe('ca-on-test-all-values');
})->group('LawPolicySources');

test('store route - duplicate slug values', function () {
    $user = User::factory()->create();
    $data = [
        'name' => 'test',
        'country' => 'CA',
    ];

    $this->actingAs($user)->post(route('lawPolicySources.store'), $data);
    $this->actingAs($user)->post(route('lawPolicySources.store'), $data);

    $lawPolicySources = LawPolicySource::where('name', $data['name'])->get();

    expect($lawPolicySources)->toHaveCount(2);
    expect($lawPolicySources[0]->slug)->toBe('ca-test');
    expect($lawPolicySources[1]->slug)->toBe('ca-test-1');
})->group('LawPolicySources');

test('store route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('lawPolicySources.store'), $data);

    $response->assertSessionHasErrors($errors);
})->with('lawPolicySourceValidationErrors')
  ->group('LawPolicySources');

test('store route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->post(route('lawPolicySources.store'), [
        'name' => 'test',
        'country' => 'CA',
    ]);
})->throws(AuthenticationException::class)
  ->group('LawPolicySources');

test('store route - unauthenticated redirected to login', function () {
    $response = $this->post(route('lawPolicySources.store'), [
        'name' => 'test',
        'country' => 'CA',
    ]);

    $response->assertRedirect(\localized_route('login'));

    $lawPolicySources = LawPolicySource::where('name', 'test')->get();
    expect($lawPolicySources)->toHaveCount(0);
})->group('LawPolicySources');
