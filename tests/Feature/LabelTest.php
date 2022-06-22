<?php

test('default render', function () {
    $view = $this->blade('<x-forms.label for="test"/>');

    $view->assertSee('id="test-label"', false);
    $view->assertSee('for="test"', false);
});

test('render value', function () {
    $view = $this->blade(
        '<x-forms.label for="test" :value="$value" />',
        ['value' => 'Test Label']
    );

    $view->assertSee('id="test-label"', false);
    $view->assertSee('for="test"', false);
    $view->assertSee('Test Label</label>', false);
});

test('render value from slot', function () {
    $view = $this->blade('<x-forms.label for="test">Label in slot</x-forms.label>');

    $view->assertSee('id="test-label"', false);
    $view->assertSee('for="test"', false);
    $view->assertSee('Label in slot</label>', false);
});

test('render without for attribute', function () {
    $view = $this->blade('<x-forms.label />');

    $view->assertDontSee('<label>"', false);
});
