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
