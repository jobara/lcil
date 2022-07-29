<?php

use App\Models\Measure;
use App\Models\MeasureDimension;
use App\Models\MeasureIndicator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(ConstantMeasureSeeder::class);
});

test('index route', function () {
    $response = $this->get(localized_route('measures'));

    $response->assertStatus(200);
    $response->assertViewIs('measures.index');
    $response->assertViewHas('lcilMeasures');

    foreach ($response['lcilMeasures'] as $dimension) {
        expect($dimension)->toBeInstanceOf(MeasureDimension::class);

        foreach ($dimension->indicators as $indicator) {
            expect($indicator)->toBeInstanceOf(MeasureIndicator::class);

            foreach ($indicator->measures as $measure) {
                expect($measure)->toBeInstanceOf(Measure::class);
            }
        }
    }
});

test('index route render', function () {
    $lcilMeasures = MeasureDimension::factory(2)
        ->has(MeasureIndicator::factory(2)->has(Measure::factory(2), 'measures'), 'indicators')
        ->create();
    $toSee = [
        '<title>Measures &mdash; Legal Capacity Inclusion Lens</title>',
        '<h1 itemprop="name">Legal Capacity Inclusion Lens: Measures</h1>',
    ];

    foreach ($lcilMeasures as $dimension) {
        $toSee[] = $dimension['code'];
        $toSee[] = $dimension['description'];

        foreach ($dimension->indicators as $indicator) {
            $toSee[] = $indicator['code'];
            $toSee[] = $indicator['description'];

            foreach ($indicator->measures as $measure) {
                $toSee[] = $measure['code'];
                $toSee[] = $measure['title'];
                $toSee[] = $measure['type'];
                $toSee[] = $measure['description'];
            }
        }
    }

    expect($toSee)->toHaveCount(46); // ensures that correct number of strings were found from $lcilMeasures

    $response = $this->get(localized_route('measures'));
    $response->assertSeeInOrder($toSee, false);
});

test('measure model relationships', function () {
    $dimension = MeasureDimension::factory()->create();
    $indicator = MeasureIndicator::factory()->for($dimension, 'dimension')->create();
    $measure = Measure::factory()->for($indicator, 'indicator')->create();

    // Dimension relationships
    expect($dimension->indicators)->toHaveCount(1);
    expect($dimension->indicators->contains($indicator))->toBeTrue();

    // Indicator relationships
    expect($indicator->measures)->toHaveCount(1);
    expect($indicator->dimension->is($dimension))->toBeTrue();
    expect($indicator->measures->contains($measure))->toBeTrue();

    // Measure relationships
    expect($measure->indicator->is($indicator))->toBeTrue();
});
