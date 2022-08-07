<?php

test('render', function () {
    $view = $this->blade('<x-heading>Test heading</x-heading>');

    $toSee = [
        '<h3>',
        'Test heading',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - level', function () {
    $view = $this->blade(
        '<x-heading :level="$level">Test heading</x-heading>',
        [
            'level' => 4,
        ]
    );

    $toSee = [
        '<h4>',
        'Test heading',
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<h3>', false);
});

test('render - level above 1', function () {
    $view = $this->blade(
        '<x-heading :level="$level">Test heading</x-heading>',
        [
            'level' => 0,
        ]
    );

    $toSee = [
        '<h1>',
        'Test heading',
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<h3>', false);
    $view->assertDontSee('<h0>', false);
});

test('render - level below 6', function () {
    $view = $this->blade(
        '<x-heading :level="$level">Test heading</x-heading>',
        [
            'level' => 7,
        ]
    );

    $toSee = [
        '<h6>',
        'Test heading',
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<h3>', false);
    $view->assertDontSee('<h7>', false);
});
