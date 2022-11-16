<x-app-layout>
    <x-slot name="title">{{ __('Law and Policy Sources') }}</x-slot>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('Law and Policy Sources') }}</h1>
    </x-slot>

    <div x-data="{country: '{{ old('country', request('country')) }}'}">
        @auth
            <nav class="nav-actions" aria-label="{{  __('Section Actions') }}">
                {{-- push focus to the first focusable element in the search form --}}
                <ul role="list">
                    <li>
                        <a href="#" @click.prevent="$focus.within($refs.search).first()">
                            <div class="icon-round">
                                @svg('gmdi-search', ['aria-hidden' => 'true'])
                            </div>
                            {{ __('Search for sources of law and policy to view or edit') }}
                            @svg('gmdi-arrow-downward', 'icon-inline', ['aria-hidden' => 'true'])
                        </a>
                    </li>
                    <li>
                        <a href="{{ localized_route('lawPolicySources.create') }}">
                            <div class="icon-round">
                                @svg('gmdi-description', ['aria-hidden' => 'true'])
                            </div>
                            {{ __('Create new law or policy source if it does not already exist') }}
                            @svg('gmdi-arrow-forward', 'icon-inline', ['aria-hidden' => 'true'])
                        </a>
                    </li>
                </ul>
            </nav>
        @else
            <p>{{ __('Search for sources of law and policy to view') }}</p>
        @endauth

        <form method="GET">
            <ul role="list" x-ref="search">
                <li>
                    <x-forms.label for="country" :value="__('Country:')" />
                    <x-forms.country-select :country="old('country', request('country'))" :placeholder="__('All countries')" x-model="country" />
                </li>
                <li>
                    <x-forms.label for="subdivision" :value="__('Province / Territory:')" />
                    <x-forms.subdivision-select :country="old('country', request('country'))" :subdivision="old('subdivision', request('subdivision'))"/>
                </li>
                <li>
                    <x-forms.label for="keywords" :value="__('Law or policy name contains keywords:')" />
                    <x-hearth-input type="text" name="keywords" :value="old('keywords', request('keywords'))" />
                </li>
                <li>
                    <button type="submit">{{ __('Search') }}</button>
                </li>
            </ul>
        </form>
    </div>

    <div>
        @isset($lawPolicySources)
            <x-paged-search-summary
                :paginator="$lawPolicySources"
                :country="old('country', request('country'))"
                :subdivision="old('subdivision', request('subdivision'))"
                :keywords="old('keywords', request('keywords'))"
            />
            @if (count($lawPolicySources))
                <ul role="list" class="search-results">
                    @foreach (group_by_jurisdiction($lawPolicySources->items()) as $countryName => $subdivisionGroups)
                        <li>
                            <h2>{{ $countryName }}</h2>
                            <ul role="list" class="search-results__sub">
                                @foreach ($subdivisionGroups as $subdivisionName => $groupedLawPolicySources)
                                    <li>
                                        <h3>{{ $subdivisionName ? $subdivisionName : __('Federal') }}</h3>
                                        <x-law-policy-source-cards :lawPolicySources="$groupedLawPolicySources" />
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
                {{ $lawPolicySources->links() }}
            @endif
        @else
            <p role="status" class="card">{{ __('Search results will appear here') }}</p>
        @endisset
    </div>
</x-app-layout>
