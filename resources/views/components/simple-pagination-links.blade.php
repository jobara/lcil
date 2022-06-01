@if ($paginator->hasPages())
    <nav aria-label="{{ __('Pagination') }}">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true"><span>&lsaquo; {{ __('Previous') }}</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo; {{ __('Previous') }}</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('Next') }} &rsaquo;</a></li>
            @else
                <li class="disabled" aria-disabled="true"><span>{{ __('Next') }} &rsaquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
