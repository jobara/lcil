<?php

namespace Tests\Feature;

use Tests\TestCase;

class SubdivisionSelectTest extends TestCase
{
    /**
     * Default rendering
     *
     * @return void
     */
    public function test_default_render()
    {
        $view = $this->blade(
            '<x-subdivision-select />'
        );

        $view->assertSee('id="subdivision"', false);
        $view->assertSee('name="subdivision"', false);
        $view->assertSee('x-data="{subdivision: \'\', subdivisions: {}}"', false);
        $view->assertSee('subdivisions = await (async () => {await $nextTick(); return [];})();', false);
        $view->assertSee('country = \'all\'', false);
        $view->assertSee('subdivision = \'\'', false);
        $view->assertSee('$watch(\'country\', async () => {let response = await axios.get(`/jurisdictions/${country}`); subdivisions = response.data; subdivision = \'\'});', false);
        $view->assertSee('x-model="subdivision"', false);
    }

    /**
     * Custom name defined
     *
     * @return void
     */
    public function test_name_data_render()
    {
        $view = $this->blade(
            '<x-subdivision-select :name="$name"/>',
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
    public function test_name_data_and_custom_id_render()
    {
        $view = $this->blade(
            '<x-subdivision-select :name="$name" id="other"/>',
            ['name' => 'test']
        );

        $view->assertSee('id="other"', false);
        $view->assertSee('name="test"', false);
    }

    /**
     * Custom country provided
     *
     * @return void
     */
    public function test_country_data_render()
    {
        $view = $this->blade(
            '<x-subdivision-select :country="$country"/>',
            ['country' => 'CA']
        );

        $view->assertSee('country = \'CA\'', false);

        $view->assertSee(
            'subdivisions = await (async () => {await $nextTick(); return JSON.parse(\'{\u0022AB\u0022:\u0022Alberta\u0022,\u0022BC\u0022:\u0022British Columbia\u0022,\u0022MB\u0022:\u0022Manitoba\u0022,\u0022NB\u0022:\u0022New Brunswick\u0022,\u0022NL\u0022:\u0022Newfoundland and Labrador\u0022,\u0022NT\u0022:\u0022Northwest Territories\u0022,\u0022NS\u0022:\u0022Nova Scotia\u0022,\u0022NU\u0022:\u0022Nunavut\u0022,\u0022ON\u0022:\u0022Ontario\u0022,\u0022PE\u0022:\u0022Prince Edward Island\u0022,\u0022QC\u0022:\u0022Quebec\u0022,\u0022SK\u0022:\u0022Saskatchewan\u0022,\u0022YT\u0022:\u0022Yukon\u0022}\');})();', false);
    }

    /**
     * Custom subdivision provided
     *
     * @return void
     */
    public function test_subdivision_data_render()
    {
        $view = $this->blade(
            '<x-subdivision-select :country="$country" :subdivision="$subdivision"/>',
            [
                'country' => 'CA',
                'subdivision' => 'ON',
            ]
        );

        $view->assertSee('subdivision = \'ON\'', false);
    }

    /**
     * Custom subdivision provided without country
     *
     * @return void
     */
    public function test_subdivision_data_without_country_render()
    {
        $view = $this->blade(
            '<x-subdivision-select :subdivision="$subdivision"/>',
            ['subdivision' => 'ON']
        );

        $view->assertSee('country = \'all\'', false);
        $view->assertSee('subdivision = \'\'', false);
        $view->assertSee('subdivisions = await (async () => {await $nextTick(); return [];})();', false);
    }
}
