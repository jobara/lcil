<?php

namespace Tests\Feature;

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimplePaginationLinksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * No pages
     *
     * @return void
     */
    public function test_no_pages_render()
    {
        LawPolicySource::factory(5)->create();
        $paginator = LawPolicySource::simplePaginate(5);

        $view = $this->blade(
            '<x-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertDontSee('<nav aria-label="Pagination">', false);
    }

    /**
     * On first page
     *
     * @return void
     */
    public function test_first_page_render()
    {
        LawPolicySource::factory(15)->create();
        $paginator = LawPolicySource::paginate(5);

        $view = $this->blade(
            '<x-simple-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSee('<li class="disabled" aria-disabled="true"><span>&laquo; Previous</span></li>', false);
        $view->assertDontSee("<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\">&laquo; Previous</a></li>", false);

        $view->assertSee("<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"next\">Next &raquo;</a></li>", false);
        $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>Next &raquo;</span></li>', false);
    }

    /**
     * On middle page
     *
     * @return void
     */
    public function test_middle_page_render()
    {
        LawPolicySource::factory(15)->create();
        $paginator = LawPolicySource::paginate(5, ['*'], 'page', 2);

        $view = $this->blade(
            '<x-simple-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSee("<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\">&laquo; Previous</a></li>", false);
        $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>&laquo; Previous</span></li>', false);

        $view->assertSee("<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"next\">Next &raquo;</a></li>", false);
        $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>Next &raquo;</span></li>', false);
    }

    /**
     * On last page
     *
     * @return void
     */
    public function test_last_page_render()
    {
        LawPolicySource::factory(15)->create();
        $paginator = LawPolicySource::paginate(5, ['*'], 'page', 3);

        $view = $this->blade(
            '<x-simple-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSee("<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\">&laquo; Previous</a></li>", false);
        $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>&laquo; Previous</span></li>', false);

        $view->assertSee('<li class="disabled" aria-disabled="true"><span>Next &raquo;</span></li>', false);
        $view->assertDontSee("<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"next\">Next &raquo;</a></li>", false);
    }

    /**
     * Links generates the correct view.
     *
     * @return void
     */
    public function test_paginator_links_returns_correct_view()
    {
        LawPolicySource::factory(15)->create();
        $paginator = LawPolicySource::simplePaginate(5);

        $this->assertEquals('components.simple-pagination-links', $paginator->links()->name());
    }
}
