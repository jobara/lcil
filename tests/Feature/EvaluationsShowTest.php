<?php

use App\Enums\LawPolicyTypes;
use App\Models\Evaluation;
use App\Models\LawPolicySource;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('show route display', function () {
    $evaluation = Evaluation::factory()->create();
    $regimeAssessment = $evaluation->regimeAssessment;
    $measure = $evaluation->measure;

    $response = $this->get(\localized_route('evaluations.show', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]));

    $response->assertStatus(200);
    $response->assertViewIs('evaluations.show');
    $response->assertViewHas('regimeAssessment');
    $response->assertViewHas('measure');
    $response->assertViewHas('evaluations');

    expect($response['regimeAssessment'])->toBeInstanceOf(RegimeAssessment::class);
    expect($response['measure'])->toBeInstanceOf(Measure::class);
    foreach ($response['evaluations'] as $evaluation) {
        expect($evaluation)->toBeInstanceOf(Evaluation::class);
    }
})->group('Evaluations');

test('show route render - no law and policy sources', function () {
    $user = User::factory()->create();
    $evaluation = Evaluation::factory()->create();
    $regimeAssessment = $evaluation->regimeAssessment;
    $measure = $evaluation->measure;

    $jurisdiction = htmlentities(get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality));

    $toSee = [
        "<title>Regime Assessment Evaluation - {$measure->code}: {$jurisdiction} &mdash; Legal Capacity Inclusion Lens</title>",
        '<nav aria-label="Breadcrumbs">',
        '<li><a href="'.localized_route('regimeAssessments.index').'">Regime Assessments</a></li>',
        '<li><a href="'.localized_route('regimeAssessments.show', $regimeAssessment),
        "{$jurisdiction}</a></li>",
        '<li  aria-current="page" >Legal Capacity Measure '.$measure->code,
        '<h1 itemprop="name">Legal Capacity Measure '.$measure->code,
        '<dl>',
        "<dt>{$measure->code}: {$measure->title}</dt>",
        "<dd>{$measure->description}</dd>",
        '<h2>Make an evaluation</h2>',
        "Review the provisions from the sources of law and policy and evaluate how well the provision satisfies the measure {$measure->code}: {$measure->title}",
    ];

    $dontSee = [
        '<section x-data="{open: false}">',
        '<h3',
        '<select',
        '<textarea',
        '<p>No provisions have been added.</p>',
        '<button type="submit">Save</button>',
        '<time',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
})->group('Evaluations');

test('show route render - with law and policy source', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => LawPolicyTypes::Statute->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => 2022,
        ]);
    Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);
    Evaluation::factory()
        ->for($measure)
        ->for($regimeAssessment)
        ->for($lawPolicySource->provisions->first())
        ->create();

    $jurisdiction = htmlentities(get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality));

    $toSee = [
        '<form method="POST" action="'.route('evaluations.update', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]),
        '<section x-data="{open: false}">',
        '<h3 id="'.$lawPolicySource->slug,
        '<button',
        'type="button"',
        'x-on:click="open = !open"',
        'x-bind:aria-expanded="open"',
        'aria-controls="'.$lawPolicySource->slug.'-content"',
        $lawPolicySource->name,
        '<dl>',
        '<dt>Type:</dt>',
        "<dd>{$lawPolicySource->type->labels()[$lawPolicySource->type->value]}</dd>",
        '<dt>Jurisdiction:</dt>',
        "<dd>{$jurisdiction}</dd>",
        '<dt>Year in effect:</dt>',
        "<dd>{$lawPolicySource->year_in_effect}</dd>",
        '<dt>Reference:</dt>',
        '<dd><a href="" aria-labelledby="'.$lawPolicySource->slug.'">Link</a></dd>',
        '<dt>Provisions:</dt>',
        '<dd>1 (1 evaluated)</dd>',
        '<a href="'.localized_route('lawPolicySources.show', $lawPolicySource).'">Edit / add provisions</a>',
        '<div id="'.$lawPolicySource->slug.'-content" x-show="open" x-cloak>',
        '<h4>',
        'Section / Subsection: '.$lawPolicySource->provisions->first()->section,
        '<select',
        '<textarea',
        '<button type="submit">Save</button>',
    ];

    $dontSee = [
        '<p>No provisions have been added.</p>',
        '<time',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
})->group('Evaluations');

