<?php

test('render', function () {
    $view = $this->blade('<x-site-navigation />');

    $toSee = [
        '<nav aria-label="Site Navigation">',
        localized_route('lawPolicySources.index'),
        'Law and Policy Sources',
        localized_route('regimeAssessments.index'),
        'Regime Assessments',
        localized_route('about'),
        'About',
    ];

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('aria-current="page"', false);
});

test('render - current page: Law Policy Source', function () {
    $response = $this->get(localized_route('lawPolicySources.index'));

    $response->assertStatus(200);
    $response->assertViewIs('lawPolicySources.index');
    $response->assertSee('<a aria-current="page" href="'.localized_route('lawPolicySources.index'), false);
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('regimeAssessments.index'), false);
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('about'), false);
});

test('render - current page: Regime Assessments', function () {
    $response = $this->get(localized_route('regimeAssessments.index'));

    $response->assertStatus(200);
    $response->assertViewIs('regimeAssessments.index');
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('lawPolicySources.index'), false);
    $response->assertSee('<a aria-current="page" href="'.localized_route('regimeAssessments.index'), false);
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('about'), false);
});

test('render - current page: About', function () {
    $response = $this->get(localized_route('about'));

    $response->assertStatus(200);
    $response->assertViewIs('about');
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('lawPolicySources.index'), false);
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('regimeAssessments.index'), false);
    $response->assertSee('<a aria-current="page" href="'.localized_route('about'), false);
});

test('render - current page: Landing Page', function () {
    $response = $this->get(localized_route('welcome'));

    $response->assertStatus(200);
    $response->assertViewIs('welcome');
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('lawPolicySources.index'), false);
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('regimeAssessments.index'), false);
    $response->assertDontSee('<a aria-current="page" href="'.localized_route('about'), false);
});
