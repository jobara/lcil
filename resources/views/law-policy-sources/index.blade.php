<x-app-layout>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('Law and Policy Sources') }}</h1>
    </x-slot>

    <section x-data="{country: '{{ old('country', request('country', 'all')) }}'}">
        @auth
            {{-- push focus to the first focusable element in the search form --}}
            <a href="#" @click.prevent="$focus.within($refs.search).first()">
                {{ __('Search for sources of law and policy to view or edit') }}
            </a>
            <a href="{{ localized_route('law-policy-sources.create') }}">{{ __('Create new law or policy source if it does not already exist') }}</a>
        @else
            <p>{{ __('Search for sources of law and policy to view') }}</p>
        @endauth

        <form method="GET" action="">
            <ul x-ref="search">
                <li>
                    <label for="country">{{ __('Country:') }}</label>
                    <x-country-select :country="old('country', request('country', 'all'))" required/>
                </li>
                <li>
                    <label for="subdivision">{{ __('Province / Territory:') }}</label>
                    <x-subdivision-select :country="old('country', request('country', 'all'))" :subdivision="old('subdivision', request('subdivision', ''))"/>
                </li>
                <li>
                    <label for="keywords">{{ __('Law or policy name contains keywords:') }}</label>
                    <input type="text" name="keywords" id="keywords" value="{{ old('keywords', request('keywords')) }}">
                </li>
                <li>
                    <button type="submit">{{ __('Search') }}</button>
                </li>
            </ul>
        </form>
    </section>

    <section>
        @isset($lawPolicySources)
            <x-paged-search-summary
                :paginator="$lawPolicySources"
                :country="old('country', request('country', 'all'))"
                :subdivision="old('subdivision', request('subdivision', ''))"
                :keywords="old('keywords', request('keywords'))"
            />
            @if (count($lawPolicySources))
                <ul>
                    @foreach ($lawPolicySources as $lawPolicySource)
                        <li>
                            <h2><a href="{{ localized_route('law-policy-sources.show', $lawPolicySource->slug) }}">{{ $lawPolicySource->name }}</a></h2>
                            <dl>
                                @php
                                    $jurisdictionName = get_jurisdiction_name($lawPolicySource->jurisdiction, $lawPolicySource->municipality)
                                @endphp
                                <dt>{{ __('Jurisdiction') }}</dt>
                                <dd>{{ $jurisdictionName }}</dd>
                                <dt>{{ __('Year in Effect') }}</dt>
                                <dd>{{ $lawPolicySource->year_in_effect }}</dd>
                                @isset($lawPolicySource->type)
                                    <dt>{{ __('Type') }}</dt>
                                    <dd>{{ $lawPolicySource->type->value }}</dd>
                                @endisset
                                <dt>{{ __('Provisions') }}</dt>
                                <dd>{{ count($lawPolicySource->provisions) }}</dd>
                            </dl>
                        </li>
                    @endforeach
                </ul>
                {{ $lawPolicySources->links() }}
            @endif
        @else
            <p role="status">{{ __('Search results will appear here') }}</p>
        @endisset
    </section>
</x-app-layout>
