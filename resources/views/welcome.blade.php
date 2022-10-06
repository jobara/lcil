<x-app-layout>
    <x-slot name="header">
        <h1 itemprop="name">{{ config('app.name', 'Hearth') }}</h1>
    </x-slot>

    <section>
        <h2>{{ __('Search Regime Assessments') }}</h2>
        <div x-data="{country: ''}">
            <form method="GET" action="{{ localized_route('regimeAssessments.index') }}">
                <ul role="list">
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
    </section>

    @auth
        <section>
            <h2>{{ __('Your Regime Assessments') }}</h2>

            @if (count($regimeAssessments))
                <x-regime-assessment-cards :regimeAssessments="$regimeAssessments" level="3" />
            @else
                <p>{{ __('You have not worked on a Regime Assessment.') }}</p>
            @endif
        </section>

        <section>
            <h2>{{ __('Latest Activity') }}</h2>
            @if (count($latestActivity))
                <ol>
                    @foreach ($latestActivity as $activity)
                        <li>
                            <time datetime="{{ $activity->created_at->toISOString() }}">
                                @if ($activity->created_at->diffInDays())
                                    {!! __(':date - ', ['date' => $activity->created_at->format('Y-m-d')]) !!}
                                @elseif ($activity->created_at->diffInHours())
                                    {!! __(':hours hours ago - ', ['hours' => $activity->created_at->diffInHours()]) !!}
                                @else
                                    {!! __(':minutes minutes ago - ', ['minutes' => $activity->created_at->diffInMinutes()]) !!}
                                @endif
                            </time>

                            @if($activity->auditable_type === 'App\Models\LawPolicySource')
                                @php
                                    $lpData = [
                                        'name' => $activity->auditable->name,
                                        'url' => localized_route('lawPolicySources.show', $activity->auditable),
                                        'user' => $activity->user?->name ?? __('unknown'),
                                    ]
                                @endphp
                                @if ($activity->event === 'created')
                                    {!! Str::inlineMarkdown(__('[:name](:url) law or policy source created by :user', $lpData)) !!}
                                @else
                                    {!! Str::inlineMarkdown(__('[:name](:url) law or policy source modified by :user', $lpData)) !!}
                                @endif
                            @endif

                            @if($activity->auditable_type === 'App\Models\Provision')
                                @php
                                    $provData = [
                                        'section' => $activity->auditable->section,
                                        'lawPolicy' => $activity->auditable->lawPolicySource->name,
                                        'url' => localized_route('lawPolicySources.show', $activity->auditable->lawPolicySource),
                                        'user' => $activity->user?->name ?? __('unknown'),
                                    ]
                                @endphp
                                @if ($activity->event === 'created')
                                    {!! Str::inlineMarkdown(__('Provision :section added to [:lawPolicy](:url) by :user', $provData)) !!}
                                @else
                                    {!! Str::inlineMarkdown(__('Provision :section of [:lawPolicy](:url) modified by :user', $provData)) !!}
                                @endif
                            @endif

                            @if($activity->auditable_type === 'App\Models\RegimeAssessment')
                                @php
                                    $raData = [
                                        'jurisdiction' => get_jurisdiction_name($activity->auditable->jurisdiction, $activity->auditable->municipality),
                                        'url' => localized_route('regimeAssessments.show', $activity->auditable),
                                        'user' => $activity->user?->name ?? __('unknown'),
                                    ]
                                @endphp
                                @if ($activity->event === 'created')
                                    {!! Str::inlineMarkdown(__('[:jurisdiction](:url) regime assessment created by :user', $raData)) !!}
                                @else
                                    {!! Str::inlineMarkdown(__('[:jurisdiction](:url) regime assessment modified by :user', $raData)) !!}
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ol>
            @else
                <p>{{ __('There is no recent activity.') }}</p>
            @endif
        </section>
    @endauth
</x-app-layout>
