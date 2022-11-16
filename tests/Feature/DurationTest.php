<?php

test('default render', function () {
    $view = $this->blade('<x-duration />');

    $toSee = [
        '<span  x-data="',
        htmlentities('duration({"unitText":{"years":{"singular":"year","plural":"years"},"months":{"singular":"month","plural":"months"},"days":{"singular":"day","plural":"days"},"hours":{"singular":"hour","plural":"hours"},"minutes":{"singular":"minute","plural":"minutes"},"seconds":{"singular":"second","plural":"seconds"}}})'),
        '<time x-bind:datetime="duration.iso">',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render slot', function () {
    $view = $this->blade('<x-duration>Test</x-duration>');

    $toSee = [
        '<span  x-data="',
        '<time x-bind:datetime="duration.iso">',
        'Test',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render slot and duration', function () {
    $view = $this->blade('<x-duration>Duration: {!! $component->getDurationMarkup() !!}</x-duration>');

    $toSee = [
        '<span  x-data="',
        '<time x-bind:datetime="duration.iso">',
        'Duration: <span x-text="duration.text"></span>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render options', function () {
    $view = $this->blade(
        '<x-duration :options="$options" />',
        [
            'options' => [
                'delay' => 1000,
                'unitText' => [
                    'milliseconds' => 'ms',
                ],
            ],
        ]
    );

    $toSee = [
        '<span  x-data="',
        htmlentities('duration({"unitText":{"milliseconds":"ms"},"delay":1000})'),
        '<time x-bind:datetime="duration.iso">',
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee(htmlentities('duration({"unitText":{"years":{"singular":"year","plural":"years"},"months":{"singular":"month","plural":"months"},"days":{"singular":"day","plural":"days"},"hours":{"singular":"hour","plural":"hours"},"minutes":{"singular":"minute","plural":"minutes"},"seconds":{"singular":"second","plural":"seconds"}}})'), false);
});
