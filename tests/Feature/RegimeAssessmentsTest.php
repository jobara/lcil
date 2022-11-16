<?php

use App\Models\Evaluation;
use App\Models\LawPolicySource;
use App\Models\Measure;
use App\Models\MeasureDimension;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('model relationships', function () {
    $measure = Measure::factory()->create();
    $measureDimension = MeasureDimension::all()->first();
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_of_assessment' => 2022,
    ]);

    $lawPolicySource = LawPolicySource::factory()->create([
        'jurisdiction' => $regimeAssessment->jurisdiction,
        'municipality' => $regimeAssessment->municipality,
    ]);

    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create();

    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $evaluation = Evaluation::factory()
        ->for($regimeAssessment)
        ->for($provision)
        ->for($measure)
        ->create([
            'assessment' => 'fully',
        ]);

    // Regime Assessment relationships
    expect($regimeAssessment->lawPolicySources)->toHaveCount(1);
    expect($regimeAssessment->lawPolicySources->contains($lawPolicySource))->toBeTrue();
    expect($regimeAssessment->evaluations)->toHaveCount(1);
    expect($regimeAssessment->evaluations->contains($evaluation))->toBeTrue();

    // Evaluation relationships
    expect($evaluation->regimeAssessment->is($regimeAssessment))->toBeTrue();
    expect($evaluation->provision->is($provision))->toBeTrue();
    expect($evaluation->measure->is($measure))->toBeTrue();

    // LawPolicySource relationships
    expect($lawPolicySource->regimeAssessments)->toHaveCount(1);
    expect($lawPolicySource->regimeAssessments->contains($regimeAssessment))->toBeTrue();

    // Provision relationships
    expect($provision->evaluations)->toHaveCount(1);
    expect($provision->evaluations->contains($evaluation))->toBeTrue();

    // Measure relationships
    expect($measure->evaluations)->toHaveCount(1);
    expect($measure->evaluations->contains($evaluation))->toBeTrue();
})->group('RegimeAssessments');
