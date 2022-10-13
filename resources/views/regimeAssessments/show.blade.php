<x-app-layout>
    @php
        $jurisdiction = get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality);
    @endphp
    <x-slot name="title">{{ __('Regime Assessment Summary: :jurisdiction', ['jurisdiction' => $jurisdiction]) }}</x-slot>
    <x-slot name="header">
        {{ Breadcrumbs::render('regimeAssessments.show', $regimeAssessment) }}
        <h1 itemprop="name">
            <span>{{ __('Regime Assessment Summary') }}</span>
            <span>{{ $jurisdiction }}</span>
            @auth
                <span>({{ \App\Enums\RegimeAssessmentStatuses::labels()[$regimeAssessment->status->value] }})</span>
            @endauth
        </h1>
        <p>{{  $regimeAssessment->description }}</p>
    </x-slot>

    @auth
        <x-forms.error-summary />
    @endauth

    <h2>{{ __('Measures') }}</h2>
    <p>
        @php
            $numMeasures = 0;
            foreach ($measureDimensions as $measureDimension) {
                foreach($measureDimension->indicators as $indicator) {
                    $numMeasures += $indicator->measures->count();
                }
            }
        @endphp
        <span>{{ __(
            'There are :numMeasures legal capacity measures divided into :numDimensions dimensions. Provisions from
            sources of law or policy are evaluated against these measures to show how well a regime supports legal
            capacity.',
            [
                'numMeasures' => $numMeasures,
                'numDimensions' => $measureDimensions->count(),
            ]
        ) }}
        <a href="{{ localized_route('about') }}">{{ __('More about Legal Capacity Measurements') }}</a>
    </p>
    @auth
        <span>{{ __('Possible actions:') }}</span>
        <ul>
            <li>{{ __('Choose a measure to evaluate.') }}</li>
            <li>{{ __('Change assessment status to “Draft”, “Needs Review”, “Published”.') }}</li>
        </ul>
    @endauth

        @auth
        <aside>
            <h2 id="ra-status-heading">{{ __('Regime Assessment Status') }}</h2>

            <form method="POST" action="{{ route('regimeAssessments.updateStatus', $regimeAssessment) }}">
                @csrf
                @method('patch')

                <ul>
                    <li>
                        <x-hearth-select
                            name="status"
                            aria-labelledby="ra-status-heading"
                            required
                            :options="\App\Enums\RegimeAssessmentStatuses::options()->toArray()"
                            :selected="old('status', $regimeAssessment?->status?->value)"
                        />
                        <x-hearth-error for="status" />
                    </li>
                    <li><button type="submit">{{ __('Save') }}</button></li>
                </ul>
            </form>
        </aside>
    @endauth

    <aside>
        <h2>{{ __('Regime Assessment Details') }}</h2>
        <dl>
            <dt>{{ __('Jurisdiction:') }}</dt>
            <dd>{{ get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality) }}</dd>

            @isset($regimeAssessment->description)
                <dt>{{ __('Description:') }}</dt>
                <dd>{{ $regimeAssessment->description }}</dd>
            @endisset

            @isset($regimeAssessment->year_in_effect)
                <dt>{{ __('Effective Data:') }}</dt>
                <dd>{{ $regimeAssessment->year_in_effect }}</dd>
            @endisset

            <dt>{{ __('ID:') }}</dt>
            <dd>{{ $regimeAssessment->ra_id }}</dd>
        </dl>

        @if ($regimeAssessment->lawPolicySources->count())
            <h3>{{ __('Law and Policy Sources') }}</h3>
            <ul>
                @foreach ($regimeAssessment->lawPolicySources->sortBy('name') as $lawPolicySource)
                    <li>
                        <a href="{{ localized_route('lawPolicySources.show', $lawPolicySource) }}">{{ $lawPolicySource->name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif

        @auth
            <a href="{{ \localized_route('regimeAssessments.edit', $regimeAssessment) }}">
                {{ __('View / Edit Details') }}
            </a>
        @endauth

    </aside>

    @php
        $numProvisions = $regimeAssessment->lawPolicySources->reduce(function ($carry, $lawPolicySource) {
            return ($carry ?? 0) + $lawPolicySource->provisions->count();
        });
    @endphp
    @foreach ($measureDimensions as $measureDimension)
        <details>
            <summary>{{ $measureDimension->code }} {{ $measureDimension->description }}</summary>

            <ol>
                @foreach ($measureDimension->indicators as $indicators)
                    @foreach ($indicators->measures as $measure)
                        <li>
                            <a href="{{ localized_route('evaluations.show', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]) }}">{{ $measure->code }}@if ($measure->title): {{ $measure->title }}@endif</a>
                            <ul>
                                @php
                                    $evaluations = $regimeAssessment->evaluations->where('measure_id', $measure->id);
                                @endphp
                                @foreach (App\Enums\EvaluationAssessments::values() as $evaluationAssessment)
                                    <li>{{ $evaluations->where('assessment', App\Enums\EvaluationAssessments::from($evaluationAssessment))->count() }} {{ $evaluationAssessment }}</li>
                                @endforeach
                                <li>{{ __(':count do not apply', ['count' => ($numProvisions - $evaluations->count())]) }}</li>
                            </ul>
                        </li>
                    @endforeach
                @endforeach
            </ol>
        </details>
    @endforeach
</x-app-layout>
