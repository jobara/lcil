<?php

test('index route', function () {
    $response = $this->getJson('/jurisdictions');

    $response->assertStatus(200);
    $response->assertExactJson(get_countries());
});

test('show route', function () {
    $response = $this->getJson('/jurisdictions/CA');

    $response->assertStatus(200);
    $response->assertExactJson(get_subdivisions('CA'));
});

test('show route with invalid country', function () {
    $response = $this->getJson('/jurisdictions/missing');

    $response->assertStatus(200);
    $response->assertExactJson([]);
});
