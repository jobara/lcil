<?php

use App\Enums\RegimeAssessmentStatuses;
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
    $regimeAssessment = RegimeAssessment::factory()->create([
        'status' => RegimeAssessmentStatuses::Published->value,
    ]);
    Measure::factory()->create();

    $response = $this->get(localized_route('regimeAssessments.show', $regimeAssessment));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.show');
    $response->assertViewHas('regimeAssessment');
    $response->assertViewHas('measureDimensions');

    expect($response['regimeAssessment'])->toBeInstanceOf(RegimeAssessment::class);
    expect($response['measureDimensions']->first())->toBeInstanceOf(MeasureDimension::class);
})->group('RegimeAssessments');

test('show route display - block unpublished from guest', function () {
    // create a Regime Assessment and measure to use for the test
    $regimeAssessment = RegimeAssessment::factory()->create([
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);
    Measure::factory()->create();

    $response = $this->get(localized_route('regimeAssessments.show', $regimeAssessment));

    $response->assertNotFound();
})->group('RegimeAssessments');

test('show route display - authenticated users can view unpublished', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'status' => RegimeAssessmentStatuses::NeedsReview->value,
    ]);
    Measure::factory()->create();

    $response = $this->actingAs($user)->get(localized_route('regimeAssessments.show', $regimeAssessment));

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
        'description' => 'test description',
        'status' => RegimeAssessmentStatuses::Published->value,
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
        '<title>Regime Assessment Summary: Toronto, Ontario, Canada &mdash; Legal Capacity Inclusion Lens</title>',
        '<nav aria-label="Breadcrumbs">',
        '<a href="'.localized_route('regimeAssessments.index').'">Regime Assessments</a>',
        '<li  aria-current="page" >Toronto, Ontario, Canada</li>',
        '<h1',
        '<span>Regime Assessment Summary</span>',
        '<span>Toronto, Ontario, Canada</span>',
        '<span>('.RegimeAssessmentStatuses::labels()[$regimeAssessment->status->value].')</span>',
        '</h1>',
        "<p>{$regimeAssessment->description}</p>",
        '<h2>Measures</h2>',
        'There are 1 legal capacity measures divided into 1 dimensions. Provisions from',
        'sources of law or policy are evaluated against these measures to show how well a regime supports legal',
        'capacity.',
        '<a href="'.localized_route('about').'">More about Legal Capacity Measurements</a>',
        '<span>Possible actions:',
        '<ul>',
        '<li>Choose a measure to evaluate.',
        '<li>Change assessment status to “Draft”, “Needs Review”, “Published”',
        '<details>',
        "<summary>{$measureDimension->code} {$measureDimension->description}</summary>",
        '<ol>',
        '<a href="'.localized_route('evaluations.show', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]),
        $measure->code.': '.$measure->title,
        '<ul>',
        '<li>1 fully</li>',
        '<li>0 partially</li>',
        '<li>0 not_at_all</li>',
        '<li>0 do not apply</li>',
        '</details>',
        '<aside>',
        '<h2 id="ra-status-heading">Regime Assessment Status</h2>',
        '<form method="POST" action="'.route('regimeAssessments.update', $regimeAssessment),
        '<input type="hidden" name="_method" value="patch">',
        '<select',
        'name="status" id="status" aria-labelledby="ra-status-heading"',
        'required',
        '<option value="draft" >Draft</option>',
        '<option value="needs_review" >Needs Review</option>',
        '<option value="published" selected>Published</option>',
        '<li><button type="submit">Save</button></li>',
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
        '<a href="'.\localized_route('regimeAssessments.edit', $regimeAssessment),
        'View / Edit Details',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('regimeAssessments.show', [
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
        'description' => 'test description',
        'status' => RegimeAssessmentStatuses::Published->value,
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
        '<h2 id="ra-status-heading">Regime Assessment Status</h2>',
        '<form method="POST" action="'.route('regimeAssessments.update', $regimeAssessment),
        '<input type="hidden" name="_method" value="patch">',
        'name="status" id="status" aria-labelledby="ra-status-heading"',
        '<option value="draft" >Draft</option>',
        '<option value="needs_review" >Needs Review</option>',
        '<option value="published" selected>Published</option>',
        '<li><button type="submit">Save</button></li>',
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
        '<a href="'.localized_route('evaluations.show', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]),
        $measure->code.'</a>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('regimeAssessments.show', [
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

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('regimeAssessments.show', [
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
        '<a href="'.localized_route('evaluations.show', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]),
        $measure->code.': '.$measure->title,
        '<ul>',
        '<li>0 fully</li>',
        '<li>0 partially</li>',
        '<li>0 not_at_all</li>',
        '<li>0 do not apply</li>',
        '</details>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('regimeAssessments.show', [
            'regimeAssessment' => $regimeAssessment,
            'measureDimensions' => MeasureDimension::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<h3>Law and Policy Sources</h3>');
})->group('RegimeAssessments');

test('show route render errors', function () {
    $user = User::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
        'description' => 'test description',
        'status' => RegimeAssessmentStatuses::Published->value,
    ]);

    $errors = [
        'status' => 'The Regime Assessment Status (status) must be one of the following: '.implode(', ', RegimeAssessmentStatuses::values()).'.',
    ];

    $toSee = [
        '<div id="error-summary" role="alert">',
        "<li><a href=\"#status\">{$errors['status']}</a></li>",
        'id="status"',
        'aria-describedby',
        'status-error',
        'aria-invalid="true"',
        '<p class="field__error" id="status-error">',
        $errors['status'],
    ];

    $view = $this->actingAs($user)
        ->withViewErrors($errors)
        ->view('regimeAssessments.show', [
            'regimeAssessment' => $regimeAssessment,
            'measureDimensions' => MeasureDimension::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
})->group('RegimeAssessments');
