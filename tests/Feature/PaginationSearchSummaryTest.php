<?php

namespace Tests\Feature;

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\ViewException;
use Tests\TestCase;

class PaginationSearchSummaryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Default rendering of paged data summary
     *
     * @return void
     */
    public function test_pagination_search_summary_render()
    {
        LawPolicySource::factory(15)->create();
        $paginator = LawPolicySource::paginate(5);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSee('role="status"', false);
        $view->assertSeeText('Found 15 for All countries.');
        $view->assertSeeText('Showing results 1 to 5.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for middle page
     *
     * @return void
     */
    public function test_pagination_search_summary_middle_page_render()
    {
        LawPolicySource::factory(15)->create();

        // From Illuminate\Database\Eloquent\Builder
        // parameters: $perPage, $columns, $pageName, $page
        $paginator = LawPolicySource::paginate(5, ['*'], 'page', 2);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSeeText('Found 15 for All countries.');
        $view->assertSeeText('Showing results 6 to 10.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for last page
     *
     * @return void
     */
    public function test_pagination_search_summary_last_page_render()
    {
        LawPolicySource::factory(15)->create();

        // From Illuminate\Database\Eloquent\Builder
        // parameters: $perPage, $columns, $pageName, $page
        $paginator = LawPolicySource::paginate(10, ['*'], 'page', 2);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSeeText('Found 15 for All countries.');
        $view->assertSeeText('Showing results 11 to 15.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Assert error thrown when no paginator provided.
     *
     * @return void
     */
    public function test_pagination_search_summary_throw_error_without_paginator()
    {
        $this->expectException(ViewException::class);
        $view = $this->blade(
            '<x-pagination-search-summary />'
        );
    }

    /**
     * Rendering summary for search with country
     *
     * @return void
     */
    public function test_pagination_search_summary_jurisdiction_country_render()
    {
        LawPolicySource::factory(15)->create();

        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :country="$country" />',
            [
                'paginator' => $paginator,
                'country' => 'CA'
            ]
        );

        $view->assertSeeText('Found 15 for Canada.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for search with invalid country
     *
     * @return void
     */
    public function test_pagination_search_summary_jurisdiction_invalid_country_render()
    {
        LawPolicySource::factory(15)->create();

        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :country="$country" />',
            [
                'paginator' => $paginator,
                'country' => 'MISSING'
            ]
        );

        $view->assertSeeText('Found 15 for All countries.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for search with country and subdivision
     *
     * @return void
     */
    public function test_pagination_search_summary_jurisdiction_subdivision_render()
    {
        LawPolicySource::factory(15)->create();

        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" />',
            [
                'paginator' => $paginator,
                'country' => 'CA',
                'subdivision' => 'ON'
            ]
        );

        $view->assertSeeText('Found 15 for Ontario, Canada.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for search with country and invalid subdivision
     *
     * @return void
     */
    public function test_pagination_search_summary_jurisdiction_invalid_subdivision_render()
    {
        LawPolicySource::factory(15)->create();

        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" />',
            [
                'paginator' => $paginator,
                'country' => 'CA',
                'subdivision' => 'MISSING'
            ]
        );

        $view->assertSeeText('Found 15 for Canada.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for search with keywords
     *
     * @return void
     */
    public function test_pagination_search_summary_keywords_render()
    {
        LawPolicySource::factory(15)->create();

        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :keywords="$keywords" />',
            [
                'paginator' => $paginator,
                'keywords' => 'testing foo bar'
            ]
        );

        $view->assertSeeText('Found 15 for All countries, keywords: testing foo bar.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for search with keywords and jurisdiction
     *
     * @return void
     */
    public function test_pagination_search_summary_keywords_and_jurisdiction_render()
    {
        LawPolicySource::factory(15)->create();

        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" :keywords="$keywords" />',
            [
                'paginator' => $paginator,
                'country' => 'CA',
                'subdivision' => 'ON',
                'keywords' => 'testing foo bar'
            ]
        );

        $view->assertSeeText('Found 15 for Ontario, Canada, keywords: testing foo bar.');
        $view->assertDontSeeText('Found 0 for');
    }

    /**
     * Rendering summary for search with no results found
     *
     * @return void
     */
    public function test_pagination_search_summary_not_found_render()
    {
        $paginator = LawPolicySource::paginate(10);

        $view = $this->blade(
            '<x-pagination-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" :keywords="$keywords" />',
            [
                'paginator' => $paginator,
                'country' => 'CA',
                'subdivision' => 'ON',
                'keywords' => 'testing foo bar'
            ]
        );

        $view->assertSeeText('Found 0 for Ontario, Canada, keywords: testing foo bar.');
        $view->assertDontSeeText('Showing results');
    }
}
