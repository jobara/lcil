<?php

use App\Http\Resources\MeasureResource;
use App\Models\Measure;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Resource', function () {
    $measure = Measure::factory()->create();
    $measureResource = new MeasureResource($measure);

    expect($measureResource->toJson())
        ->json()
        ->toHaveCount(8)
        ->id->toBe($measure->id)
        ->code->toBe($measure->code)
        ->description->toBe($measure->description)
        ->title->toBe($measure->title)
        ->type->toBe($measure->type)
        ->slug->toBe($measure->slug)
        ->created_at->toBeString()
        ->updated_at->toBeString();
})->group('api');
