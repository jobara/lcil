<x-app-layout>
    <x-slot name="header">
        {{ Breadcrumbs::render('lawPolicySources.show', $lawPolicySource) }}
        <h1 itemprop="name">{{ $lawPolicySource->name }}</h1>
    </x-slot>

    <dl>
        @php
            $jurisdictionName = get_jurisdiction_name($lawPolicySource->jurisdiction, $lawPolicySource->municipality)
        @endphp
        <dt>{{ __('Jurisdiction') }}</dt>
        <dd>{{ $jurisdictionName }}</dd>

        <dt>{{ __('Year in Effect') }}</dt>
        <dd>{{ $lawPolicySource->year_in_effect }}</dd>

        @isset($lawPolicySource->reference)
            <dt>{{ __('Reference') }}</dt>
            <dd><a href="{{ $lawPolicySource->reference }}">{{ $lawPolicySource->reference }}</a></dd>
        @endisset

        @isset($lawPolicySource->type)
            <dt>{{ __('Type') }}</dt>
            <dd>{{ $lawPolicySource->type->value }}</dd>
        @endisset

        @isset($lawPolicySource->is_core)
            <dt>{{ __('Effect on Legal Capacity') }}</dt>
            <dd>
                @if ($lawPolicySource->is_core)
                    {{ __('Core - directly affects legal capacity') }}
                @else
                    {{ __('Supplemental - indirectly affects legal capacity') }}
                @endif
            </dd>
        @endisset
    </dl>

    @auth
        <a href="{{ \localized_route('lawPolicySources.edit', $lawPolicySource) }}">
            {{ __('Edit :name', ['name' => $lawPolicySource->name]) }}
        </a>
    @endauth

    <section>
        <h2>{{ __('Provisions') }}</h2>
        @auth
            <a href="{{ \localized_route('provisions.create', $lawPolicySource) }}">{{ __('Add Provision') }}</a>
        @endauth
        @forelse ($lawPolicySource->provisions->sortBy('section') as $provision)
            <h3>{{ __('Section / Subsection: :section', ['section' => $provision->section]) }}</h3>
            @auth
                <a href="{{ localized_route('provisions.edit', [$lawPolicySource, $provision->slug]) }}">{{ __('Edit') }}</a>
            @endauth
            <div>{!! $provision->body !!}</div>
            @isset($provision->reference)
                <a href="{{ $provision->reference }}">{{ __('Section / Subsection: :section Reference', ['section' => $provision->section]) }}</a>
            @endisset
            @if (isset($provision->legal_capacity_approach) or isset($provision->decision_making_capability))
                <h4>{{ __('Other Information') }}</h4>
                <ul role="list">
                    @isset($provision->legal_capacity_approach)
                        <li>{{ __(':approach approach to legal capacity', ['approach' => $provision->legal_capacity_approach->value]) }}</li>
                    @endisset
                    @isset($provision->decision_making_capability)
                        @if (count($provision->decision_making_capability) === 1)
                            @if ($provision->decision_making_capability[0] === App\Enums\DecisionMakingCapabilities::Independent->value)
                                 <li>{{ __('Recognizes Independent Only decision making capability') }}</li>
                            @else
                                 <li>{{ __('Recognizes Interdependent Only decision making capability') }}</li>
                            @endif
                        @else
                            <li>{{ __('Recognizes Independent and Interdependent decision making capability') }}</li>
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
        @empty
            <p>{{ __('No provisions have been added.') }}</p>
        @endforelse
    </section>
</x-app-layout>
