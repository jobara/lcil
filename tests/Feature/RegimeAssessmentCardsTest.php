<?php

use App\Models\RegimeAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('render cards', function () {
    RegimeAssessment::factory(3)->create();

    $regimeAssessments = RegimeAssessment::paginate(10);

    $view = $this->blade(
        '<x-regime-assessment-cards :regimeAssessments="$regimeAssessments" />',
        ['regimeAssessments' => $regimeAssessments]
    );

    $toSee = [
        '<ul role="list"',
        '<li class="card">',
    ];

    foreach ($regimeAssessments as $regimeAssessment) {
        $toSee[] = '<h4>';
        $toSee[] = get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality);
    }

    $view->assertSeeInOrder($toSee, false);
});

test('render cards - level', function () {
    RegimeAssessment::factory()->create();

    $regimeAssessments = RegimeAssessment::paginate(10);

    $view = $this->blade(
        '<x-regime-assessment-cards :regimeAssessments="$regimeAssessments" :level="$level" />',
        [
            'regimeAssessments' => $regimeAssessments,
            'level' => 3,
        ]
    );

    $view->assertSee('<h3>', false);
    $view->assertDontSee('<h4>', false);
});

test('render - empty', function () {
    RegimeAssessment::factory()->create();

    $regimeAssessments = RegimeAssessment::where('id', 'missing')->paginate(10);

    $view = $this->blade(
        '<x-regime-assessment-cards :regimeAssessments="$regimeAssessments" />',
        ['regimeAssessments' => $regimeAssessments]
    );

    $view->assertDontSee('<ul>', false);
    $view->assertDontSee('<li>', false);
});
