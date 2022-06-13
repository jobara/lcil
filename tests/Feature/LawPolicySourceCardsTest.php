<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('render cards', function () {
    LawPolicySource::factory(3)->create([
        'name' => $this->faker->shuffle('lcil-card-test'),
    ]);

    $lawPolicySources = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-law-policy-source-cards :lawPolicySources="$lawPolicySources" />',
        ['lawPolicySources' => $lawPolicySources]
    );

    $toSee = [
        '<ul>',
        '<li>',
    ];

    foreach ($lawPolicySources as $lawPolicySource) {
        $toSee[] = $lawPolicySource->name;
    }

    $view->assertSeeInOrder($toSee, false);
});

test('render cards - level', function () {
    LawPolicySource::factory()->create();

    $lawPolicySources = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-law-policy-source-cards :lawPolicySources="$lawPolicySources" :level="$level" />',
        [
            'lawPolicySources' => $lawPolicySources,
            'level' => 3,
        ]
    );

    $view->assertSee('<h3>', false);
    $view->assertDontSee('<h4>', false);
});

test('render - empty', function () {
    LawPolicySource::factory()->create();

    $lawPolicySources = LawPolicySource::where('id', 'missing')->paginate(10);

    $view = $this->blade(
        '<x-law-policy-source-cards :lawPolicySources="$lawPolicySources" />',
        ['lawPolicySources' => $lawPolicySources]
    );

    $view->assertDontSee('<ul>', false);
    $view->assertDontSee('<li>', false);
});
