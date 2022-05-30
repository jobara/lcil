<?php

namespace Tests\Feature;

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationLinksTest extends TestCase
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
        $paginator = LawPolicySource::paginate(5);

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
            '<x-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSeeText('Next 5 items');
        $view->assertDontSeeText('Previous 5 items');
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
            '<x-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSeeText('Previous 5 items');
        $view->assertSeeText('Next 5 items');
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
            '<x-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSeeText('Previous 5 items');
        $view->assertDontSeeText('Next 5 items');
    }

    /**
     * Less than a full page remaining. Should indicate fewer for next page.
     *
     * @return void
     */
    public function test_next_remaining_items_render()
    {
        LawPolicySource::factory(13)->create();
        $paginator = LawPolicySource::paginate(5, ['*'], 'page', 2);

        $view = $this->blade(
            '<x-pagination-links :paginator="$paginator" />',
            ['paginator' => $paginator]
        );

        $view->assertSeeText('Previous 5 items');
        $view->assertSeeText('Next 3 items');
    }

    /**
     * Links generates the correct view.
     *
     * @return void
     */
    public function test_paginator_links_returns_correct_view()
    {
        LawPolicySource::factory(15)->create();
        $paginator = LawPolicySource::paginate(5);

        $this->assertEquals('components.pagination-links', $paginator->links()->name());
    }
}
