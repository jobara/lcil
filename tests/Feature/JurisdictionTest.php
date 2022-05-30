<?php

namespace Tests\Feature;

use Tests\TestCase;

class JurisdictionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index_route()
    {
        $response = $this->get('/jurisdictions');

        $response->assertStatus(200);
        $response->assertExactJson(get_countries());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_route()
    {
        $response = $this->get('/jurisdictions/CA');

        $response->assertStatus(200);
        $response->assertExactJson(get_subdivisions('CA'));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_route_with_invalid_country()
    {
        $response = $this->get('/jurisdictions/missing');

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }
}
