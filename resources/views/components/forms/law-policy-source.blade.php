@props(['lawPolicySource' => null])
<form
    {{
        $attributes->merge([
            'method' => 'POST',
            'action' => route($lawPolicySource ? 'lawPolicySources.update' : 'lawPolicySources.store', $lawPolicySource),
        ])
    }}
>
    @csrf
    @isset($lawPolicySource)
        @method('patch')
    @endisset
    <ul role="list" x-data="{country: '{{ old('country', $lawPolicySource?->country) }}'}">
        <li>
            <x-forms.label for="name" :value="__('Law or Policy Name (required)')" />
            <x-hearth-input type="text" name="name" :value="old('name', $lawPolicySource?->name)" required />
            <x-hearth-error for="name" />
        </li>
        <li>
            <x-forms.label for="country" :value="__('Country (required)')" />
            <x-forms.country-select :country="old('country', parse_country_code($lawPolicySource?->jurisdiction))" x-model="country" required />
            <x-hearth-error for="country" />
        </li>
        <li>
            <x-forms.label for="subdivision" :value="__('Province / Territory')" />
            <x-forms.subdivision-select
                :country="old('country', parse_country_code($lawPolicySource?->jurisdiction))"
                :subdivision="old('subdivision', parse_subdivision_code($lawPolicySource?->jurisdiction))"
            />
            <x-hearth-error for="subdivision" />
        </li>
        <li>
            <x-forms.label for="municipality" :value="__('Municipality')" />
            <x-hearth-input type="text" name="municipality" :value="old('municipality', $lawPolicySource?->municipality)" hinted />
            <x-hearth-hint for="municipality">{{ __('Requires a Province / Territory to be selected') }}</x-hearth-hint>
            <x-hearth-error for="municipality" />
        </li>
        <li>
            <x-forms.label for="year_in_effect" :value="__('Year in Effect')" />
            <x-hearth-input
                type="number"
                name="year_in_effect"
                min="{{ config('settings.year.min') }}"
                max="{{ config('settings.year.max') }}"
                hinted
                :value="old('year_in_effect', $lawPolicySource?->year_in_effect)"
            />
            <x-hearth-hint for="year_in_effect">{{ __('YYYY format. Example: 2022.') }}</x-hearth-hint>
            <x-hearth-error for="year_in_effect" />
        </li>
        <li>
            <x-forms.label for="reference" :value="__('Reference / Link')" />
            <x-hearth-input type="url" name="reference" hinted :value="old('reference', $lawPolicySource?->reference)" />
            {{-- <x-hearth-hint> compoents render markdown and autolink urls. Using `\` to escape values prevents the autolinking --}}
            <x-hearth-hint for="reference">{{ __('Web link or URL to source. Example: https\://www\.example\.com/') }}</x-hearth-hint>
            <x-hearth-error for="reference" />
        </li>
        <li>
            <x-forms.label for="type" :value="__('Type')" />
            <x-hearth-select
                name="type"
                :options="\App\Enums\LawPolicyTypes::options()->nullable('')->toArray()"
                :selected="old('type', $lawPolicySource?->type->value ?? '')"
            />
            <x-hearth-error for="type" />
        </li>
        <li>
            <fieldset>
                <legend id="is_core-label">{{ __('Effect on Legal Capacity') }}</legend>
                    <x-hearth-radio-buttons name="is_core"
                        :options="\App\Enums\LegalCapacityEffects::options()->toArray()"
                        :checked="old('is_core', $lawPolicySource?->is_core)"
                    />
                    <x-hearth-error for="is_core" />
            </fieldset>
        </li>
        <li>
            <a href="{{ \localized_route($lawPolicySource ? 'lawPolicySources.show' : 'lawPolicySources.index', $lawPolicySource) }}">Cancel</a>
        </li>
        <li>
            <button type="submit">{{ __('Submit') }}</button>
        </li>
    </ul>
</form>
