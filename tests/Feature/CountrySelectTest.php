<?php

namespace Tests\Feature;

use Tests\TestCase;

class CountrySelectTest extends TestCase
{
    /**
     * Default rendering.
     *
     * @return void
     */
    public function test_default_render()
    {
        $view = $this->blade(
            '<x-country-select />'
        );

        $view->assertSee('id="country"', false);
        $view->assertSee('name="country"', false);
        $view->assertSee('x-model="country"', false);
        $view->assertSee('<option value="all" selected>All countries</option>', false);
        $view->assertSee('<option value="CA" >Canada</option>', false);
        $view->assertSee('<option value="US" >United States</option>', false);
    }

    /**
     * Custom name defined
     *
     * @return void
     */
    public function test_render_name_data()
    {
        $view = $this->blade(
            '<x-country-select :name="$name"/>',
            ['name' => 'test']
        );

        $view->assertSee('id="test"', false);
        $view->assertSee('name="test"', false);
    }

    /**
     * Custom name and ID provided
     *
     * @return void
     */
    public function test_render_name_data_and_custom_id()
    {
        $view = $this->blade(
            '<x-country-select :name="$name" id="other"/>',
            ['name' => 'test']
        );

        $view->assertSee('id="other"', false);
        $view->assertSee('name="test"', false);
    }

    /**
     * All countries option selected
     *
     * @return void
     */
    public function test_render_country_data_all()
    {
        $view = $this->blade(
            '<x-country-select :country="$country"/>',
            ['country' => 'all']
        );

        $view->assertSee('<option value="all" selected>All countries</option>', false);
    }

    /**
     * CA option selected
     *
     * @return void
     */
    public function test_render_country_data_CA()
    {
        $view = $this->blade(
            '<x-country-select :country="$country"/>',
            ['country' => 'CA']
        );

        $view->assertDontSee('<option value="all" selected>All countries</option>', false);
        $view->assertSee('<option value="CA" selected>Canada</option>', false);
    }

    /**
     * Invalid country option provided
     *
     * @return void
     */
    public function test_render_invalid_country_data()
    {
        $view = $this->blade(
            '<x-country-select :country="$country"/>',
            ['country' => 'INVALID']
        );

        $view->assertSee('<option value="all" selected>All countries</option>', false);
    }
}
