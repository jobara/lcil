<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('update route', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create([
        'section' => '12',
        'body' => '<p>content</p>',
    ]);

    $data = [
        'section' => '12',
        'body' => '<p>updated content</p>',
    ];

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), $data);

    $update = Provision::firstWhere('id', $provision->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($update->section)->toBe($data['section']);
    expect($update->body)->toBe($data['body']);
})->group('Provisions', 'LawPolicySources');

test('update route - all values', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create([
        'section' => '12',
        'body' => '<p>content</p>',
        'decision_type' => [ProvisionDecisionTypes::Financial->value],
        'legal_capacity_approach' => LegalCapacityApproaches::Status->value,
        'decision_making_capability' => [DecisionMakingCapabilities::Independent->value],
        'reference' => 'http://example.com/original',
        'court_challenge' => ProvisionCourtChallenges::SubjectTo->value,
        'decision_citation' => 'decision citation',
    ]);

    $data = [
        'section' => '12 a',
        'body' => '<p>updated content</p>',
        'decision_type' => [ProvisionDecisionTypes::Health->value],
        'legal_capacity_approach' => LegalCapacityApproaches::Cognitive->value,
        'decision_making_capability' => [DecisionMakingCapabilities::Interdependent->value],
        'reference' => 'http://example.com/updated',
        'court_challenge' => ProvisionCourtChallenges::ResultOf->value,
        'decision_citation' => 'updated decision citation',
    ];

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), $data);

    $updated = Provision::firstWhere('id', $provision->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($updated->section)->toBe($data['section']);
    expect($updated->body)->toBe($data['body']);
    expect($updated->decision_type)->toBe($data['decision_type']);
    expect($updated->legal_capacity_approach)->toBe(LegalCapacityApproaches::from($data['legal_capacity_approach']));
    expect($updated->decision_making_capability)->toBe($data['decision_making_capability']);
    expect($updated->reference)->toBe($data['reference']);
    expect($updated->court_challenge)->toBe(ProvisionCourtChallenges::from($data['court_challenge']));
    expect($updated->decision_citation)->toBe($data['decision_citation']);
    expect($updated->slug)->toBe('12-a');
})->group('Provisions', 'LawPolicySources');

test('update route - no updates', function () {
    $user = User::factory()->create();
    $data = [
        'section' => '12',
        'body' => '<p>content</p>',
        'decision_type' => [ProvisionDecisionTypes::Financial->value],
        'legal_capacity_approach' => LegalCapacityApproaches::Status->value,
        'decision_making_capability' => [DecisionMakingCapabilities::Independent->value],
        'reference' => 'http://example.com/original',
        'court_challenge' => ProvisionCourtChallenges::SubjectTo->value,
        'decision_citation' => 'decision citation',
    ];
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create($data);

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), $data);

    $updated = Provision::firstWhere('id', $provision->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($updated->updated_at->toDateTimeString())->toBe($provision->created_at->toDateTimeString());
})->group('Provisions', 'LawPolicySources');

test('update route - clear dependent values', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->for($lawPolicySource)->create([
        'section' => '12',
        'body' => '<p>content</p>',
        'decision_type' => [ProvisionDecisionTypes::Financial->value],
        'court_challenge' => ProvisionCourtChallenges::SubjectTo->value,
        'decision_citation' => 'decision citation',
    ]);

    $data = [
        'section' => '12',
        'body' => '<p>content</p>',
        'court_challenge' => ProvisionCourtChallenges::NotRelated->value,
    ];

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), $data);

    $updated = Provision::firstWhere('id', $provision->id);
    $response->assertRedirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    $response->assertSessionHasNoErrors();

    expect($updated->decision_type)->toBeEmpty();
    expect($updated->decision_citation)->toBeEmpty();
})->group('Provisions', 'LawPolicySources');

test('update route - update non-existent provision', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();

    $data = [
        'section' => '12',
        'body' => '<p>content</p>',
        'court_challenge' => ProvisionCourtChallenges::NotRelated->value,
    ];

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => '22',
    ]), $data);

    $response->assertNotFound();
})->group('Provisions', 'LawPolicySources');

test('update route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->create([
        'section' => 'a 1',
        'body' => '<p>original content</p>',
    ]);

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), $data);
    $response->assertSessionHasErrors($errors);
})->with('provisionValidationErrors')
    ->group('Provisions', 'LawPolicySources');

test('update route - array validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->create([
        'section' => 'a 1',
        'body' => '<p>original content</p>',
    ]);

    $response = $this->actingAs($user)->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), $data);
    $response->assertSessionHasErrors($errors);
})->with('provisionArrayValidationErrors')
    ->group('Provisions', 'LawPolicySources');

test('update route - unauthenticated throws AuthenticationException', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->create();

    $this->withoutExceptionHandling()->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), [
        'section' => '12',
        'body' => '<p>test</p>',
    ]);
})->throws(AuthenticationException::class)
    ->group('Provisions', 'LawPolicySources');

test('update route - unauthenticated redirected to login', function () {
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()->create([
        'section' => '1a',
    ]);

    $response = $this->patch(route('provisions.update', [
        'lawPolicySource' => $lawPolicySource,
        'slug' => $provision->slug,
    ]), [
        'section' => '12',
        'body' => '<p>test</p>',
    ]);

    $response->assertRedirect(\localized_route('login'));

    $provisions = Provision::where('section', '12')->get();
    expect($provisions)->toHaveCount(0);
})->group('Provisions', 'LawPolicySources');
