@props(['value', 'for'])
@isset($for)
    <label {{ $attributes->merge(['id' => "{$for}-label", 'for' => $for]) }}>{{ $value ?? $slot }}</label>
@endisset
