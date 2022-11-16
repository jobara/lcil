<div {{ $attributes->merge(['role' => 'status']) }}>
    <p>
        {{ trans_choice('Found :total item for :search.|Found :total items for :search.', $paginator->total(), ['total' => $paginator->total(), 'search' => $search]) }}
    </p>
    @if ($paginator->count())
        <p>{{ __('Showing results :start to :end.', ['start' => $start, 'end' => $end]) }}</p>
    @endif
</div>
