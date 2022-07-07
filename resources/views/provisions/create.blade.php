<x-app-layout>
    <x-slot name="header">
        {{ Breadcrumbs::render('provisions.create', $lawPolicySource) }}
        <h1 itemprop="name">{{ __('Add Provision') }}</h1>
        <p>{{ __('Use this form to add a provision to the law or policy source.') }}
    </x-slot>

    @auth
        <x-forms.error-summary
            :anchors="[
                'court_challenge' => 'court_challenge-not_related',
                'decision_making_capability' => 'decision_making_capability-independent',
                'decision_making_capability.0' => 'decision_making_capability-independent',
                'decision_type' => 'decision_type-financial_property',
                'decision_type.0' => 'decision_type-financial_property',
            ]"
        />

        <form
            method="POST"
            action="{{ route('provisions.store', $lawPolicySource) }}"
        >
            @csrf
            <ul role="list">
                <li>
                    <x-forms.label for="section" :value="__('Section or Subsection (required)')" />
                    <x-hearth-input type="text" name="section" :value="old('section')" required />
                    <x-hearth-error for="section" />
                </li>
                <li>
                    <x-forms.label for="body" :value="__('Provision Text (required)')" />
                    <x-hearth-textarea name="body" required>{{ old('body') }}</x-hearth-textarea>
                    <x-hearth-error for="body" />
                </li>
                <li>
                    <x-forms.label for="reference" :value="__('Reference / Link')" />
                    <x-hearth-input type="url" name="reference" hinted :value="old('reference')" />
                    <x-hearth-hint for="reference">{{ __('Web link or URL to source. Example: https://www.example.com/') }}</x-hearth-hint>
                    <x-hearth-error for="reference" />
                </li>
                <li>
                    <h2>{{ __('Additional Information') }}</h2>
                    <ul>
                        <li>
                            <x-forms.label for="legal_capacity_approach" :value="__('Approach to Legal Capacity')" />
                            <x-hearth-select
                                name="legal_capacity_approach"
                                :options="array_merge(['' => ''], to_associative_array(\App\Enums\LegalCapacityApproaches::values(), MB_CASE_TITLE))"
                                :selected="old('legal_capacity_approach')"
                            />
                            <x-hearth-error for="legal_capacity_approach" />
                        </li>
                        <li>
                            <fieldset>
                                <legend id="decision_making_capability-label">{{ __('How does this provision recognize decision making capability? Check all that apply.') }}</legend>
                                    <x-hearth-checkboxes
                                        name="decision_making_capability"
                                        :options="array_merge([], to_associative_array(\App\Enums\DecisionMakingCapabilities::values(), MB_CASE_TITLE))"
                                        :checked="old('decision_making_capability', [])"
                                    />
                                    <x-hearth-error for="decision_making_capability" />
                            </fieldset>
                        </li>
                    </ul>
                </li>
                <li>
                    <h2>{{ __('Legal Information') }}</h2>
                    <ul x-data="{
                        courtChallenge: '{{ old('court_challenge') }}',
                        get hasChallenge() { return this.courtChallenge && this.courtChallenge !== '{{ App\Enums\ProvisionCourtChallenges::NotRelated->value }}' },
                    }">
                        <li>
                            <fieldset>
                                <legend id="court_challenge-label">{{ __('Court Challenge Details. Choose the option that best describes this provision.') }}</legend>
                                    <x-hearth-radio-buttons
                                        name="court_challenge"
                                        :options="array_combine(\App\Enums\ProvisionCourtChallenges::values(), [
                                            __('Not related to a court challenge.'),
                                            __('Is or has been subject to a constitutional or other court challenge.'),
                                            __('Is the result of a court challenge.'),
                                        ])"
                                        :checked="old('court_challenge')"
                                        x-model="courtChallenge"
                                    />
                                    <x-hearth-error for="court_challenge" />
                            </fieldset>
                        </li>
                        <li>
                            <fieldset x-bind:disabled="!hasChallenge">
                                <legend id="decision_type-label">{{ __('Type of Decision') }}</legend>
                                    <x-hearth-checkboxes
                                        name="decision_type"
                                        :options="array_combine(\App\Enums\ProvisionDecisionTypes::values(), [
                                            __('Financial and Property'),
                                            __('Health Care'),
                                            __('Personal Life and Care'),
                                        ])"
                                        :checked="old('decision_type', [])"
                                    />
                                    <x-hearth-error for="decision_type" />
                            </fieldset>
                        </li>
                        <li>
                            <x-forms.label for="decision_citation" :value="__('Decision Citation')" />
                            <x-hearth-textarea name="decision_citation" x-bind:disabled="!hasChallenge">{{ old('decision_citation') }}</x-hearth-textarea>
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
    @endauth
    <aside>
        <x-law-policy-source-card :lawPolicySource="$lawPolicySource" level="2" expanded />

    </aside>

</x-app-layout>
