@props(['placeholder', 'name' => 'country'])

<x-hearth-select
    {{ $attributes }}
    :name="$name"
    :options="$countries"
    :selected="$country"
/>
