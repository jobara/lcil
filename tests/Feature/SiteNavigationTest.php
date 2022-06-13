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
    $response->assertSee('<a aria-current="page" href="' . localized_route('lawPolicySources.index'), false);
    $response->assertDontSee('<a aria-current="page" href="' . localized_route('regimeAssessments.index'), false);
    $response->assertDontSee('<a aria-current="page" href="' . localized_route('about'), false);
});

// TODO: uncomment when Regime Assessments page has been created
// test('render - current page: Regime Assessments', function () {
//     $response = $this->get(localized_route('regimeAssessments.index'));

//     $response->assertStatus(200);
//     $response->assertViewIs('regimeAssessments.index');
//     $response->assertDontSee('<a aria-current="page" href="' . localized_route('lawPolicySources.index'), false);
//     $response->assertSee('<a aria-current="page" href="' . localized_route('regimeAssessments.index'), false);
//     $response->assertDontSee('<a aria-current="page" href="' . localized_route('about'), false);
// });

// TODO: uncomment when About page has been created
// test('render - current page: About', function () {
//     $response = $this->get(localized_route('about'));

//     $response->assertStatus(200);
//     $response->assertViewIs('about');
//     $response->assertDontSee('<a aria-current="page" href="' . localized_route('lawPolicySources.index'), false);
//     $response->assertDontSee('<a aria-current="page" href="' . localized_route('regimeAssessments.index'), false);
//     $response->assertSee('<a aria-current="page" href="' . localized_route('about'), false);
// });

test('render - current page: Landing Page', function () {
    $response = $this->get(localized_route('welcome'));

    $response->assertStatus(200);
    $response->assertViewIs('welcome');
    $response->assertDontSee('<a aria-current="page" href="' . localized_route('lawPolicySources.index'), false);
    $response->assertDontSee('<a aria-current="page" href="' . localized_route('regimeAssessments.index'), false);
    $response->assertDontSee('<a aria-current="page" href="' . localized_route('about'), false);
});

/*

<!-- Site Navigation -->
<nav aria-label="{{ __('Site Navigation') }}">

    <!-- Navigation Links -->
    <ul role="list">
        <x-nav-link :href="localized_route('lawPolicySources.index')" :active="request()->routeIs(locale() . '.lawPolicySources.index')">
            {{ __('Law and Policy Sources') }}
        </x-nav-link>
        <x-nav-link :href="localized_route('regimeAssessments.index')" :active="request()->routeIs(locale() . '.regimeAssessments.index')">
            {{ __('Regime Assessments') }}
        </x-nav-link>
        <x-nav-link :href="localized_route('about')" :active="request()->routeIs(locale() . '.about')">
            {{ __('About') }}
        </x-nav-link>
    </ul>
</nav>

 */
