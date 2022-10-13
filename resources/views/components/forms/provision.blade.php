@props(['lawPolicySource' => null, 'provision' => null])
<form
    {{
        $attributes->merge([
            'method' => 'POST',
            'action' => route($provision ? 'provisions.update' : 'provisions.store', ['lawPolicySource' => $lawPolicySource, 'slug' => $provision?->slug]),
        ])
    }}
>
    @csrf
    @isset($provision)
        @method('patch')
    @endisset
    <ul role="list">
        <li>
            <x-forms.label for="section" :value="__('Section or Subsection (required)')" />
            <x-hearth-input type="text" name="section" :value="old('section', $provision?->section)" required />
            <x-hearth-error for="section" />
        </li>
        <li>
            <x-forms.label for="body" :value="__('Provision Text (required)')" />
            <x-forms.rich-text-editor name="body" aria-labelledby="body-label" required>{!! old('body', $provision?->body) !!}</x-rich-text-editor>
            <x-hearth-error for="body" />

        </li>
        <li>
            <x-forms.label for="reference" :value="__('Reference / Link')" />
            <x-hearth-input type="url" name="reference" hinted :value="old('reference', $provision?->reference)" />
            <x-hearth-hint for="reference">{{ __('Web link or URL to source. Example: https\://www\.example\.com/') }}</x-hearth-hint>
            <x-hearth-error for="reference" />
        </li>
        <li>
            <h2>{{ __('Additional Information') }}</h2>
            <ul role="list">
                <li>
                    <x-forms.label for="legal_capacity_approach" :value="__('Approach to Legal Capacity')" />
                    <x-hearth-select
                        name="legal_capacity_approach"
                        :options="\App\Enums\LegalCapacityApproaches::options()->nullable('')->toArray()"
                        :selected="old('legal_capacity_approach', $provision?->legal_capacity_approach?->value)"
                    />
                    <x-hearth-error for="legal_capacity_approach" />
                </li>
                <li class="inline-label">
                    <fieldset>
                        <legend id="decision_making_capability-label">{{ __('How does this provision recognize decision making capability? Check all that apply.') }}</legend>
                            <x-hearth-checkboxes
                                name="decision_making_capability"
                                :options="\App\Enums\DecisionMakingCapabilities::options()->toArray()"
                                :checked="old('decision_making_capability', $provision?->decision_making_capability ?? [])"
                            />
                            <x-hearth-error for="decision_making_capability" />
                    </fieldset>
                </li>
            </ul>
        </li>
        <li>
            <h2>{{ __('Legal Information') }}</h2>
            <ul role="list" x-data="{
                courtChallenge: '{{ old('court_challenge', $provision?->court_challenge?->value) }}',
                get hasChallenge() { return this.courtChallenge && this.courtChallenge !== '{{ App\Enums\ProvisionCourtChallenges::NotRelated->value }}' },
            }">
                <li class="inline-label">
                    <fieldset>
                        <legend id="court_challenge-label">{{ __('Court Challenge Details. Choose the option that best describes this provision.') }}</legend>
                            <x-hearth-radio-buttons
                                name="court_challenge"
                                :options="\App\Enums\ProvisionCourtChallenges::options()->toArray()"
                                :checked="old('court_challenge', $provision?->court_challenge?->value)"
                                x-model="courtChallenge"
                            />
                            <x-hearth-error for="court_challenge" />
                    </fieldset>
                </li>
                <li class="inline-label">
                    <fieldset x-bind:disabled="!hasChallenge">
                        <legend id="decision_type-label">{{ __('Type of Decision') }}</legend>
                            <x-hearth-checkboxes
                                name="decision_type"
                                :options="\App\Enums\ProvisionDecisionTypes::options()->toArray()"
                                :checked="old('decision_type', $provision?->decision_type ?? [])"
                            />
                            <x-hearth-error for="decision_type" />
                    </fieldset>
                </li>
                <li>
                    <x-forms.label for="decision_citation" :value="__('Decision Citation')" />
                    <x-hearth-textarea name="decision_citation" x-bind:disabled="!hasChallenge">{{ old('decision_citation', $provision?->decision_citation) }}</x-hearth-textarea>
                    <x-hearth-error for="decision_citation" />
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ \localized_route('lawPolicySources.show', $lawPolicySource) }}">Cancel</a>
        </li>
        <li>
            <button type="submit">{{ __('Submit') }}</button>
        </li>
    </ul>
</form>
