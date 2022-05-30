<?php

namespace Tests\Feature;

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LawPolicySourcesIndexKeywordsTest extends TestCase
{
    // Can't use the RefreshDatabase trait because full text searches do not work with it. Need to handle the commit and
    // cleanup manually instead.
    // See: https://laracasts.com/discuss/channels/testing/issue-with-data-persistenceeloquent-query-when-running-tests
    // use RefreshDatabase;

    // Refresh the DB with migrations
    use DatabaseMigrations;

    /**
     * @return void
     */
    public function test_index_route_with_keywords_parameter()
    {
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory()
            ->create([
                'name' => 'Test Law and Policy Source',
                'jurisdiction' => 'CA',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'Test LP',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'Act Source',
            ]);
        LawPolicySource::factory()
            ->create([
                'name' => 'An Act',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' =>'all', 'keywords' => 'Test Source']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        // Should find 3 Law and Policy Sources
        $this->assertEquals(3, $response['lawPolicySources']->count(), 'Expected 3 law and policy sources returned');
    }

    /**
     * @return void
     */
    public function test_index_route_with_keywords_parameter_no_matches()
    {
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory()
            ->create([
                'name' => 'Test Law and Policy Source',
                'jurisdiction' => 'CA',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' =>'all', 'keywords' => 'None']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        $this->assertEquals(0, $response['lawPolicySources']->count(), 'Expected 0 law and policy sources returned');
    }

    /**
     * @return void
     */
    public function test_index_route_with_keywords_parameter_no_country_matches()
    {
        // create Law and Policy Sources to use for the test
        LawPolicySource::factory()
            ->create([
                'name' => 'Test Law and Policy Source',
                'jurisdiction' => 'CA',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['country' =>'US', 'keywords' => 'Test']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewHas('lawPolicySources');
        $this->assertEquals(0, $response['lawPolicySources']->count(), 'Expected 0 law and policy sources returned');
    }

    /**
     * @return void
     */
    public function test_index_route_with_keywords_but_no_country_parameter()
    {
        // create a Law and Policy Source to use for the test
        LawPolicySource::factory()
            ->create([
                'name' => 'Test Law and Policy Source',
            ]);

        $response = $this->get(localized_route('law-policy-sources.index', ['keywords' => 'test source']));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.index');
        $response->assertViewMissing('lawPolicySources');
    }
}
