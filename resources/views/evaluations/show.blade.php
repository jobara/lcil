<x-app-layout>
    @php
        $jurisdiction = get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality);
    @endphp
    <x-slot name="title">{{ __('Regime Assessment Evaluation - :code: :jurisdiction', ['code' => $measure->code, 'jurisdiction' => $jurisdiction]) }}</x-slot>
    <x-slot name="header">
        {{ Breadcrumbs::render('regimeAssessments.evaluation', $regimeAssessment, $measure) }}
        <h1 itemprop="name">{{ __('Legal Capacity Measure :code', ['code' => $measure->code]) }}</h1>
        <dl>
            <dt>{{ $measure->code }}: {{ $measure->title }}</dt>
            <dd>{{ $measure->description }}</dd>
        </dl>
    </x-slot>

    <h2>{{ __('Make an evaluation') }}</h2>
    <p>
        {{
            __('Review the provisions from the sources of law and policy and evaluate how well the provision satisfies the measure :code: :title.', [
                'code' => $measure->code,
                'title' => $measure->title,
            ])
        }}
    </p>

    @if ($regimeAssessment->lawPolicySources->count())
        @auth
            <x-forms.error-summary :anchors="['evaluations.*.assessment' => true, 'evaluations.*.comment' => true]" />

            <form method="POST" action="{{ route('evaluations.update', ['regimeAssessment' => $regimeAssessment, 'measure' => $measure]) }}">
            @csrf
        @endauth
        @foreach ($regimeAssessment->lawPolicySources as $lawPolicySource)
            <section x-data="{open: false}">
                <h3 id="{{ $lawPolicySource->slug }}">
                    <button
                        type="button"
                        x-on:click="open = !open"
                        x-bind:aria-expanded="open"
                        aria-controls="{{ "{$lawPolicySource->slug}-content" }}"
                    >
                        {{ $lawPolicySource->name }}
                    </button>
                </h3>
                <dl>
                    @isset($lawPolicySource->type)
                        <dt>{{ __('Type:') }}</dt>
                        <dd>{{ $lawPolicySource->type->labels()[$lawPolicySource->type->value] }}</dd>
                    @endisset
                    <dt>{{ __('Jurisdiction:') }}</dt>
                    <dd>{{ $jurisdiction }}</dd>
                    @isset($lawPolicySource->year_in_effect)
                        <dt>{{ __('Year in effect:') }}</dt>
                        <dd>{{ $lawPolicySource->year_in_effect }}</dd>
                    @endisset
                    @isset($lawPolicySource->reference)
                        <dt>{{ __('Reference:') }}</dt>
                        <dd><a href="" aria-labelledby="{{ $lawPolicySource->slug }}">{{ __('Link') }}</a></dd>
                    @endisset
                    <dt>{{ __('Provisions:') }}</dt>
                    <dd>{{ $lawPolicySource->provisions->count() }} @if ($lawPolicySource->provisions->count())({{ __(':count evaluated', ['count' => $evaluations->count()]) }})@endif</dd>
                </dl>
                @auth
                    <a href="{{ localized_route('lawPolicySources.show', $lawPolicySource) }}">{{ __('Edit / add provisions') }}</a>
                @endauth

                <div id="{{ "{$lawPolicySource->slug}-content" }}" x-show="open" x-cloak>
                    @forelse ($lawPolicySource->provisions->sortBy('section') as $provision)
                        <x-provision-card :lawPolicySource="$lawPolicySource" :provision="$provision" :level="4" />
                        @auth
                            @php
                                $evaluation = $evaluations->where('provision_id', $provision->id)->first();
                                $assessmentName = 'evaluations['.$provision->id.'][assessment]';
                                $assessmentID = \Illuminate\Support\Str::slug($assessmentName);
                                $commentName = 'evaluations['.$provision->id.'][comment]';
                                $commentID = \Illuminate\Support\Str::slug($commentName);
                            @endphp
                            <div x-data="{assessment: '{{ old('evaluations.'.$provision->id.'.assessment', $evaluation?->assessment?->value ?? '') }}'}">
                                <x-hearth-input type="hidden" name="{{ 'evaluations['.$provision->id.'][provision_id]' }}" value="{{ $provision->id }}" />
                                <x-forms.label for="{{ $assessmentID }}" :value="__('Measure Evaluation:')" />
                                <x-hearth-hint for="{{ $assessmentID }}">
                                    {{ __('How well does this provision satisfy the measure :title exclusion?', ['title' => $measure->title]) }}
                                </x-hearth-hint>
                                @php
                                    $name = "evaluations[{$provision->id}][assessment]";
                                @endphp
                                <x-hearth-select
                                    x-model="assessment"
                                    name="{{ $assessmentName }}"
                                    id="{{ $assessmentID }}"
                                    hinted
                                    :options="\App\Enums\EvaluationAssessments::options()->nullable('')->toArray()"
                                    :selected="old('evaluations.'.$provision->id.'.assessment', $evaluation?->assessment?->value ?? '')"
                                />
                                <x-hearth-error for="{{ $assessmentID }}" />
                                <x-hearth-label for="{{ $commentID }}" :value="__('Measure Evaluation Remarks')" />
                                <x-hearth-textarea
                                    id="{{ $commentID }}"
                                    name="{{ $commentName }}"
                                    :value="old('evaluations.'.$provision->id.'.comment', $evaluation?->comment ?? '')"
                                    x-bind:disabled="!assessment"
                                />
                                <x-hearth-error for="{{ $commentID }}" />
                            </div>
                        @endauth
                        @guest
                            @php
                                $evaluation = $evaluations->where('provision_id', $provision->id)->first();
                            @endphp
                            @isset($evaluation)
                                <h4>{{ __('Measure Evaluation') }}</h4>
                                <p>
                                    {{ __('How well does this provision satisfy the measure No disability-based exclusions exclusion?') }}
                                    <strong>{{ App\Enums\EvaluationAssessments::labels()[$evaluation->assessment->value] }}</strong>
                                </p>
                                @isset($evaluation->comment)
                                    <p>{{ $evaluation->comment }}</p>
                                @endisset
                            @endisset
                        @endguest
                    @empty
                        <p>{{ __('No provisions have been added.') }}</p>
                    @endforelse
                </div>
            </section>
        @endforeach
        @auth
                <button type="submit">{{ __('Save') }}</button>
                @if (session('status') === 'saved')
                    <x-duration id="save__message" role="status">
                        {!! __('Last save successful :duration ago', ['duration' => $component->getDurationMarkup()]) !!}
                    </x-duration>
                @endif
            </form>
        @endauth
    @endif
</x-app-layout>
