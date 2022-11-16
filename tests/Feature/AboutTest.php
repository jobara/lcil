<?php

test('about page', function () {
    $response = $this->get(localized_route('about'));

    $response->assertStatus(200);
    $response->assertViewIs('about');
});

test('about page render', function () {
    $toSee = [
        '<title>About &mdash; Legal Capacity Inclusion Lens</title>',
        '<h1 itemprop="name">About</h1>',
    ];

    $view = $this->view('about');

    $view->assertSeeInOrder($toSee, false);
});
