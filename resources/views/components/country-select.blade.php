@props(['name' => 'country'])
<select
    {{ $attributes->merge(['id' => $name]) }}
    name="{{ $name }}"
    x-data
    x-model="country"
>
    <option value="all" @selected($country === 'all')>{{ __('All countries') }}</option>
    @foreach($countries as $countryCode=>$countryName)
        <option value="{{ $countryCode }}" @selected($country === $countryCode)>{{ $countryName }}</option>
    @endforeach
</select>
