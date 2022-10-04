<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('render LP Selections', function () {
    $lpOne = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create([
            'name' => 'LP 1',
            'type' => $this->faker->randomElement(LawPolicyTypes::values()),
            'jurisdiction' => 'US-NY',
            'municipality' => 'New York',
            'year_in_effect' => $this->faker->numberBetween(config('settings.year.min'), config('settings.year.max')),
        ]);

    $lpTwo = LawPolicySource::factory()->create([
        'name' => 'LP 2',
        'type' => $this->faker->randomElement(LawPolicyTypes::values()),
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => $this->faker->numberBetween(config('settings.year.min'), config('settings.year.max')),
    ]);

    $lpThree = LawPolicySource::factory()->create([
        'name' => 'LP 3',
        'type' => $this->faker->randomElement(LawPolicyTypes::values()),
        'jurisdiction' => 'CA',
        'municipality' => null,
        'year_in_effect' => $this->faker->numberBetween(config('settings.year.min'), config('settings.year.max')),
    ]);

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.law-policy-source-selection :lawPolicySources="$lawPolicySources" />',
            ['lawPolicySources' => $lawPolicySources]
        );

    $toSee = [
        '<ul role="list">',
        '<li>',
        '<h3>',
        'Canada',
        '<ul role="list">',
        '<li>',
        '<h4>',
        'Federal',
        '<ul role="list">',
        '<li>',
        '<h5>',
        $lpThree->name,
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpThree->id.']" id="lawPolicySources['.$lpThree->id.']"',
        'value="1"',
        '<label for="lawPolicySources['.$lpThree->id.']">',
        'Add to regime assessment',
        '<dl>',
        '<dt>Jurisdiction</dt>',
        '<dd>Canada</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lpThree->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lpThree->type->value}</dd>",
        '<dt>Provisions</dt>',
        "<dd>{$lpThree->provisions->count()}</dd>",
        '<li>',
        '<h4>',
        'Ontario',
        '<ul role="list">',
        '<li>',
        '<h5>',
        $lpTwo->name,
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpTwo->id.']" id="lawPolicySources['.$lpTwo->id.']"',
        'value="1"',
        '<label for="lawPolicySources['.$lpTwo->id.']">',
        'Add to regime assessment',
        '<dl>',
        '<dt>Jurisdiction</dt>',
        '<dd>Toronto, Ontario, Canada</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lpTwo->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lpTwo->type->value}</dd>",
        '<dt>Provisions</dt>',
        "<dd>{$lpTwo->provisions->count()}</dd>",
        '<li>',
        '<h3>',
        'United States',
        '<ul role="list">',
        '<li>',
        '<h4>',
        'New York',
        '<ul role="list">',
        '<li>',
        '<h5>',
        $lpOne->name,
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpOne->id.']" id="lawPolicySources['.$lpOne->id.']"',
        'value="1"',
        '<label for="lawPolicySources['.$lpOne->id.']">',
        'Add to regime assessment',
        '<dl>',
        '<dt>Jurisdiction</dt>',
        '<dd>New York, New York, United States</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lpOne->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lpOne->type->value}</dd>",
        '<dt>Provisions</dt>',
        "<dd>{$lpOne->provisions->count()}</dd>",
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('checked', false);
});

test('render LP Selections - checked', function () {
    $lpChecked = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create([
            'name' => 'LP 1',
            'type' => $this->faker->randomElement(LawPolicyTypes::values()),
            'jurisdiction' => 'US-NY',
            'municipality' => 'New York',
            'year_in_effect' => $this->faker->numberBetween(config('settings.year.min'), config('settings.year.max')),
        ]);

    $lpUnchecked = LawPolicySource::factory()->create([
        'name' => 'LP 2',
        'type' => $this->faker->randomElement(LawPolicyTypes::values()),
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => $this->faker->numberBetween(config('settings.year.min'), config('settings.year.max')),
    ]);

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.law-policy-source-selection :lawPolicySources="$lawPolicySources" :checked="$checked" />',
            [
                'lawPolicySources' => $lawPolicySources,
                'checked' => LawPolicySource::where('id', $lpChecked->id)->get(),
            ]
        );

    $toSee = [
        '<ul role="list">',
        '<li>',
        '<h3>',
        'Canada',
        '<ul role="list">',
        '<li>',
        '<h4>',
        'Ontario',
        '<ul role="list">',
        '<li>',
        '<h5>',
        $lpUnchecked->name,
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpUnchecked->id.']" id="lawPolicySources['.$lpUnchecked->id.']"',
        'value="1"',
        '<label for="lawPolicySources['.$lpUnchecked->id.']">',
        'Add to regime assessment',
        '<dl>',
        '<dt>Jurisdiction</dt>',
        '<dd>Toronto, Ontario, Canada</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lpUnchecked->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lpUnchecked->type->value}</dd>",
        '<dt>Provisions</dt>',
        "<dd>{$lpUnchecked->provisions->count()}</dd>",
        '<li>',
        '<h3>',
        'United States',
        '<ul role="list">',
        '<li>',
        '<h4>',
        'New York',
        '<ul role="list">',
        '<li>',
        '<h5>',
        $lpChecked->name,
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpChecked->id.']" id="lawPolicySources['.$lpChecked->id.']"',
        'value="1"',
        'checked',
        '<label for="lawPolicySources['.$lpChecked->id.']">',
        'Add to regime assessment',
        '<dl>',
        '<dt>Jurisdiction</dt>',
        '<dd>New York, New York, United States</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lpChecked->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lpChecked->type->value}</dd>",
        '<dt>Provisions</dt>',
        "<dd>{$lpChecked->provisions->count()}</dd>",
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render LP Selections - without optional attributes', function () {
    $lpSource = LawPolicySource::factory()->create([
        'name' => 'LP 2',
        'type' => null,
        'jurisdiction' => 'CA',
        'year_in_effect' => null,
    ]);

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.law-policy-source-selection :lawPolicySources="$lawPolicySources" />',
            ['lawPolicySources' => $lawPolicySources]
        );

    $toSee = [
        '<ul role="list">',
        '<li>',
        '<h3>',
        'Canada',
        '<ul role="list">',
        '<li>',
        '<h4>',
        'Federal',
        '<ul role="list">',
        '<li>',
        '<h5>',
        $lpSource->name,
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpSource->id.']" id="lawPolicySources['.$lpSource->id.']"',
        'value="1"',
        '<label for="lawPolicySources['.$lpSource->id.']">',
        'Add to regime assessment',
        '<dl>',
        '<dt>Jurisdiction</dt>',
        '<dd>Canada</dd>',
        '<dt>Provisions</dt>',
        "<dd>{$lpSource->provisions->count()}</dd>",
    ];

    $dontSee = [
        '<dt>Year in Effect</dt>',
        '<dt>Type</dt>',
    ];

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('render LP Selections - level', function () {
    LawPolicySource::factory()->create();

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.law-policy-source-selection :lawPolicySources="$lawPolicySources" :level="$level" />',
            [
                'lawPolicySources' => $lawPolicySources,
                'level' => 2,
            ]
        );

    $toSee = [
        '<h2>',
        '<h3>',
        '<h4>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render LP Selections - empty', function () {
    LawPolicySource::factory()->create();

    $lawPolicySources = LawPolicySource::where('id', 'missing')->get()->all();

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.law-policy-source-selection :lawPolicySources="$lawPolicySources" />',
            ['lawPolicySources' => $lawPolicySources]
        );

    $view->assertDontSee('<li>', false);
});
