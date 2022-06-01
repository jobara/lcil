<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class PagedSearchSummary extends Component
{
    public string $search;

    public LengthAwarePaginator $paginator;

    public int $start;

    public int $end;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(LengthAwarePaginator $paginator, string $country = 'all', ?string $subdivision = null, ?string $keywords = null)
    {
        /** @var array<string> */
        $searchData = [
            get_jurisdiction_name(isset($subdivision) ?
                "{$country}-{$subdivision}" :
                $country) ?? __('All countries'),
        ];

        if (isset($keywords)) {
            $searchData[] = "keywords: {$keywords}";
        }

        $this->search = implode(', ', $searchData);
        $this->paginator = $paginator;
        $this->start = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
        $this->end = $this->start + $paginator->count() - 1;
    }

    public function render(): View|\Closure|string
    {
        return view('components.paged-search-summary');
    }
}
