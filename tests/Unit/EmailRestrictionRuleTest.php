<?php

use App\Rules\EmailRestriction;

uses(Tests\TestCase::class);

test('email restriction validation', function ($config, $expected) {
    $passed = true;
    $fail = function () use (&$passed) {
        $passed = false;
    };

    config($config);

    $rule = new EmailRestriction();
    $rule('email', 'test@example.com', $fail);

    expect($passed)->toBe($expected);
})->with('emailRestrictions');
