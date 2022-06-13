@props(['lawPolicySource', 'level' => 4])
<h{{ clamp($level, 1, 6) }}>
    <a href="{{ localized_route('lawPolicySources.show', $lawPolicySource->slug) }}">{{ $lawPolicySource->name }}</a>
</h{{ $level }}>
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
