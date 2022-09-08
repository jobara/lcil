@props(['regimeAssessment' => null, 'id' => 'ra-form'])
<form
    {{
        $attributes->merge([
            'id' => $id,
            'method' => 'POST',
            'action' => route($regimeAssessment ? 'regimeAssessments.update' : 'regimeAssessments.store', $regimeAssessment),
        ])
    }}
>
    @csrf
    @isset($regimeAssessment)
        @method('patch')
    @endisset

    <x-hearth-input type="hidden" name="status" :value="$regimeAssessment?->status->value ?? \App\Enums\RegimeAssessmentStatuses::Draft->value" />
    <ul role="list" x-data="{country: '{{ old('country', parse_country_code($regimeAssessment?->jurisdiction)) }}'}">
        <li>
            <x-forms.label for="country" :value="__('Country (required)')" />
            <x-forms.country-select :country="old('country', parse_country_code($regimeAssessment?->jurisdiction))" x-model="country" required />
            <x-hearth-error for="country" />
        </li>
        <li>
            <x-forms.label for="subdivision" :value="__('Province / Territory')" />
            <x-forms.subdivision-select
                :country="old('country', parse_country_code($regimeAssessment?->jurisdiction))"
                :subdivision="old('subdivision', parse_subdivision_code($regimeAssessment?->jurisdiction))"
            />
            <x-hearth-error for="subdivision" />
        </li>
        <li>
            <x-forms.label for="municipality" :value="__('Municipality')" />
            <x-hearth-input type="text" name="municipality" :value="old('municipality', $regimeAssessment?->municipality)" hinted />
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
                :value="old('year_in_effect', $regimeAssessment?->year_in_effect)"
            />
            <x-hearth-hint for="year_in_effect">{{ __('YYYY format. Example: 2022.') }}</x-hearth-hint>
            <x-hearth-error for="year_in_effect" />
        </li>

        <li>
            <x-forms.label for="description" :value="__('Description')" />
            <x-hearth-textarea name="description">{{ old('description', $regimeAssessment?->description) }}</x-hearth-textarea>
            <x-hearth-error for="description" />
        </li>
    </ul>
</form>

<section>
    <h2>{{ __('Choose Available Law and Policy Sources') }}</h2>
    <div>
        {{ __('Possible actions:') }}
        <ul>
            <li>{{ __('Search for sources of law and policy to add to this regime assessment.') }}</li>
            <li>
                {!! Str::inlineMarkdown(__('[Create Law and Policy Source](:url) if it doesn’t already exist.', ['url' => \localized_route('lawPolicySources.create')])); !!}
            </li>
        </ul>
    </div>
</section>

<section>
    <h2>{{ __('Refine Selection') }}</h2>
    <div>
        {{ __('Possible actions:') }}
        <ul>
            <li>{{ __('Refine chosen sources of law and policy by removing them from the list below.') }}</li>
            <li>{{ __('Add more sources of law and policy by searching above.') }}</li>
            <li>{{ __('Submit when done.') }}</li>
        </ul>
    </div>
</section>

<button type="submit" form="{{ $id }}">{{ __('Submit') }}</button>
