<div {{ $attributes->merge(['role' => 'status']) }}>
    @if ($paginator->count())
        <p>{{ __('Found :total for :search.', ['total' => $paginator->total(), 'search' => $search]) }}</p>
        <p>{{ __('Showing results :start to :end.', ['start' => $start, 'end' => $end]) }}</p>
    @else
        <p>{{ __('Found 0 for :search.', ['search' => $search]) }}</p>
    @endif
</div>
