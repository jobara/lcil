@props([
    'anchors' => [],
    'id' => 'error-summary',
    'message' => __('Please check the following fields in order to proceed:')
])
@if ($errors->any())
    <div {{ $attributes->class(['error-summary'])->merge(['id' => $id,'role' => 'alert']) }}>
        <p id="{{ $id . '__message' }}">
            @svg('gmdi-error-outline', 'icon-inline', ['aria-hidden' => 'true'])
            {{ $message }}
        </p>
        <ul>
            @foreach($errors->keys() as $key)
                @foreach ($errors->get($key) as $message)
                    @php
                        $found = Illuminate\Support\Arr::first(array_keys($anchors), function ($anchorKey) use ($key) {
                            return fnmatch($anchorKey, $key);
                        });

                        $anchor = $anchors[$found] ?? $key;

                        if ($anchor === true) {
                            $anchor = \Illuminate\Support\Str::slug($key);
                        }
                    @endphp
                    <li><a href="#{{ $anchor }}">{{ $message }}</a></li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif
