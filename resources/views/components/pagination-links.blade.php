@if ($paginator->hasPages())
    <nav aria-label="{{ __('Pagination') }}">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @unless ($paginator->onFirstPage())
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('Previous :numPrev items', ['numPrev' => $paginator->perPage()]) }}">&lsaquo; {{ __('Previous :numPrev items', ['numPrev' => $paginator->perPage()]) }}</a>
                </li>
            @endunless

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                @php
                    $numNext = $paginator->currentPage() + 1 === $paginator->lastPage() ? $paginator->total() - ($paginator->currentPage() * $paginator->perPage()) : $paginator->perPage();
                @endphp
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('Next :numNext items', ['numNext' => $numNext]) }}">{{ __('Next :numNext items', ['numNext' => $numNext]) }} &rsaquo;</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
