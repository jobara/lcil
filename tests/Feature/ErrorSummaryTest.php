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
        '<div id="error-summary" role="alert" class="error-summary">',
        '<p id="error-summary__message">',
        '<svg',
        'Please check the following fields in order to proceed:',
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
        '<div id="custom" role="alert" class="error-summary">',
        '<p id="custom__message">',
        '<svg',
        'Please check the following fields in order to proceed:',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - custom class', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
    ])->blade('<x-forms.error-summary class="custom" />');

    $toSee = [
        '<div id="error-summary" role="alert" class="error-summary custom">',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - custom role', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
    ])->blade('<x-forms.error-summary role="status" />');

    $toSee = [
        '<div id="error-summary" role="status" class="error-summary">',
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
        '<div id="error-summary" role="alert" class="error-summary">',
        '<p id="error-summary__message">',
        '<svg',
        'Test message',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - custom anchor', function () {
    $view = $this->withViewErrors([
        'name' => 'The name field is required.',
        'country' => 'The country must be at least 2 characters.',
    ])->blade(
        '<x-forms.error-summary :anchors="$anchors"/>',
        ['anchors' => ['name' => 'test-name']]
    );

    $toSee = [
        '<div id="error-summary" role="alert" class="error-summary">',
        '<p id="error-summary__message">',
        '<svg',
        'Please check the following fields in order to proceed:',
        '<li><a href="#test-name">The name field is required.</a></li>',
        '<li><a href="#country">The country must be at least 2 characters.</a></li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - sanitized', function () {
    $view = $this->withViewErrors([
        'name.first' => 'The name field is required.',
        'other' => 'The other field is required.',
    ])->blade(
        '<x-forms.error-summary :anchors="$anchors"/>',
        ['anchors' => ['name.first' => true]]
    );

    $toSee = [
        '<div id="error-summary" role="alert" class="error-summary">',
        '<p id="error-summary__message">',
        '<svg',
        'Please check the following fields in order to proceed:',
        '<li><a href="#namefirst">The name field is required.</a></li>',
        '<li><a href="#other">The other field is required.</a></li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - wildcards', function () {
    $view = $this->withViewErrors([
        'name.1.first' => 'The name field is required.',
        'name.2.first' => 'The name field is required.',
        'other' => 'The other field is required.',
    ])->blade(
        '<x-forms.error-summary :anchors="$anchors"/>',
        ['anchors' => ['name.*.first' => true]]
    );

    $toSee = [
        '<div id="error-summary" role="alert" class="error-summary">',
        '<p id="error-summary__message">',
        '<svg',
        'Please check the following fields in order to proceed:',
        '<li><a href="#name1first">The name field is required.</a></li>',
        '<li><a href="#name2first">The name field is required.</a></li>',
        '<li><a href="#other">The other field is required.</a></li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});
