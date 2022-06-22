<?php

test('default render', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select />'
    );

    $view->assertSee('id="country"', false);
    $view->assertSee('name="country"', false);
    $view->assertSee('<option value="" selected></option>', false);
    $view->assertSee('<option value="CA" >Canada</option>', false);
    $view->assertSee('<option value="US" >United States</option>', false);
});

test('render with name data', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select :name="$name"/>',
        ['name' => 'test']
    );

    $view->assertSee('id="test"', false);
    $view->assertSee('name="test"', false);
});

test('render with name data and custom id', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select :name="$name" id="other"/>',
        ['name' => 'test']
    );

    $view->assertSee('id="other"', false);
    $view->assertSee('name="test"', false);
});

test('render with placeholder', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select :placeholder="$placeholder" />',
        ['placeholder' => 'Default Option']
    );

    $view->assertSee('<option value="" selected>Default Option</option>', false);
    $view->assertDontSee('<option value="" selected></option>', false);
});

test('render with country data - empty string', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select :country="$country"/>',
        ['country' => '']
    );

    $view->assertSee('<option value="" selected></option>', false);
});

test('render with country data - country code', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select :country="$country"/>',
        ['country' => 'CA']
    );

    $view->assertDontSee('<option value="" selected></option>', false);
    $view->assertSee('<option value="CA" selected>Canada</option>', false);
});

test('render with country data - invalid', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-country-select :country="$country"/>',
        ['country' => 'INVALID']
    );

    $view->assertSee('<option value="" selected></option>', false);
});
