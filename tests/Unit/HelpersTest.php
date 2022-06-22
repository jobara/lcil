<?php

// get_jurisdiction_name() tests

use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

test('get_jurisdiction_name: country name', function () {
    $jurisdiction = get_jurisdiction_name('CA');
    expect($jurisdiction)->toBe('Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: country does not exist', function () {
    $jurisdiction = get_jurisdiction_name('invalid');
    expect($jurisdiction)->toBeNull();
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: country does not exist - with subdivision code', function () {
    $jurisdiction = get_jurisdiction_name('XX-ON');
    expect($jurisdiction)->toBeNull();
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: country name in requested locale', function () {
    $jurisdiction = get_jurisdiction_name('US', locale: 'fr-CA');
    expect($jurisdiction)->toBe('États-Unis');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: ignore municipality if no subdivision', function () {
    $jurisdiction = get_jurisdiction_name('CA', 'Toronto');
    expect($jurisdiction)->toBe('Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: subdivision name', function () {
    $jurisdiction = get_jurisdiction_name('CA-ON');
    expect($jurisdiction)->toBe('Ontario, Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: subdivision name in requested locale', function () {
    $jurisdiction = get_jurisdiction_name('BR-AP', locale: 'pt-BR');
    expect($jurisdiction)->toBe('Amapá, Brasil');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: ignore missing/invalid subdivision name', function () {
    $jurisdiction = get_jurisdiction_name('CA-XX');
    expect($jurisdiction)->toBe('Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: municipality name', function () {
    $jurisdiction = get_jurisdiction_name('CA-ON', 'Toronto');
    expect($jurisdiction)->toBe('Toronto, Ontario, Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: uppercase municipality name', function () {
    $jurisdiction = get_jurisdiction_name('CA-ON', 'toronto');
    expect($jurisdiction)->toBe('Toronto, Ontario, Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: ignore municipality if subdivision is missing', function () {
    $jurisdiction = get_jurisdiction_name('CA', 'Toronto');
    expect($jurisdiction)->toBe('Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: ignore municipality if subdivision is invalid', function () {
    $jurisdiction = get_jurisdiction_name('CA-XX', 'Toronto');
    expect($jurisdiction)->toBe('Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: jurisdiction name with custom separator', function () {
    $jurisdiction = get_jurisdiction_name('CA-ON', 'toronto', separator: '_');
    expect($jurisdiction)->toBe('Toronto_Ontario_Canada');
})->group('get_jurisdiction_name');

test('get_jurisdiction_name: ignore additional code segments', function () {
    $jurisdiction = get_jurisdiction_name('CA-ON-BC');
    expect($jurisdiction)->toBe('Ontario, Canada');
})->group('get_jurisdiction_name');

// get_countries() tests

test('get_countries: list of available countries', function () {
    $countryRepository = new CountryRepository();
    $countries = get_countries();
    expect($countries)->toBe($countryRepository->getList());
})->group('get_countries');

test('get_countries: list of available countries in the requested locale', function () {
    $countryRepository = new CountryRepository();
    $locale = 'fr-CA';
    $countries = get_countries($locale);
    expect($countries)->toBe($countryRepository->getList($locale));
})->group('get_countries');

// get_subdivisions() tests

test('get_subdivisions: list of available subdivisions', function () {
    $subdivisionRepository = new SubdivisionRepository();
    $countryCode = 'CA';
    $subdivisions = get_subdivisions($countryCode);
    expect($subdivisions)->toBe($subdivisionRepository->getList([$countryCode]));
})->group('get_subdivisions');

test('get_subdivisions: list of available subdivisions in the requested locale', function () {
    $subdivisionRepository = new SubdivisionRepository();
    $countryCode = 'CA';
    $locale = 'fr-CA';
    $subdivisions = get_subdivisions($countryCode, $locale);
    expect($subdivisions)->toBe($subdivisionRepository->getList([$countryCode], $locale));
})->group('get_subdivisions');

test('get_subdivisions: no country found', function () {
    $subdivisions = get_subdivisions('INVALID');
    expect($subdivisions)->toBe([]);
})->group('get_subdivisions');

test('get_subdivisions: no country provided', function () {
    $subdivisions = get_subdivisions();
    expect($subdivisions)->toBe([]);
})->group('get_subdivisions');

// group_by_jurisdiction() tests

test('group_by_jurisdiction: no arguments', function () {
    $groupedItems = group_by_jurisdiction();
    expect($groupedItems)->toBe([]);
})->group('group_by_jurisdiction');

test('group_by_jurisdiction: group items', function () {
    $items = [
        (object) ['jurisdiction' => 'CA'],
        (object) ['jurisdiction' => 'CA-AB', 'item' => 1],
        (object) ['jurisdiction' => 'CA-AB', 'item' => 2],
        (object) ['jurisdiction' => 'CA-ON'],
        (object) ['jurisdiction' => 'US'],
        (object) ['jurisdiction' => 'US-NY'],
    ];

    $expected = [
        'Canada' => [
            '' => [
                $items[0],
            ],
            'Alberta' => [
                $items[1],
                $items[2],
            ],
            'Ontario' => [
                $items[3],
            ],
        ],
        'United States' => [
            '' => [
                $items[4],
            ],
            'New York' => [
                $items[5],
            ],
        ],
    ];

    $groupedItems = group_by_jurisdiction($items);
    expect($groupedItems)->toMatchArray($expected);
    // expect($groupedItems)->toBe($expected);
})->group('group_by_jurisdiction');

test('group_by_jurisdiction: invalid country', function () {
    $items = [
        (object) ['jurisdiction' => 'INVALID'],
        (object) ['jurisdiction' => 'INVALID-AB'],
        (object) ['jurisdiction' => 'CA-INVALID'],
    ];

    $expected = [
        'Canada' => [
            '' => [
                $items[2],
            ],
        ],
    ];

    $groupedItems = group_by_jurisdiction($items);
    expect($groupedItems)->toBe($expected);
})->group('group_by_jurisdiction');

test('group_by_jurisdiction: with locale', function () {
    $items = [
        (object) ['jurisdiction' => 'BR-SP'],
    ];

    $expected = [
        'Brasil' => [
            'São Paulo' => [
                $items[0],
            ],
        ],
    ];

    $groupedItems = group_by_jurisdiction($items, 'pt-BR');
    expect($groupedItems)->toBe($expected);
})->group('group_by_jurisdiction');

test('clamp', function ($num, $min, $max, $expected) {
    $clamped = clamp($num, $min, $max);
    expect($clamped)->toBe($expected);
})->with([
    'int in range' => [2, 1, 3, 2],
    'int below range' => [0, 1, 3, 1],
    'int above range' => [4, 1, 3, 3],
    'float in range' => [2.5, 0.5, 3.5, 2.5],
    'float below range' => [0.1, 0.5, 3.5, 0.5],
    'float above range' => [4.5, 0.5, 3.5, 3.5],
]);

test('to_associative_array', function ($array, $expected) {
    $converted = to_associative_array($array);
    expect($converted)->toBe($expected);
})->with([
    'string array' => [['foo', 'bar'], ['foo' => 'foo', 'bar' => 'bar']],
    'int array' => [[0, 1, 2], [0 => 0, 1 => 1, 2 => 2]],
])->group('to_associative_array');

test('to_associative_array - string conversion', function ($array, $mode, $expected) {
    $converted = to_associative_array($array, $mode);
    expect($converted)->toBe($expected);
})->with([
    'Upper-case' => [['Hello world'], MB_CASE_UPPER, ['Hello world' => 'HELLO WORLD']],
    'Lower-case' => [['Hello world'], MB_CASE_LOWER, ['Hello world' => 'hello world']],
    'Title-case' => [['Hello world'], MB_CASE_TITLE, ['Hello world' => 'Hello World']],
    'Fold' => [['Hello world'], MB_CASE_FOLD, ['Hello world' => 'hello world']],
    'Simple Upper-case' => [['Hello world'], MB_CASE_UPPER_SIMPLE, ['Hello world' => 'HELLO WORLD']],
    'Simple Lower-case' => [['Hello world'], MB_CASE_LOWER_SIMPLE, ['Hello world' => 'hello world']],
    'Simple Title-case' => [['Hello world'], MB_CASE_TITLE_SIMPLE, ['Hello world' => 'Hello World']],
    'Simple Fold' => [['Hello world'], MB_CASE_FOLD_SIMPLE, ['Hello world' => 'hello world']],
])->group('to_associative_array');

test('to_associative_array - string encoding', function () {
    $converted = to_associative_array(['Hello World'], MB_CASE_LOWER, 'UTF-8');
    expect($converted)->toBe(['Hello World' => 'hello world']);
})->group('to_associative_array');
