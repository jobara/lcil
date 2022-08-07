@props(['regimeAssessment', 'level' => 4])
<x-heading :level="$level">
    {{ get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality) }}
    @auth
        - ({{ $regimeAssessment->status->labels()[$regimeAssessment->status->value] }})
    @endauth
</x-heading>
@isset($regimeAssessment->description)
    <p>{{ $regimeAssessment->description }}</p>
@endisset
<dl>
    @isset($regimeAssessment->year_in_effect)
        <dt>{{ __('Effective date:') }}</dt>
        <dd>{{ $regimeAssessment->year_in_effect }}</dd>
    @endisset
    @if (isset($regimeAssessment->lawPolicySources) && $regimeAssessment->lawPolicySources->count() > 0)
        <dt>{{ __('Law and Policy Sources:') }}</dt>
        <dd>
            <ul>
                @foreach ($regimeAssessment->lawPolicySources as $lawPolicySource)
                    <li>
                        {{ $lawPolicySource->name }}
                    </li>
                @endforeach
            </ul>
        </dd>
    @endif
    <dt>{{ __('Modified:') }}</dt>
    <dd>{{ ($regimeAssessment->modified_at ?? $regimeAssessment->created_at)->format('Y-m-d') }}</dd>
    <dt>{{ __('Created:') }}</dt>
    <dd>{{ $regimeAssessment->created_at->format('Y-m-d') }}</dd>
</dl>
<a href="{{ localized_route('regimeAssessments.show', $regimeAssessment) }}">@auth(){{ __('View / Edit Details') }}@else{{ __('View Details') }}@endauth</a>
