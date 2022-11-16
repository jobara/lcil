<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('show route display', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create();

    $response = $this->get(localized_route('lawPolicySources.show', $lawPolicySource));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.show');
    $response->assertViewHas('lawPolicySource');

    expect($response['lawPolicySource'])->toBeInstanceOf(LawPolicySource::class);
})->group('LawPolicySources');

test('show route render - authenticated', function () {
    // create a Law and Policy Source and user to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();
    $user = User::factory()->create();

    $toSee = [
        'Provisions',
        '<a href="'.\localized_route('provisions.create', $lawPolicySource).'">Add Provision</a>',
    ];

    $view = $this->actingAs($user)->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeInOrder($toSee, false);
})->group('LawPolicySources');

test('show route render - all fields', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => $this->faker->randomElement(LawPolicyTypes::values()),
            'is_core' => $this->faker->boolean(),
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => ucfirst($this->faker->word()),
        ]);

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => 12,
        ]);

    $escapedName = htmlspecialchars($lawPolicySource->name);

    $toSee = [
        "<title>{$escapedName} &mdash; Legal Capacity Inclusion Lens</title>",
        $lawPolicySource->name,
        'Jurisdiction',
        "{$lawPolicySource->municipality}, Ontario, Canada",
        'Year in Effect',
        $lawPolicySource->year_in_effect,
        'Reference',
        "href=\"{$lawPolicySource->reference}\"",
        $lawPolicySource->reference,
        'Type',
        $lawPolicySource->type->labels()[$lawPolicySource->type->value],
        'Effect on Legal Capacity',
        $lawPolicySource->is_core ? 'Core - directly affects legal capacity' : 'Supplemental - indirectly affects legal capacity',
        'Provisions',
        "Section / Subsection: {$provision->section}",
    ];

    $dontSee = [
        'No provisions have been added.',
        'Add Provision',
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAnyText($view, $dontSee, false);
})->group('LawPolicySources');

test('show route render - minimum fields', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test LP Source',
            'type' => null,
            'is_core' => null,
            'reference' => null,
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
        ]);

    $dontSee = [
        '<dt>Reference</dt>',
        'Type',
        'Effect on Legal Capacity',
        'Add Provision',
    ];

    $toSee = [
        $lawPolicySource->name,
        'Jurisdiction',
        'Ontario, Canada',
        'Year in Effect',
        $lawPolicySource->year_in_effect,
        'No provisions have been added.',
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeTextInOrder($toSee);
    assertDontSeeAny($view, $dontSee, false);
})->group('LawPolicySources');

test('show route render - provision order', function () {
    // create a Law and Policy Source to use for the test
    $lawPolicySource = LawPolicySource::factory()->create();

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 2]);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 'ab 3']);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => '10']);

    $toSee = [
        'Provisions',
        'Section / Subsection: 2',
        'Section / Subsection: 10',
        'Section / Subsection: ab 3',
    ];

    $view = $this->view('lawPolicySources.show', ['lawPolicySource' => $lawPolicySource]);
    $view->assertSeeTextInOrder($toSee);
})->group('LawPolicySources');
