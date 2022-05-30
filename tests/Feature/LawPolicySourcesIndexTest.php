<?php

namespace Tests\Feature;

use App\Models\LawPolicySource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LawPolicySourcesIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_index_route_without_parameters()
    {
        $response = $this->get(localized_route('law-policy-sources.index'));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewMissing('lawPolicySources');
    }

    /**
     * @return void
     */
    public function test_index_route_with_all_country_parameter()
    {
        $count = 2;
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory($count)->create();

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        $this->assertEquals($count, $response['lawPolicySources']->count());
        foreach ($response['lawPolicySources'] as $lawPolicySource) {
            $this->assertInstanceOf(LawPolicySource::class, $lawPolicySource);
        }
    }

    /**
     * @return void
     */
    public function test_index_route_with_country_parameter()
    {
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory()
            ->create([
                'jurisdiction' => 'CA-ON',
            ]);
        LawPolicySource::factory()
            ->create([
                'jurisdiction' => 'CA',
            ]);
        LawPolicySource::factory()
            ->create([
                'jurisdiction' => 'US',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        // only 2 are from the CA country code
        $this->assertEquals(2, $response['lawPolicySources']->count(), 'Expected 2 law and policy sources returned');
        foreach ($response['lawPolicySources'] as $lawPolicySource) {
            $this->assertInstanceOf(LawPolicySource::class, $lawPolicySource);
            $this->assertEquals('CA', explode('-', $lawPolicySource->jurisdiction)[0]);
        }
    }

    /**
     * @return void
     */
    public function test_index_route_with_country_parameter_no_matching_records()
    {
        // create a Law and Policy Source to use for the test
        LawPolicySource::factory()
            ->create([
                'jurisdiction' => 'US',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        // none have the CA country code
        $this->assertEquals(0, $response['lawPolicySources']->count(), 'Expected 0 law and policy sources returned');
    }

    /**
     * @return void
     */
    public function test_index_route_with_all_country_and_subdivision_parameter()
    {
        $count = 2;
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory($count)->create();

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all', 'subdivision' => 'any']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');

        $this->assertEquals($count, $response['lawPolicySources']->count(), "Expected {$count} law and policy sources returned");
        foreach ($response['lawPolicySources'] as $lawPolicySource) {
            $this->assertInstanceOf(LawPolicySource::class, $lawPolicySource);
        }
    }

    /**
     * @return void
     */
    public function test_index_route_with_country_and_subdivision_parameter()
    {
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA-ON',
        ]);
        LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'CA',
        ]);
        LawPolicySource::factory()
        ->create([
            'jurisdiction' => 'US',
        ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA', 'subdivision' => 'ON']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        // only 2 are from the CA country code
        $this->assertEquals(1, $response['lawPolicySources']->count(), 'Expected 1 law and policy sources returned');
        foreach ($response['lawPolicySources'] as $lawPolicySource) {
            $this->assertInstanceOf(LawPolicySource::class, $lawPolicySource);
            $this->assertEquals('CA', explode('-', $lawPolicySource->jurisdiction)[0]);
        }
    }

    /**
     * @return void
     */
    public function test_index_route_with_country_and_subdivision_parameter_no_matching_records()
    {
        // create a Law and Policy Source to use for the test
        LawPolicySource::factory()
            ->create([
                'jurisdiction' => 'CA-ON',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'CA', 'subdivision' => 'BC']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        // none have the CA country code
        $this->assertEquals(0, $response['lawPolicySources']->count(), 'Expected 0 law and policy sources returned');
    }

    /**
     * @return void
     */
    public function test_index_route_paged()
    {
        // create a Law and Policy Source to use for the test
        $count = 20;
        LawPolicySource::factory($count)->create();

        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');

        $this->assertEquals(10, $response['lawPolicySources']->count(), 'Expected 10 law and policy sources returned in page');
        $this->assertEquals($count, $response['lawPolicySources']->total(), "Total should be {$count}");
    }

    /**
     * @return void
     */
    public function test_index_route_without_parameters_render()
    {
        $strings = [
            'Law and Policy Sources',
            'Search for sources of law and policy to view',
            'Country:',
            'All countries',
            'Province / Territory:',
            'Select a country first',
            'Law or policy name contains keywords:',
            'Search',
            'Search results will appear here',
        ];

        $strings_not_rendered = [
            'Search for sources of law and policy to view or edit',
            'Create new law or policy source if it does not already exist',
            'Found',
            'Previous',
            'Next',
        ];

        $view = $this->view('law-policy-sources.index');
        $view->assertSeeTextInOrder($strings);

        foreach ($strings_not_rendered as $notRendered) {
            $view->assertDontSeeText($notRendered);
        }
    }

    /**
     * @return void
     */
    public function test_index_route_with_authenticated_user_render()
    {
        $strings = [
            'Search for sources of law and policy to view or edit',
            'Create new law or policy source if it does not already exist',
        ];

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(localized_route('law-policy-sources.index'));

        $response->assertSeeTextInOrder($strings);
    }

    /**
     * @return void
     */
    public function test_index_route_sort_order()
    {
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory()
            ->create([
                'name' => 'Subdivision First',
                'jurisdiction' => 'CA-ON',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'Subdivision Second',
                'jurisdiction' => 'CA-ON',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'City Second',
                'jurisdiction' => 'CA-ON',
                'municipality' => 'Toronto',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'City First',
                'jurisdiction' => 'CA-ON',
                'municipality' => 'Markham',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'Country First',
                'jurisdiction' => 'CA',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'Country Second',
                'jurisdiction' => 'US',
            ]);

        $order = ['Country First', 'Subdivision First', 'Subdivision Second', 'City First', 'City Second', 'Country Second'];
        $response = $this->get(localized_route('law-policy-sources.index', ['country' => 'all']));

        $this->assertEquals($order, array_column($response->viewData('lawPolicySources')->items(), 'name'));
    }
}
