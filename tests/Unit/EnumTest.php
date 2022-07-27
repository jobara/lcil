<?php

use Spatie\LaravelOptions\Options;

uses(Tests\TestCase::class);

test('Values trait', function ($enum, $expectedLabels) {
    $values = $enum::values();
    expect($values)->toEqual(array_keys($expectedLabels));

    $labels = $enum::labels();
    expect($labels)->toBe($expectedLabels);

    $options = $enum::options();
    expect($options)->toBeInstanceOf(Options::class);
    expect($options->toArray())->toEqual(Options::forArray($expectedLabels)->toArray());

})->with('enums');
