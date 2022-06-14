@unless ($breadcrumbs->isEmpty())
    <nav aria-label="{{ __('Breadcrumbs') }}">
        <ol>
            @foreach ($breadcrumbs as $breadcrumb)

                @if (isset($breadcrumb->url) && !$loop->last)
                    <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @else
                    <li @if ($loop->last) aria-current="page" @endif>{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ol>
    </nav>
@endunless
