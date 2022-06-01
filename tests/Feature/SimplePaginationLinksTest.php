<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    LawPolicySource::factory(15)->create();
});

test('render - no pages', function () {
    $paginator = LawPolicySource::simplePaginate(15);

    $view = $this->blade(
        '<x-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertDontSee('<nav aria-label="Pagination">', false);
});

test('render - first page', function () {
    $paginator = LawPolicySource::paginate(5);

    $view = $this->blade(
        '<x-simple-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSee('<li class="disabled" aria-disabled="true"><span>&laquo; Previous</span></li>', false);
    $view->assertDontSee("<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\">&laquo; Previous</a></li>", false);

    $view->assertSee("<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"next\">Next &raquo;</a></li>", false);
    $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>Next &raquo;</span></li>', false);
});

test('render - middle page', function () {
    $paginator = LawPolicySource::paginate(5, ['*'], 'page', 2);

    $view = $this->blade(
        '<x-simple-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSee("<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\">&laquo; Previous</a></li>", false);
    $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>&laquo; Previous</span></li>', false);

    $view->assertSee("<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"next\">Next &raquo;</a></li>", false);
    $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>Next &raquo;</span></li>', false);
});

test('render - last page', function () {
    $paginator = LawPolicySource::paginate(5, ['*'], 'page', 3);

    $view = $this->blade(
        '<x-simple-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSee("<li><a href=\"{$paginator->previousPageUrl()}\" rel=\"prev\">&laquo; Previous</a></li>", false);
    $view->assertDontSee('<li class="disabled" aria-disabled="true"><span>&laquo; Previous</span></li>', false);

    $view->assertSee('<li class="disabled" aria-disabled="true"><span>Next &raquo;</span></li>', false);
    $view->assertDontSee("<li><a href=\"{$paginator->nextPageUrl()}\" rel=\"next\">Next &raquo;</a></li>", false);
});

test('paginator uses correct view', function () {
    $paginator = LawPolicySource::simplePaginate(5);

    expect($paginator->links()->name())->toBe('components.simple-pagination-links');
});
