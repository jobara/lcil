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
        '<div role="alert">',
        '<li><a href="#name-label">The name field is required.</a></li>',
        '<li><a href="#country-label">The country must be at least 2 characters.</a></li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});
