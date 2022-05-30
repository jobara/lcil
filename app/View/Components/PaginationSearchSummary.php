<?php

namespace App\View\Components;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class PaginationSearchSummary extends Component
{
    /**
     * The string of the search parameters used.
     *
     * @var string
     */
    public $search;

    /**
     * An instance of the paginator for which the data is being summarized
     *
     * @var LengthAwarePaginator
     */
    public $paginator;

    /**
     * Start index for items on current page
     *
     * @var int
     */
    public $start;

    /**
     * End index for items on current page
     *
     * @var int
     */
    public $end;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($paginator, $country = 'all', $subdivision = null, $keywords = null)
    {
        /**
         * @var array<string>
         */
        $search = [get_jurisdiction_name(isset($subdivision) ? "{$country}-{$subdivision}" : $country) ?? __('All countries')];
        if (isset($keywords)) {
            $search[] = "keywords: {$keywords}";
        }

        $this->search = implode(', ', $search);
        $this->paginator = $paginator;
        $this->start = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
        $this->end = $this->start + $paginator->count() - 1;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.pagination-search-summary');
    }
}
