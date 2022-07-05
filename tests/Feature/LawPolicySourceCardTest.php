<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('render - all values - count provisions', function () {
    $lawPolicySource = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
            'year_in_effect' => 2022,
            'name' => 'test-lawPolicySource',
            'type' => LawPolicyTypes::CaseLaw,
        ]);

    $provisions = $lawPolicySource->provisions->sortBy('section');

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $url = localized_route('lawPolicySources.show', $lawPolicySource->slug);

    $toSee = [
        '<h4>',
        "<a href=\"{$url}\">{$lawPolicySource->name}</a>",
        '<dt>Jurisdiction</dt>',
        '<dd>Ontario, Canada</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lawPolicySource->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lawPolicySource->type->value}</dd>",
        '<dt>Provisions</dt>',
        '<dd>3</dd>',
    ];

    $dontSee = [
        'Reference',
        '<h5>Provisions</h5>',
        $provisions[0]->section,
        $provisions[1]->section,
        $provisions[2]->section,
    ];

    $view->assertSeeInOrder($toSee, false);
    foreach ($dontSee as $value) {
        $view->assertDontSee($value, false);
    }
});

test('render - all values - list provisions', function () {
    $lawPolicySource = LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
            'year_in_effect' => 2022,
            'name' => 'test-lawPolicySource',
            'type' => LawPolicyTypes::CaseLaw,
            'reference' => 'http://example.com',
        ]);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 2]);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 'ab 3']);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => '10']);

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" expanded />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $url = localized_route('lawPolicySources.show', $lawPolicySource->slug);

    $toSee = [
        '<h4>',
        "<a href=\"{$url}\">{$lawPolicySource->name}</a>",
        '<dt>Jurisdiction</dt>',
        '<dd>Ontario, Canada</dd>',
        '<dt>Year in Effect</dt>',
        "<dd>{$lawPolicySource->year_in_effect}</dd>",
        '<dt>Type</dt>',
        "<dd>{$lawPolicySource->type->value}</dd>",
        'Reference',
        '<h5>Provisions</h5>',
        '2',
        '10',
        'ab 3',
    ];

    $dontSee = [
        '<dt>Provisions</dt>',
        '<dd>3</dd>',
    ];

    $view->assertSeeInOrder($toSee, false);
    foreach ($dontSee as $value) {
        $view->assertDontSee($value, false);
    }
});

test('render - level', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" :level="$level" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'level' => 2,
        ]
    );

    $view->assertSee('<h2>', false);
    $view->assertDontSee('<h4>', false);
});

test('render - level above 1', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" :level="$level" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'level' => 0,
        ]
    );

    $view->assertSee('<h1>', false);
    $view->assertDontSee('<h4>', false);
    $view->assertDontSee('<h0>', false);
});

test('render - level below 6', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" :level="$level" />',
        [
            'lawPolicySource' => $lawPolicySource,
            'level' => 7,
        ]
    );

    $view->assertSee('<h6>', false);
    $view->assertDontSee('<h4>', false);
    $view->assertDontSee('<h7>', false);
});

test('render - level with expansion', function () {
    $lawPolicySource = LawPolicySource::factory()->create();

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" :level="$level" expanded />',
        [
            'lawPolicySource' => $lawPolicySource,
            'level' => 2,
        ]
    );

    $view->assertSee('<h2>', false);
    $view->assertSee('<h3>', false);
    $view->assertDontSee('<h4>', false);
    $view->assertDontSee('<h5>', false);
});

test('render - without year', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'year_in_effect' => null,
    ]);

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $view->assertDontSee('<dt>Year in Effect</dt>', false);
});

test('render - without type', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'type' => null,
    ]);

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $view->assertDontSee('<dt>Type</dt>', false);
});

test('render - without reference', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'reference' => null,
    ]);

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" expanded />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $view->assertDontSee('Reference', false);
});

test('render - list provisions as guest', function () {
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

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" expanded />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $toSee = [
        '2',
        '10',
        'ab 3',
    ];

    $dontSee = [
        '2</a>',
        '10</a>',
        'ab 3</a>',
    ];

    $view->assertSeeInOrder($toSee, false);
    foreach ($dontSee as $value) {
        $view->assertDontSee($value, false);
    }
});

test('render - list provisions as authenticated', function () {
    $user = User::factory()->create();
    $lawPolicySource = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create();

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 2]);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => 'ab 3']);

    Provision::factory()
        ->for($lawPolicySource)
        ->create(['section' => '10']);

    $view = $this->actingAs($user)->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" expanded />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $toSee = [
        '2</a>',
        '10</a>',
        'ab 3</a>',
    ];

    $view->assertSeeInOrder($toSee, false);
});
