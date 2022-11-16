<?php

test('default render', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.subdivision-select />'
    );

    $view->assertSee('id="subdivision"', false);
    $view->assertSee('name="subdivision"', false);
    $view->assertSee('x-data="{subdivision: \'\', subdivisions: {}}"', false);
    $view->assertSee('subdivisions = await (async () => {await $nextTick(); return [];})();', false);
    $view->assertSee('country = \'\'', false);
    $view->assertSee('subdivision = \'\'', false);
    $view->assertSee('$watch(\'country\', async () => {let response = country ? await axios.get(`/jurisdictions/${country}`) : {}; subdivisions = response.data ?? []; subdivision = \'\'});', false);
    $view->assertSee('x-model="subdivision"', false);
});

test('render with name data', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.subdivision-select :name="$name"/>',
        ['name' => 'test']
    );

    $view->assertSee('id="test"', false);
    $view->assertSee('name="test"', false);
});

test('render with name data and custom id', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.subdivision-select :name="$name" id="other"/>',
        ['name' => 'test']
    );

    $view->assertSee('id="other"', false);
    $view->assertSee('name="test"', false);
});

test('render with country data', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.subdivision-select :country="$country"/>',
        ['country' => 'CA']
    );

    $view->assertSee('country = \'CA\'', false);

    $view->assertSee(
        'subdivisions = await (async () => {await $nextTick(); return JSON.parse(\'{\u0022AB\u0022:\u0022Alberta\u0022,\u0022BC\u0022:\u0022British Columbia\u0022,\u0022MB\u0022:\u0022Manitoba\u0022,\u0022NB\u0022:\u0022New Brunswick\u0022,\u0022NL\u0022:\u0022Newfoundland and Labrador\u0022,\u0022NT\u0022:\u0022Northwest Territories\u0022,\u0022NS\u0022:\u0022Nova Scotia\u0022,\u0022NU\u0022:\u0022Nunavut\u0022,\u0022ON\u0022:\u0022Ontario\u0022,\u0022PE\u0022:\u0022Prince Edward Island\u0022,\u0022QC\u0022:\u0022Quebec\u0022,\u0022SK\u0022:\u0022Saskatchewan\u0022,\u0022YT\u0022:\u0022Yukon\u0022}\');})();',
        false
    );
});

test('render with subdivision data', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.subdivision-select :country="$country" :subdivision="$subdivision"/>',
        [
            'country' => 'CA',
            'subdivision' => 'ON',
        ]
    );

    $view->assertSee('subdivision = \'ON\'', false);
});

test('render with subdivision data - without country data', function () {
    $view = $this->withViewErrors([])->blade(
        '<x-forms.subdivision-select :subdivision="$subdivision"/>',
        ['subdivision' => 'ON']
    );

    $view->assertSee('country = \'\'', false);
    $view->assertSee('subdivision = \'\'', false);
    $view->assertSee('subdivisions = await (async () => {await $nextTick(); return [];})();', false);
});

test('render with hint', function ($data, $hint) {
    $tag = isset($data['name']) ?
        '<x-forms.subdivision-select :name="$name" :hinted="$hinted" />' :
        '<x-forms.subdivision-select :hinted="$hinted" />';

    $view = $this->withViewErrors([])->blade($tag, $data);

    $view->assertSee('aria-describedby', false);
    $view->assertSee($hint, false);
})->with([
    'default' => [['hinted' => true], 'subdivision-hint'],
    'hint id' => [['hinted' => 'custom-hint-id'], 'custom-hint-id'],
    'custom name' => [['hinted' => true, 'name' => 'test'], 'test-hint'],
]);

test('render with error hint', function ($data, $hint) {
    $tag = isset($data['name']) ?
        '<x-forms.subdivision-select :name="$name" />' :
        '<x-forms.subdivision-select />';

    $view = $this->withViewErrors([
        $data['name'] ?? 'subdivision' => 'The subdivision field is required when municipality is present.',
    ])->blade($tag, $data);

    $view->assertSee('aria-describedby', false);
    $view->assertSee($hint, false);
})->with([
    'default' => [[], 'subdivision-error'],
    'custom name' => [['name' => 'test'], 'test-error'],
]);
