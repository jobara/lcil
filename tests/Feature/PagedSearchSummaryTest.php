<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\ViewException;

uses(RefreshDatabase::class);

beforeEach(function () {
    LawPolicySource::factory(15)->create();
});

test('render - first page', function () {
    $paginator = LawPolicySource::paginate(5);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSee('role="status"', false);
    $view->assertSeeText('Found 15 for All countries.');
    $view->assertSeeText('Showing results 1 to 5.');
    $view->assertDontSeeText('Found 0 for');
});

test('render - middle page', function () {
    // From Illuminate\Database\Eloquent\Builder
    // parameters: $perPage, $columns, $pageName, $page
    $paginator = LawPolicySource::paginate(5, ['*'], 'page', 2);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSeeText('Found 15 for All countries.');
    $view->assertSeeText('Showing results 6 to 10.');
    $view->assertDontSeeText('Found 0 for');
});

test('render - last page', function () {
    $paginator = LawPolicySource::paginate(10, ['*'], 'page', 2);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" />',
        ['paginator' => $paginator]
    );

    $view->assertSeeText('Found 15 for All countries.');
    $view->assertSeeText('Showing results 11 to 15.');
    $view->assertDontSeeText('Found 0 for');
});

test('throws an error if paginator not provided', function () {
    $this->blade(
        '<x-paged-search-summary />'
    );
})->throws(ViewException::class, 'Unresolvable dependency resolving [Parameter #0');

test('render search with country', function () {
    $paginator = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :country="$country" />',
        [
            'paginator' => $paginator,
            'country' => 'CA',
        ]
    );

    $view->assertSeeText('Found 15 for Canada.');
    $view->assertDontSeeText('Found 0 for');
});

test('render search with country - invalid', function () {
    $paginator = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :country="$country" />',
        [
            'paginator' => $paginator,
            'country' => 'MISSING',
        ]
    );

    $view->assertSeeText('Found 15 for All countries.');
    $view->assertDontSeeText('Found 0 for');
});

test('render search with country and subdivision', function () {
    $paginator = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" />',
        [
            'paginator' => $paginator,
            'country' => 'CA',
            'subdivision' => 'ON',
        ]
    );

    $view->assertSeeText('Found 15 for Ontario, Canada.');
    $view->assertDontSeeText('Found 0 for');
});

test('render search with country and subdivision - invalid', function () {
    $paginator = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" />',
        [
            'paginator' => $paginator,
            'country' => 'CA',
            'subdivision' => 'MISSING',
        ]
    );

    $view->assertSeeText('Found 15 for Canada.');
    $view->assertDontSeeText('Found 0 for');
});

test('render search with keywords', function () {
    $paginator = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :keywords="$keywords" />',
        [
            'paginator' => $paginator,
            'keywords' => 'testing foo bar',
        ]
    );

    $view->assertSeeText('Found 15 for All countries, keywords: testing foo bar.');
    $view->assertDontSeeText('Found 0 for');
});

test('render search with keywords and jurisdiction', function () {
    $paginator = LawPolicySource::paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" :keywords="$keywords" />',
        [
            'paginator' => $paginator,
            'country' => 'CA',
            'subdivision' => 'ON',
            'keywords' => 'testing foo bar',
        ]
    );

    $view->assertSeeText('Found 15 for Ontario, Canada, keywords: testing foo bar.');
    $view->assertDontSeeText('Found 0 for');
});

test('render search - no matches', function () {
    $paginator = LawPolicySource::where('id', 'missing')->paginate(10);

    $view = $this->blade(
        '<x-paged-search-summary :paginator="$paginator" :country="$country" :subdivision="$subdivision" :keywords="$keywords" />',
        [
            'paginator' => $paginator,
            'country' => 'CA',
            'subdivision' => 'ON',
            'keywords' => 'testing foo bar',
        ]
    );

    $view->assertSeeText('Found 0 for Ontario, Canada, keywords: testing foo bar.');
    $view->assertDontSeeText('Showing results');
});
