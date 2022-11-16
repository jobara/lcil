<?php

use App\Models\LawPolicySource;
use App\Models\RegimeAssessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('render - all values - guest', function () {
    $regimeAssessment = RegimeAssessment::factory()
        ->has(LawPolicySource::factory()->count(3))
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_of_assessment' => 2022,
            'description' => $this->faker->paragraph(),
        ]);

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" />',
        ['regimeAssessment' => $regimeAssessment]
    );

    $date = Carbon::now()->format('Y-m-d');

    $toSee = [
        '<h4>',
        'Toronto, Ontario, Canada',
        $regimeAssessment->description,
        'Effective date:',
        '2022',
        'Law and Policy Sources:',
        htmlspecialchars($regimeAssessment->lawPolicySources[0]->name),
        htmlspecialchars($regimeAssessment->lawPolicySources[1]->name),
        htmlspecialchars($regimeAssessment->lawPolicySources[2]->name),
        'Modified:',
        $date,
        'Created:',
        $date,
        '<a href="'.localized_route('regimeAssessments.show', $regimeAssessment).'">View Details</a>',
    ];

    $dontSee = [
        "-({$regimeAssessment->status->labels()[$regimeAssessment->status->value]})",
        'View / Edit Details',
    ];

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('render - all values - authenticated', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()
        ->has(LawPolicySource::factory()->count(3))
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_of_assessment' => 2022,
            'description' => $this->faker->paragraph(),
        ]);

    $view = $this->actingAs($user)->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" />',
        ['regimeAssessment' => $regimeAssessment]
    );

    $date = Carbon::now()->format('Y-m-d');

    $toSee = [
        '<h4>',
        'Toronto, Ontario, Canada',
        "- ({$regimeAssessment->status->labels()[$regimeAssessment->status->value]})",
        $regimeAssessment->description,
        'Effective date:',
        '2022',
        'Law and Policy Sources:',
        htmlspecialchars($regimeAssessment->lawPolicySources[0]->name),
        htmlspecialchars($regimeAssessment->lawPolicySources[1]->name),
        htmlspecialchars($regimeAssessment->lawPolicySources[2]->name),
        'Modified:',
        $date,
        'Created:',
        $date,
        '<a href="'.localized_route('regimeAssessments.show', $regimeAssessment).'">View / Edit Details</a>',
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('View Details', false);
});

test('render - level', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" :level="$level"/>',
        [
            'regimeAssessment' => $regimeAssessment,
            'level' => 2,
        ]
    );

    $view->assertSee('<h2>', false);
    $view->assertDontSee('<h4>', false);
});

test('render - level above 1', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" :level="$level"/>',
        [
            'regimeAssessment' => $regimeAssessment,
            'level' => 0,
        ]
    );

    $view->assertSee('<h1>', false);
    $view->assertDontSee('<h4>', false);
    $view->assertDontSee('<h0>', false);
});

test('render - level below 6', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" :level="$level"/>',
        [
            'regimeAssessment' => $regimeAssessment,
            'level' => 7,
        ]
    );

    $view->assertSee('<h6>', false);
    $view->assertDontSee('<h4>', false);
    $view->assertDontSee('<h7>', false);
});

test('render - without description', function () {
    $regimeAssessment = RegimeAssessment::factory()
        ->create([
            'description' => null,
        ]);

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" />',
        ['regimeAssessment' => $regimeAssessment]
    );

    $view->assertDontSee('<p>', false);
});

test('render - without year', function () {
    $regimeAssessment = RegimeAssessment::factory()
        ->create([
            'year_of_assessment' => null,
        ]);

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" />',
        ['regimeAssessment' => $regimeAssessment]
    );

    $view->assertDontSee('Effective date:', false);
});

test('render - without law and policy sources', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" />',
        ['regimeAssessment' => $regimeAssessment]
    );

    $view->assertDontSee('Law and Policy Sources:', false);
});

test('render - with updated modified date', function () {
    $regimeAssessment = RegimeAssessment::factory()->create();
    $regimeAssessment->modified_at = Carbon::tomorrow();

    $view = $this->blade(
        '<x-regime-assessment-card :regimeAssessment="$regimeAssessment" />',
        ['regimeAssessment' => $regimeAssessment]
    );

    $toSee = [
        'Modified:',
        Carbon::tomorrow()->format('Y-m-d'),
        'Created:',
        Carbon::now()->format('Y-m-d'),
    ];

    $view->assertSeeInOrder($toSee, false);
});
