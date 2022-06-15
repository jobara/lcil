@props(['name' => 'country', 'placeholder' => ''])
<select
    {{ $attributes->merge(['id' => $name]) }}
    name="{{ $name }}"
    x-data
    x-model="country"
>
    <option value="" @selected(!$country)>{{ $placeholder }}</option>
    @foreach($countries as $countryCode=>$countryName)
        <option value="{{ $countryCode }}" @selected($country === $countryCode)>{{ $countryName }}</option>
    @endforeach
</select>