test('show route render - guest with law and policy source', function () {
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => LawPolicyTypes::Statute->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => 2022,
        ]);
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);
    $evaluation = Evaluation::factory()
        ->for($measure)
        ->for($regimeAssessment)
        ->for($lawPolicySource->provisions->first())
        ->create();

    $jurisdiction = htmlentities(get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality));

    $toSee = [
        '<section x-data="{open: false}">',
        '<h3 id="'.$lawPolicySource->slug,
        '<button',
        'type="button"',
        'x-on:click="open = !open"',
        'x-bind:aria-expanded="open"',
        'aria-controls="'.$lawPolicySource->slug.'-content"',
        $lawPolicySource->name,
        '<dl>',
        '<dt>Type:</dt>',
        "<dd>{$lawPolicySource->type->labels()[$lawPolicySource->type->value]}</dd>",
        '<dt>Jurisdiction:</dt>',
        "<dd>{$jurisdiction}</dd>",
        '<dt>Year in effect:</dt>',
        "<dd>{$lawPolicySource->year_in_effect}</dd>",
        '<dt>Reference:</dt>',
        '<dd><a href="" aria-labelledby="'.$lawPolicySource->slug.'">Link</a></dd>',
        '<dt>Provisions:</dt>',
        '<dd>1 (1 evaluated)</dd>',
        '<div id="'.$lawPolicySource->slug.'-content" x-show="open" x-cloak>',
        '<h4>',
        'Section / Subsection: '.$lawPolicySource->provisions->first()->section,
    ];

    $dontSee = [
        '<form method="POST" action="'.route('evaluations.update', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]),
        'Edit / add provisions',
        '<p>No provisions have been added.</p>',
        '<select',
        '<textarea',
        '<button type="submit">Save</button>',
        '<time',
    ];

    $view = $this->withViewErrors([])
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
})->group('Evaluations');

test('show route render - law and policy source minimum fields', function () {
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => null,
            'is_core' => null,
            'reference' => null,
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => null,
        ]);
    Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $jurisdiction = htmlentities(get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality));

    $toSee = [
        '<section x-data="{open: false}">',
        '<h3 id="'.$lawPolicySource->slug,
        '<button',
        'type="button"',
        'x-on:click="open = !open"',
        'x-bind:aria-expanded="open"',
        'aria-controls="'.$lawPolicySource->slug.'-content"',
        $lawPolicySource->name,
        '<dl>',
        '<dt>Jurisdiction:</dt>',
        "<dd>{$jurisdiction}</dd>",
        '<dt>Provisions:</dt>',
        '<dd>1 (0 evaluated)</dd>',
        '<div id="'.$lawPolicySource->slug.'-content" x-show="open" x-cloak>',
        '<h4>',
        'Section / Subsection: '.$lawPolicySource->provisions->first()->section,
    ];

    $dontSee = [
        '<dt>Type:</dt>',
        '<dt>Year in effect:</dt>',
        '<dt>Reference:</dt>',
        'Link</a></dd>',
        '<p>No provisions have been added.</p>',
        '<select',
        '<textarea',
        '<button type="submit">Save</button>',
        '<time',
    ];

    $view = $this->withViewErrors([])
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
})->group('Evaluations');

test('show route render - law and policy source no provisions', function () {
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => null,
            'is_core' => null,
            'reference' => null,
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => null,
        ]);

    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $toSee = [
        '<dt>Provisions:</dt>',
        '<dd>0 </dd>',
        '<p>No provisions have been added.</p>',
    ];

    $dontSee = [
        'evaluated',
        '<h4>',
        'Section / Subsection: ',

    ];

    $view = $this->withViewErrors([])
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
})->group('Evaluations');

test('show route render - after save', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => LawPolicyTypes::Statute->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => 2022,
        ]);
    Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);
    Evaluation::factory()
        ->for($measure)
        ->for($regimeAssessment)
        ->for($lawPolicySource->provisions->first())
        ->create();

    $toSee = [
        '<button type="submit">Save</button>',
        '<span id="save__message" role="status" x-data="duration',
        '<time x-bind:datetime="duration.iso"',
        'Last save successful <span x-text="duration.text">',
        'ago',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])
        ->withSession(['status' => 'saved'])
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
})->group('Evaluations');

test('edit route render errors', function ($data, $errors, $anchors = []) {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'name' => 'Test Law and Policy Source',
            'type' => LawPolicyTypes::Statute->value,
            'is_core' => true,
            'reference' => $this->faker->unique()->url(),
            'jurisdiction' => 'CA-ON',
            'municipality' => 'Toronto',
            'year_in_effect' => 2022,
        ]);
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $toSee = ['<div id="error-summary" role="alert">'];

    foreach ($errors as $key => $message) {
        $anchor = sprintf($anchors[$key] ?? $key, $provision->id);
        $toSee[] = "<li><a href=\"#{$anchor}\">{$message}</a></li>";
    }

    foreach ($errors as $key => $message) {
        $id = sprintf($anchors[$key] ?? $key, $provision->id);
        $toSee[] = "id=\"{$id}";
    }

    $expandedErrors = [];

    foreach ($errors as $name => $message) {
        $expandedErrors[sprintf($name, $provision->id)] = $message;
    }

    $view = $this->actingAs($user)
        ->withViewErrors($expandedErrors)
        ->view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => Evaluation::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
})->with('evaluationValidationErrors')
    ->group('Evaluations');
