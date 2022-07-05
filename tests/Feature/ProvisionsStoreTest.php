<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('store route - required values', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);
    $data = [
        'section' => '21.b',
        'body' => 'test provision body text',
    ];

    $response = $this->actingAs($user)->post(route('provisions.store', $lawPolicySource), $data);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    $lawPolicySource->refresh();
    expect($lawPolicySource->provisions[0]->section)->toBe($data['section']);
    expect($lawPolicySource->provisions[0]->body)->toBe($data['body']);
})->group('Provisions', 'LawPolicySources');

test('store route - all values', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);
    $data = [
        'section' => '21.b',
        'body' => 'test provision body text',
        'decision_type' => [ProvisionDecisionTypes::Financial->value],
        'legal_capacity_approach' => $this->faker->randomElement(LegalCapacityApproaches::values()),
        'decision_making_capability' => [DecisionMakingCapabilities::Independent->value],
        'reference' => $this->faker->unique()->url(),
        'court_challenge' => ProvisionCourtChallenges::ResultOf->value,
        'decision_citation' => $this->faker->paragraph(),
    ];

    $response = $this->actingAs($user)->post(route('provisions.store', $lawPolicySource), $data);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($lawPolicySource->provisions[0]->section)->toBe($data['section']);
    expect($lawPolicySource->provisions[0]->body)->toBe($data['body']);
    expect($lawPolicySource->provisions[0]->decision_type)->toBe($data['decision_type']);
    expect($lawPolicySource->provisions[0]->legal_capacity_approach)->toBe(LegalCapacityApproaches::from($data['legal_capacity_approach']));
    expect($lawPolicySource->provisions[0]->decision_making_capability)->toBe($data['decision_making_capability']);
    expect($lawPolicySource->provisions[0]->reference)->toBe($data['reference']);
    expect($lawPolicySource->provisions[0]->court_challenge)->toBe(ProvisionCourtChallenges::from($data['court_challenge']));
    expect($lawPolicySource->provisions[0]->decision_citation)->toBe($data['decision_citation']);
})->group('Provisions', 'LawPolicySources');

test('store route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);

    $response = $this->actingAs($user)->post(route('provisions.store', $lawPolicySource), $data);
    $response->assertSessionHasErrors($errors);
})->with('provisionValidationErrors')
    ->group('Provisions', 'LawPolicySources');

// Split these off form ProvisionValidationErrors dataset due to Hearth #149
// https://github.com/fluid-project/hearth/issues/149
test('store route - array validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);

    $response = $this->actingAs($user)->post(route('provisions.store', $lawPolicySource), $data);
    $response->assertSessionHasErrors($errors);
})->with('provisionArrayValidationErrors')
    ->group('Provisions', 'LawPolicySources');

test('store route - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);

    $this->withoutExceptionHandling()->post(route('provisions.store', $lawPolicySource), [
        'section' => '21.b',
        'body' => 'test provision body text',
    ]);
})->throws(AuthenticationException::class)
    ->group('Provisions', 'LawPolicySources');

test('store route - unauthenticated redirected to login', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test policy',
    ]);
    $response = $this->post(route('provisions.store', $lawPolicySource), [
        'section' => '21.b',
        'body' => 'test provision body text',
    ]);

    $response->assertRedirect(\localized_route('login'));

    $lawPolicySource->refresh();
    expect($lawPolicySource->provisions)->toHaveCount(0);
})->group('Provisions', 'LawPolicySources');
