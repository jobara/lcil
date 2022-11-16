@unless ($breadcrumbs->isEmpty())
    <nav class="breadcrumbs" aria-label="{{ __('Breadcrumbs') }}">
        <ol>
            @foreach ($breadcrumbs as $breadcrumb)

                @if (isset($breadcrumb->url) && !$loop->last)
                    <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                    @svg('gmdi-arrow-forward-ios', ['aria-hidden' => 'true'])
                @else
                    <li @if ($loop->last) aria-current="page" @endif>{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ol>
    </nav>
@endunless
