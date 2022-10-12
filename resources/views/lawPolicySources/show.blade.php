<x-app-layout>
    <x-slot name="title">{{ $lawPolicySource->name }}</x-slot>
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
            <dd>{{ $lawPolicySource->type->labels()[$lawPolicySource->type->value] }}</dd>
        @endisset

        @isset($lawPolicySource->is_core)
            <dt>{{ __('Effect on Legal Capacity') }}</dt>
            <dd>
                {{ \App\Enums\LegalCapacityEffects::labels()[$lawPolicySource->is_core] }}
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
            <div class="card">
                <x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" />
            </div>
        @empty
            <p>{{ __('No provisions have been added.') }}</p>
        @endforelse
    </section>
</x-app-layout>
