<?php

test('render - no errors', function () {
    $view = $this->withViewErrors([])
                 ->blade('<x-forms.error-summary />');

    $view->assertDontSee('<div role="alert">', false);
});

test('render - errors', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
        'country' => 'The country must be at least 2 characters.',
    ])->blade('<x-forms.error-summary />');

    $toSee = [
        '<div id="error-summary" role="alert">',
        '<p id="error-summary__message">Please check the following fields in order to proceed:</p>',
        '<li><a href="#name">The name field is required.</a></li>',
        '<li><a href="#country">The country must be at least 2 characters.</a></li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - custom id', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
    ])->blade('<x-forms.error-summary id="custom" />');

    $toSee = [
        '<div id="custom" role="alert">',
        '<p id="custom__message">Please check the following fields in order to proceed:</p>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - custom role', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
    ])->blade('<x-forms.error-summary role="status" />');

    $toSee = [
        '<div id="error-summary" role="status">',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - custom message', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
    ])->blade(
        '<x-forms.error-summary :message="$message" />',
        ['message' => 'Test message']
    );

    $toSee = [
        '<div id="error-summary" role="alert">',
        '<p id="error-summary__message">Test message</p>',
    ];

    $view->assertSeeInOrder($toSee, false);
});
