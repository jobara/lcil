<x-app-layout>
    <x-slot name="header">
        <h1 itemprop="name">{{ __(':name: Measures', ['name' => config('app.name', 'LCIL')]) }}</h1>
    </x-slot>

    @foreach ($lcilMeasures as $dimension)
        <h2>{{ $dimension['code'] }}: {{ $dimension['description'] }}</h2>

        @foreach ($dimension->indicators as $indicator)
            <h3>{{ $indicator['code'] }}: {{ $indicator['description'] }}</h3>

            @foreach ($indicator->measures as $measure)
                <h4>{{ $measure['code'] }}@if ($measure['title']): {{ $measure['title'] }}@endif</h4>

                @if ($measure['type'])
                    <p>
                        <em>{{ $measure['type'] }}</em>
                    </p>
                @endif

                <p>{{ $measure['description'] }}</p>

            @endforeach
        @endforeach
    @endforeach
</x-app-layout>
