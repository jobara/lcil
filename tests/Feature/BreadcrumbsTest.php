<?php

test('render', function () {
    $breadcrumbs = collect([
        (object) ['title' => 'parent', 'url' => '/'],
        (object) ['title' => 'child'],
    ]);

    $view = $this->blade(
        '<x-breadcrumbs :breadcrumbs="$breadcrumbs" />',
        ['breadcrumbs' => $breadcrumbs]
    );

    $toSee = [
        '<nav class="breadcrumbs" aria-label="Breadcrumbs">',
        '<li><a href="/">parent</a></li>',
        '<li  aria-current="page" >child</li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - empty', function () {
    $breadcrumbs = collect();

    $view = $this->blade(
        '<x-breadcrumbs :breadcrumbs="$breadcrumbs" />',
        ['breadcrumbs' => $breadcrumbs]
    );

    $view->assertDontSee('<nav class="breadcrumbs" aria-label="Breadcrumbs">', false);
    $view->assertDontSee('aria-current="page"', false);
});

test('render - url missing in middle', function () {
    $breadcrumbs = collect([
        (object) ['title' => 'parent', 'url' => '/'],
        (object) ['title' => 'intermediate'],
        (object) ['title' => 'child'],
    ]);

    $view = $this->blade(
        '<x-breadcrumbs :breadcrumbs="$breadcrumbs" />',
        ['breadcrumbs' => $breadcrumbs]
    );

    $toSee = [
        '<nav class="breadcrumbs" aria-label="Breadcrumbs">',
        '<li><a href="/">parent</a></li>',
        '<li >intermediate</li>',
        '<li  aria-current="page" >child</li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - url not rendered for last segment', function () {
    $breadcrumbs = collect([
        (object) ['title' => 'parent', 'url' => '/'],
        (object) ['title' => 'child', 'url' => '/child'],
    ]);

    $view = $this->blade(
        '<x-breadcrumbs :breadcrumbs="$breadcrumbs" />',
        ['breadcrumbs' => $breadcrumbs]
    );

    $toSee = [
        '<nav class="breadcrumbs" aria-label="Breadcrumbs">',
        '<li><a href="/">parent</a></li>',
        '<li  aria-current="page" >child</li>',
    ];

    $view->assertSeeInOrder($toSee, false);
});
