@props(['name' => 'subdivision'])
<select
    {{ $attributes->merge(['id' => $name]) }}
    name="{{ $name }}"
    x-data="{subdivision: '', subdivisions: {}}"
    x-init="
        subdivisions = await (async () => {await $nextTick(); return {{ Js::from($subdivisions) }};})();
        country = '{{ $country }}';
        subdivision = '{{ $subdivision }}';
        $watch('country', async () => {let response = country ? await axios.get(`/jurisdictions/${country}`) : {}; subdivisions = response.data ?? []; subdivision = ''});
    "
    x-model="subdivision"
>
        <template x-if="Object.keys(subdivisions).length">
            <option value="">{{ __('All provinces / territories') }}</option>
        </template>
        <template x-if="!Object.keys(subdivisions).length && country">
            <option value="">{{ __('Not available') }}</option>
        </template>
        <template x-if="!Object.keys(subdivisions).length && !country">
            <option value="">{{ __('Select a country first') }}</option>
        </template>
        <template x-for="(subdivisionName, subdivisionCode) in subdivisions">
            <option :value="subdivisionCode" x-text="subdivisionName"></option>
        </template>
</select>
