<?php

use App\Enums\LawPolicyTypes;
use App\Models\LawPolicySource;
use App\Models\Provision;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('render - all values', function () {
    $lawPolicySource = LawPolicySource::factory()
        ->has(Provision::factory()->count(3))
        ->create([
            'jurisdiction' => 'CA-ON',
            'municipality' => null,
            'name' => 'test-lawPolicySource',
            'type' => LawPolicyTypes::CaseLaw,
        ]);

    $view = $this->blade(
        '<x-law-policy-source-card :lawPolicySource="$lawPolicySource" />',
        ['lawPolicySource' => $lawPolicySource]
    );

    $url = localized_route('lawPolicySources.show', $lawPolicySource->slug);

    $view->assertSee('<h4>', false);
    $view->assertSee("<a href=\"{$url}\">{$lawPolicySource->name}</a>", false);
    $view->assertSee('<dt>Jurisdiction</dt>', false);
    $view->assertSee('<dd>Ontario, Canada</dd>', false);
    $view->assertSee('<dt>Year in Effect</dt>', false);
    $view->assertSee("<dd>{$lawPolicySource->year_in_effect}</dd>", false);
    $view->assertSee('<dt>Type</dt>', false);
    $view->assertSee("<dd>{$lawPolicySource->type->value}</dd>", false);
    $view->assertSee('<dt>Provisions</dt>', false);
    $view->assertSee('<dd>3</dd>', false);
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
