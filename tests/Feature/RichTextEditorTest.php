<?php

test('default render', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="test" />'
    );

    $toSee = [
        'class="richTextEditor"',
        'id="test-editor"',
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'id\': \'test-editable\'',
        '\'class\': \'richTextEditor__editable\'',
        '\'aria-multiline\': true',
        '\'role\': \'textbox\'',
        'id="test-toolbar"',
        'class="richTextEditor__toolbar"',
        'role="toolbar"',
        'aria-controls="test-editable"',
        '@keyup.home="focusFirst($event.target)"',
        '@keyup.end="focusLast($event.target)"',
        '<button',
        'type="button"',
        '@click="getEditor().chain().toggleBold().focus().run()"',
        '@focus="updateTabindexes($event.target)"',
        '@keyup.right="focusNext($event.target)"',
        'x-bind:aria-pressed="updatedAt && getEditor().isActive(\'bold\')"',
        'aria-label="bold"',
        '<svg',
        '<button',
        'type="button"',
        'tabindex="-1"',
        '@click="getEditor().chain().toggleItalic().focus().run()"',
        '@focus="updateTabindexes($event.target)"',
        '@keyup.right="focusNext($event.target)"',
        '@keyup.left="focusPrev($event.target)"',
        'x-bind:aria-pressed="updatedAt && getEditor().isActive(\'italic\')"',
        'aria-label="italic"',
        '<svg',
        '<button',
        'type="button"',
        'tabindex="-1"',
        '@click="getEditor().chain().toggleUnderline().focus().run()"',
        '@focus="updateTabindexes($event.target)"',
        '@keyup.right="focusNext($event.target)"',
        '@keyup.left="focusPrev($event.target)"',
        'x-bind:aria-pressed="updatedAt && getEditor().isActive(\'underline\')"',
        'aria-label="underline"',
        '<svg',
        '<button',
        'type="button"',
        'tabindex="-1"',
        '@click="getEditor().chain().toggleStrike().focus().run()"',
        '@focus="updateTabindexes($event.target)"',
        '@keyup.right="focusNext($event.target)"',
        '@keyup.left="focusPrev($event.target)"',
        'x-bind:aria-pressed="updatedAt && getEditor().isActive(\'strike\')"',
        'aria-label="strikethrough"',
        '<svg',
        '<button',
        'type="button"',
        'tabindex="-1"',
        '@click="getEditor().chain().toggleBulletList().focus().run()"',
        '@focus="updateTabindexes($event.target)"',
        '@keyup.right="focusNext($event.target)"',
        '@keyup.left="focusPrev($event.target)"',
        'x-bind:aria-pressed="updatedAt && getEditor().isActive(\'bulletList\')"',
        'aria-label="bulleted list"',
        '<svg',
        '<button',
        'type="button"',
        'tabindex="-1"',
        '@click="getEditor().chain().toggleOrderedList().focus().run()"',
        '@focus="updateTabindexes($event.target)"',
        '@keyup.left="focusPrev($event.target)"',
        'x-bind:aria-pressed="updatedAt && getEditor().isActive(\'orderedList\')"',
        'aria-label="numbered list"',
        '<svg',
        '<div class="richTextEditor__editableContainer" x-ref="editorReference"></div>',
        '<input',
        'name="test" id="test" type="hidden" x-model="content"',
    ];

    $dontSee = [
        '\'aria-required\': \'true\'',
        '\'autofocus\': \'true\'',
        '\'aria-disabled\': \'true\'',
        'aria-labelledby',
        'aria-invalid',
        'disabled',
    ];

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('render - content', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="test"><p>testing</p></x-forms.rich-text-editor>'
    );

    $toSee = [
        'id="test-editor"',
        'x-data="editor(\'<p>testing</p>\'',

    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - id', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="other" id="test" />'
    );

    $toSee = [
        'id="test-editor"',
        '\'id\': \'test-editable\'',
        'id="test-toolbar"',
        'aria-controls="test-editable"',
        'name="other" id="test" type="hidden" x-model="content"',
    ];

    $dontSee = [
        'id="other-editor"',
        '\'id\': \'other-editable\'',
        'id="other-toolbar"',
        'aria-controls="other-editable"',
        'name="test",
        id="other"',
    ];

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('render - class', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="other" class="test" />'
    );

    $toSee = [
        'class="richTextEditor test"',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - autofocus', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="test" autofocus />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'autofocus\': \'\'',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - required', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="test" required />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-required\': true',
        '<input',
        'required',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - disabled', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="test" disabled />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        '\'enabled\': false',
        'editorProps: {',
        'attributes: {',
        '\'aria-disabled\': true',
        '\'tabindex\': -1',
        'id="test-toolbar"',
        'aria-disabled=true',
        '<button',
        'disabled',
        'bold',
        '<button',
        'disabled',
        'italic',
        '<button',
        'disabled',
        'underline',
        '<button',
        'disabled',
        'strikethrough',
        '<button',
        'disabled',
        'bulleted list',
        '<button',
        'disabled',
        'numbered list',
        '<input',
        'disabled',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - hinted', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.rich-text-editor name="test" hinted />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-describedby\': \'test-hint\'',
        '<input',
        'aria-describedby="test-hint"',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - invalid', function () {
    $view = $this->withViewErrors(['test' => 'test error'])->blade(
        '<x-forms.rich-text-editor name="test" />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-describedby\': \'test-error\'',
        '\'aria-invalid\': true',
        '<input',
        'aria-describedby="test-error"',
        'aria-invalid="true"',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - invalid and hinted', function () {
    $view = $this->withViewErrors(['test' => 'test error'])->blade(
        '<x-forms.rich-text-editor name="test" hinted />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-describedby\': \'test-hint test-error\'',
        '\'aria-invalid\': true',
        '<input',
        'aria-describedby="test-hint test-error"',
        'aria-invalid="true"',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - aria-label', function () {
    $view = $this->withViewErrors(['test' => 'test error'])->blade(
        '<x-forms.rich-text-editor name="test" aria-label="test label" />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-label\': \'test label\'',
        'id="test-toolbar"',
        'aria-label="test label"',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - aria-labelledby', function () {
    $view = $this->withViewErrors(['test' => 'test error'])->blade(
        '<x-forms.rich-text-editor name="test" aria-labelledby="test-label" />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-labelledby\': \'test-label\'',
        'id="test-toolbar"',
        'aria-labelledby="test-label"',
    ];

    $view->assertSeeInOrder($toSee, false);
});

test('render - aria-label and aria-labelledby', function () {
    $view = $this->withViewErrors(['test' => 'test error'])->blade(
        '<x-forms.rich-text-editor name="test" aria-label="test label" aria-labelledby="test-label" />'
    );

    $toSee = [
        'x-data="editor(\'\'',
        'editorProps: {',
        'attributes: {',
        '\'aria-label\': \'test label\'',
        'id="test-toolbar"',
        'aria-label="test label"',
    ];

    $dontSee = [
        '\'aria-labelledby\': \'test-label\'',
        'aria-labelledby="test-label"',
    ];

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});
