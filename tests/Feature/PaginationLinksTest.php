<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    LawPolicySource::factory(15)->create();
});

test('render - no pages', function () {
    $paginator = LawPolicySource::paginate(15);

    $view = $this->blade(
        '<x-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertDontSee('<nav aria-label="Pagination">', false);
});

test('render - first page', function () {
    $perPage = 5;
    $paginator = LawPolicySource::paginate($perPage);

    $view = $this->blade(
        '<x-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSeeText("Next {$perPage} items");
    $view->assertDontSeeText("Previous {$perPage} items");
});

test('render - middle page', function () {
    $perPage = 5;
    $paginator = LawPolicySource::paginate($perPage, page: 2);

    $view = $this->blade(
        '<x-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSeeText("Previous {$perPage} items");
    $view->assertSeeText("Next {$perPage} items");
});

test('render - last page', function () {
    $perPage = 5;
    $paginator = LawPolicySource::paginate($perPage, page: 3);

    $view = $this->blade(
        '<x-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSeeText("Previous {$perPage} items");
    $view->assertDontSeeText('Next');
});

test('render - remaining items less than per page number', function () {
    $perPage = 4;
    $paginator = LawPolicySource::paginate($perPage, page: 3);

    $view = $this->blade(
        '<x-pagination-links :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSeeText("Previous {$perPage} items");
    $view->assertSeeText('Next 3 items');
});

test('paginator uses correct view', function () {
    $paginator = LawPolicySource::paginate(5);

    expect($paginator->links()->name())->toBe('components.pagination-links');
});
