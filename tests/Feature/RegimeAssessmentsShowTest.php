<?php

use App\Models\Evaluation;
use App\Models\LawPolicySource;
use App\Models\Measure;
use App\Models\MeasureDimension;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('show route display', function () {
    // create a Regime Assessment and measure to use for the test
    $regimeAssessment = RegimeAssessment::factory()->create();
    Measure::factory()->create();

    $response = $this->get(localized_route('regimeAssessments.show', $regimeAssessment));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.show');
    $response->assertViewHas('regimeAssessment');
    $response->assertViewHas('measureDimensions');

    expect($response['regimeAssessment'])->toBeInstanceOf(RegimeAssessment::class);
    expect($response['measureDimensions']->first())->toBeInstanceOf(MeasureDimension::class);
})->group('RegimeAssessments');

test('show route render - authenticated', function () {
    // create models needed for the test
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $measureDimension = MeasureDimension::all()->first();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
    ]);

    $provision = Provision::factory()
        ->for(LawPolicySource::factory()->create([
            'jurisdiction' => $regimeAssessment->jurisdiction,
            'municipality' => $regimeAssessment->municipality,
        ]))
        ->create();

    $regimeAssessment->lawPolicySources()->attach($provision->lawPolicySource);

    Evaluation::factory()
            ->for($regimeAssessment)
            ->for($provision)
            ->for($measure)
            ->create([
                'assessment' => 'fully',
            ]);

    $toSee = [
        '<nav aria-label="Breadcrumbs">',
        '<a href="' . localized_route('regimeAssessments.index') . '">Regime Assessments</a>',
        '<li  aria-current="page" >Toronto, Ontario, Canada</li>',
        '<h1',
        '<span>Regime Assessment Summary</span>',
        '<span>Toronto, Ontario, Canada</span>',
        "<span>({$regimeAssessment->status->value})</span>",
        '</h1>',
        "<p>{$regimeAssessment->description}</p>",
        '<h2>Measures</h2>',
        'There are 1 legal capacity measures divided into 1 dimensions. Provisions from',
        'sources of law or policy are evaluated against these measures to show how well a regime supports legal',
        'capacity.',
        '<a href="' . localized_route('about') . '">More about Legal Capacity Measurements</a>',
        '<span>Possible actions:',
        '<ul>',
        '<li>Choose a measure to evaluate.',
        '<li>Change assessment status to “Draft”, “Needs Review”, “Published”',
        '<details>',
        "<summary>{$measureDimension->code} {$measureDimension->description}</summary>",
        '<ol>',
        '<a href="">' . $measure->code . ': ' . $measure->title,
        '<ul>',
        '<li>1 fully</li>',
        '<li>0 partially </li>',
        '<li>0 not_at_all</li>',
        '<li>0 do not apply</li>',
        '</details>',
        '<aside>',
        '<h2>Regime Assessment Status</h2>',
        "<strong>{$regimeAssessment->status->value}",
        '<aside>',
        '<h2>Regime Assessment Details</h2>',
        '<dl>',
        '<dt>Jurisdiction:</dt>',
        '<dd>Toronto, Ontario, Canada</dd>',
        '<dt>Description:</dt>',
        "<dd>{$regimeAssessment->description}</dd>",
        '<dt>Effective Data:</dt>',
        "<dd>{$regimeAssessment->year_in_effect}</dd>",
        '<dt>ID:</dt>',
        "<dd>{$regimeAssessment->ra_id}</dd>",
        '<a href="">View / Edit Details</a>',
    ];

    $view = $this->actingAs($user)->view('regimeAssessments.show', [
        'regimeAssessment' => $regimeAssessment,
        'measureDimensions' => MeasureDimension::all(),
    ]);
    $view->assertSeeInOrder($toSee, false);
})->group('RegimeAssessments');

test('show route render - unauthenticated', function () {
    // create models needed for the test
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
    ]);

    $provision = Provision::factory()
        ->for(LawPolicySource::factory()->create([
            'jurisdiction' => $regimeAssessment->jurisdiction,
            'municipality' => $regimeAssessment->municipality,
        ]))
        ->create();

    $regimeAssessment->lawPolicySources()->attach($provision->lawPolicySource);

    Evaluation::factory()
            ->for($regimeAssessment)
            ->for($provision)
            ->for($measure)
            ->create([
                'assessment' => 'fully',
            ]);

    $dontSee = [
        "<span>({$regimeAssessment->status->value})</span>",
        '<span>Possible actions:',
        '<li>Choose a measure to evaluate.',
        '<li>Change assessment status to “Draft”, “Needs Review”, “Published”',
        '<h2>Regime Assessment Status</h2>',
        "<strong>{$regimeAssessment->status->value}",
        '<a href="">View / Edit Details</a>',
    ];

    $view = $this->view('regimeAssessments.show', [
        'regimeAssessment' => $regimeAssessment,
        'measureDimensions' => MeasureDimension::all(),
    ]);

    assertDontSeeAny($view, $dontSee, false);
})->group('RegimeAssessments');

test('show route render - no measure title', function () {
    // create models needed for the test
    $user = User::factory()->create();
    $measure = Measure::factory()->create([
        'title' => null,
    ]);
    $measureDimension = MeasureDimension::all()->first();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
    ]);

    $provision = Provision::factory()
        ->for(LawPolicySource::factory()->create([
            'jurisdiction' => $regimeAssessment->jurisdiction,
            'municipality' => $regimeAssessment->municipality,
        ]))
        ->create();

    $regimeAssessment->lawPolicySources()->attach($provision->lawPolicySource);

    $toSee = [
        "<summary>{$measureDimension->code} {$measureDimension->description}</summary>",
        '<ol>',
        '<a href="">' . $measure->code . '</a>',
    ];

    $view = $this->actingAs($user)->view('regimeAssessments.show', [
        'regimeAssessment' => $regimeAssessment,
        'measureDimensions' => MeasureDimension::all(),
    ]);

    $view->assertSeeInOrder($toSee, false);
})->group('RegimeAssessments');

test('show route render - no year in effect', function () {
    // create models needed for the test
    $user = User::factory()->create();
    Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => null,
    ]);

    $provision = Provision::factory()
        ->for(LawPolicySource::factory()->create([
            'jurisdiction' => $regimeAssessment->jurisdiction,
            'municipality' => $regimeAssessment->municipality,
        ]))
        ->create();

    $regimeAssessment->lawPolicySources()->attach($provision->lawPolicySource);

    $view = $this->actingAs($user)->view('regimeAssessments.show', [
        'regimeAssessment' => $regimeAssessment,
        'measureDimensions' => MeasureDimension::all(),
    ]);

    $view->assertDontSee('<dt>Effective Data:</dt>');
})->group('RegimeAssessments');

test('show route render - no law and policy sources', function () {
    // create models needed for the test
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $measureDimension = MeasureDimension::all()->first();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => null,
    ]);

    $toSee = [
        '<details>',
        "<summary>{$measureDimension->code} {$measureDimension->description}</summary>",
        '<ol>',
        '<a href="">' . $measure->code . ': ' . $measure->title,
        '<ul>',
        '<li>0 fully</li>',
        '<li>0 partially </li>',
        '<li>0 not_at_all</li>',
        '<li>0 do not apply</li>',
        '</details>',
    ];

    $view = $this->actingAs($user)->view('regimeAssessments.show', [
        'regimeAssessment' => $regimeAssessment,
        'measureDimensions' => MeasureDimension::all(),
    ]);

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<h3>Law and Policy Sources</h3>');
})->group('RegimeAssessments');
