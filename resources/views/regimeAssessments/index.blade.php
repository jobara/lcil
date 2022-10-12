<x-app-layout>
    <x-slot name="title">{{ __('Regime Assessments') }}</x-slot>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('Regime Assessments') }}</h1>
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
                            {{ __('Search for regime assessments') }}
                            @svg('gmdi-arrow-downward', 'icon-inline', ['aria-hidden' => 'true'])
                        </a>
                    </li>
                    <li>
                        <a href="{{ localized_route('regimeAssessments.create') }}">
                            <div class="icon-round">
                                @svg('gmdi-description', ['aria-hidden' => 'true'])
                            </div>
                            {{ __('Create new regime assessment if it does not already exist') }}
                            @svg('gmdi-arrow-forward', 'icon-inline', ['aria-hidden' => 'true'])
                        </a>
                    </li>
                </ul>
            </nav>
        @else
            <p>{{ __('Search for regime assessments') }}</p>
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
                    <x-forms.label for="keywords" :value="__('Description contains keywords:')" />
                    <x-hearth-input type="text" name="keywords" :value="old('keywords', request('keywords'))" />
                </li>
                @auth
                    <li>
                        <x-forms.label for="status" :value="__('Status:')" />
                        <x-hearth-select
                            name="status"
                            :options="\App\Enums\RegimeAssessmentStatuses::options()->nullable('')->toArray()"
                            :selected="old('status', request('status'))"
                        />
                    </li>
                @endauth
                <li>
                    <button type="submit">{{ __('Search') }}</button>
                </li>
            </ul>
        </form>
    </div>

    <div>
        @isset($regimeAssessments)
            <x-paged-search-summary
                :paginator="$regimeAssessments"
                :country="old('country', request('country'))"
                :subdivision="old('subdivision', request('subdivision'))"
                :keywords="old('keywords', request('keywords'))"
            />
            @if (count($regimeAssessments))
                <ul role="list" class="search-results">
                    @foreach (group_by_jurisdiction($regimeAssessments->items()) as $countryName => $subdivisionGroups)
                        <li>
                            <h2>{{ $countryName }}</h2>
                            <ul role="list">
                                @foreach ($subdivisionGroups as $subdivisionName => $groupedRegimeAssessments)
                                    <li>
                                        <h3>{{ $subdivisionName ? $subdivisionName : __('Federal') }}</h3>
                                        <x-regime-assessment-cards :regimeAssessments="$groupedRegimeAssessments" />
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
                {{ $regimeAssessments->links() }}
            @endif
        @else
            <p role="status">{{ __('Search results will appear here') }}</p>
        @endisset
    </div>
</x-app-layout>
