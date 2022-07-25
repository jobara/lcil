<?php

test('about page', function () {
    $response = $this->get(localized_route('about'));

    $response->assertStatus(200);
    $response->assertViewIs('about');
});
