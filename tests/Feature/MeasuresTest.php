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
    $strings = [];

    foreach ($lcilMeasures as $dimension) {
        $strings[] = $dimension['code'];
        $strings[] = $dimension['description'];

        foreach ($dimension->indicators as $indicator) {
            $strings[] = $indicator['code'];
            $strings[] = $indicator['description'];

            foreach ($indicator->measures as $measure) {
                $strings[] = $measure['code'];
                $strings[] = $measure['title'];
                $strings[] = $measure['type'];
                $strings[] = $measure['description'];
            }
        }
    }

    expect($strings)->toHaveCount(44); // ensures that correct number of strings were found from $lcilMeasures

    $response = $this->get(localized_route('measures'));
    $response->assertSeeTextInOrder($strings);
});

test('measure model relationships', function () {
    $dimension = MeasureDimension::factory()->create();
    $indicator = MeasureIndicator::factory()->for($dimension, 'dimension')->create();
    $measure = Measure::factory()->for($indicator, 'indicator')->create();

    // Dimension relationships
    expect($dimension->indicators)->toHaveCount(1);
    expect($dimension->indicators[0]->code)->toBe($indicator->code);

    // Indicator relationships
    expect($indicator->measures)->toHaveCount(1);
    expect($indicator->dimension->code)->toBe($dimension->code);
    expect($indicator->measures[0]->code)->toBe($measure->code);

    // Measure relationships
    expect($measure->indicator->code)->toBe($indicator->code);
});
