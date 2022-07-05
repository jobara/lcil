@props(['lawPolicySource', 'level' => 4, 'expanded' => null])
<h{{ clamp($level, 1, 6) }}>
    <a href="{{ localized_route('lawPolicySources.show', $lawPolicySource->slug) }}">{{ $lawPolicySource->name }}</a>
</h{{ clamp($level, 1, 6) }}>
<dl>
    @php
        $jurisdictionName = get_jurisdiction_name($lawPolicySource->jurisdiction, $lawPolicySource->municipality)
    @endphp
    <dt>{{ __('Jurisdiction') }}</dt>
    <dd>{{ $jurisdictionName }}</dd>
    @isset($lawPolicySource->year_in_effect)
        <dt>{{ __('Year in Effect') }}</dt>
        <dd>{{ $lawPolicySource->year_in_effect }}</dd>
    @endisset
    @isset($lawPolicySource->type)
        <dt>{{ __('Type') }}</dt>
        <dd>{{ $lawPolicySource->type->value }}</dd>
    @endisset
    @unless ($expanded)
        <dt>{{ __('Provisions') }}</dt>
        <dd>{{ count($lawPolicySource->provisions) }}</dd>
    @endunless
</dl>
@isset($expanded)
    @isset($lawPolicySource->reference)
        <a href="{{ $lawPolicySource->reference }}">{{ __('Reference') }}</a>
    @endisset
    <h{{ clamp($level + 1, 1, 6) }}>{{ __('Provisions') }}</h{{ clamp($level + 1, 1, 6) }}>
    <ol>
        @foreach ($lawPolicySource->provisions->sortBy('section') as $provision)
            <li>
                @auth
                    {{-- set the href when editting added --}}
                    <a href="">{{ $provision->section }}</a>
                @endauth
                @guest
                    {{ $provision->section }}
                @endguest
            </li>
        @endforeach
    </ol>
@endisset
