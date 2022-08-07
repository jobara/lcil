@props(['options'])
<span {{ $attributes }} x-data="duration({{ json_encode($options) }})">
    <time x-bind:datetime="duration.iso">
        {{ $slot }}
    </time>
</span>
