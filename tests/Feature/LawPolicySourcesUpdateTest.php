<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('update route', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test original',
    ]);

    $data = [
        'name' => 'updated test',
        'country' => 'CA',
    ];

    $response = $this->actingAs($user)->patch(route('lawPolicySources.update', $lawPolicySource), $data);

    $update = LawPolicySource::firstWhere('id', $lawPolicySource->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $update));
    $response->assertSessionHasNoErrors();

    expect($update->name)->toBe($data['name']);
    expect($update->jurisdiction)->toBe($data['country']);
    expect($update->slug)->toBe('ca-updated-test');
})->group('LawPolicySources');

test('update route - all values', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test original',
    ]);

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

    $response = $this->actingAs($user)->patch(route('lawPolicySources.update', $lawPolicySource), $data);

    $updated = LawPolicySource::firstWhere('id', $lawPolicySource->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $updated));
    $response->assertSessionHasNoErrors();

    expect($updated->name)->toBe($data['name']);
    expect($updated->type)->toBe(LawPolicyTypes::from($data['type']));
    expect($updated->is_core)->toBe($data['is_core'] ? 1 : 0);
    expect($updated->reference)->toBe($data['reference']);
    expect($updated->jurisdiction)->toBe('CA-ON');
    expect($updated->municipality)->toBe($data['municipality']);
    expect($updated->year_in_effect)->toBe($data['year_in_effect']);
    expect($updated->slug)->toBe('ca-on-test-all-values');
})->group('LawPolicySources');

test('store route - no updates', function () {
    $user = User::factory()->create();
    $data = [
        'name' => 'test update with same values',
        'type' => 'policy',
        'is_core' => 1,
        'reference' => 'http://example.com',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
    ];

    $location = [
        'country' => 'CA',
        'subdivision' => 'ON',
    ];

    $lawPolicySource = LawPolicySource::factory()->create(array_merge($data, ['jurisdiction' => 'CA-ON']));

    $response = $this->actingAs($user)->patch(route('lawPolicySources.update', $lawPolicySource), array_merge($data, $location));

    $updated = LawPolicySource::firstWhere('id', $lawPolicySource->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($updated->updated_at->toDateTimeString())->toBe($lawPolicySource->created_at->toDateTimeString());
})->group('LawPolicySources');

test('store route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
    ->has(Provision::factory())
    ->create();

    $response = $this->actingAs($user)->patch(route('lawPolicySources.update', $lawPolicySource), $data);
    $response->assertSessionHasErrors($errors);
})->with('lawPolicySourceValidationErrors')
    ->group('LawPolicySources');

test('store route - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $this->withoutExceptionHandling()->patch(route('lawPolicySources.update', $lawPolicySource), [
        'name' => 'test',
        'country' => 'CA',
    ]);
})->throws(AuthenticationException::class)
    ->group('LawPolicySources');

test('store route - unauthenticated redirected to login', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test original',
    ]);
    $response = $this->patch(route('lawPolicySources.update', $lawPolicySource), [
        'name' => 'test new',
        'country' => 'CA',
    ]);

    $response->assertRedirect(\localized_route('login'));

    $lawPolicySources = LawPolicySource::where('name', 'test')->get();
    expect($lawPolicySources)->toHaveCount(0);
})->group('LawPolicySources');
