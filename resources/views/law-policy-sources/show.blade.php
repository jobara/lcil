<x-app-layout>
    <x-slot name="header">
        {{-- replace with the correct name from law and policy source --}}
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
            <dd>{{ $lawPolicySource->reference }}</dd>
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

    @isset($lawPolicySource->provisions)
        <section>
            <h2>{{ __('Provisions') }}</h2>
            @foreach ($lawPolicySource->provisions as $provision)
                <h3>{{ __('Section / Subsection: :section', ['section' => $provision->section]) }}</h3>
                <p>{{ $provision->body }}</p>
                @isset($provision->reference)
                    <a href="$provision->reference">{{ __('Section / Subsection: :section Reference', ['section' => $provision->section]) }}</a>
                @endisset
                @isset($provision->type_of_decision)
                    <dl>
                        <dt>{{ __('Type of Decision') }}</dt>
                        <dd>{{ $provision->type_of_decision }}</dd>
                    </dl>

                    @isset($provision->decision_citation)
                        <span>{{ $provision->is_subject_to_challenge }}</span>
                    @endisset
                    @isset($provision->decision_citation)
                        <span>{{ $provision->is_result_of_challenge }}</span>
                    @endisset
                    @isset($provision->decision_citation)
                        <dl>
                            <dt>{{ __('Decision Citation') }}</dt>
                            <dd>{{ $provision->decision_citation }}</dd>
                        </dl>
                    @endisset
                @endisset
            @endforeach
        </section>
    @endisset
</x-app-layout>
