@props(['lawPolicySource', 'provision', 'level' => 3])

<x-heading :level="$level">{{ __('Section / Subsection: :section', ['section' => $provision->section]) }}</x-heading>
@auth
    <a href="{{ localized_route('provisions.edit', [$lawPolicySource, $provision->slug]) }}">{{ __('Edit') }}</a>
@endauth
<div>{!! $provision->body !!}</div>
@isset($provision->reference)
    <a href="{{ $provision->reference }}">{{ __('Section / Subsection: :section Reference', ['section' => $provision->section]) }}</a>
@endisset
@php
    $hasLegalCapacityApproach = isset($provision->legal_capacity_approach) && $provision->legal_capacity_approach !== \App\Enums\LegalCapacityApproaches::NotApplicable;
@endphp
@if ($hasLegalCapacityApproach or isset($provision->decision_making_capability))
    <h4>{{ __('Other Information') }}</h4>
    <ul role="list">
        @if ($hasLegalCapacityApproach)
            <li>{{ __(':approach approach to legal capacity', ['approach' => $provision->legal_capacity_approach->labels()[$provision->legal_capacity_approach->value]]) }}</li>
        @endif
        @isset($provision->decision_making_capability)
            @if (count($provision->decision_making_capability) === 1)
                <li>
                    {{ __('Recognizes :capability Only decision making capability', [
                        'capability' => App\Enums\DecisionMakingCapabilities::labels()[$provision->decision_making_capability[0]]
                    ]) }}
                </li>
            @else
                <li>
                    {{ __('Recognizes :capability and :capability_other decision making capability', [
                        'capability' => App\Enums\DecisionMakingCapabilities::labels()[$provision->decision_making_capability[0]],
                        'capability_other' => App\Enums\DecisionMakingCapabilities::labels()[$provision->decision_making_capability[1]],
                    ]) }}
                </li>
            @endif
        @endisset
    </ul>
@endif
@if (isset($provision->court_challenge) && $provision->court_challenge !== \App\Enums\ProvisionCourtChallenges::NotRelated)
    <h4>{{ __('Legal Information') }}</h4>
    <ul role="list">
        @if($provision->court_challenge === \App\Enums\ProvisionCourtChallenges::ResultOf)
            <li>{{ __('This provision is the result of a court challenge.') }}</li>
        @else
            <li>{{ __('This provision is, or has been, subject to a constitutional or other court challenge.') }}</li>
        @endif
        @isset($provision->decision_type)
            <li>{{ __('Type of Decision: :decision_types', ['decision_types' => implode(', ', array_map(function (string $value) {
                $toNames = [
                    App\Enums\ProvisionDecisionTypes::Financial->value => __('Financial Property'),
                    App\Enums\ProvisionDecisionTypes::Health->value => __('Health Care'),
                    App\Enums\ProvisionDecisionTypes::Personal->value => __('Personal Life and Care'),
                ];
                return $toNames[$value];
            }, $provision->decision_type))]) }}</li>
        @endisset
        @isset($provision->decision_citation)
            <li>{{ __('Decision Citation: :citation', ['citation' => $provision->decision_citation]) }}</li>
        @endisset
    </ul>
@endif
