@props(['lawPolicySources', 'checked' => collect([]), 'level' => 3])
<ul role="list">
    @foreach (group_by_jurisdiction($lawPolicySources) as $countryName => $subdivisionGroups)
        <li>
            <x-heading :level="$level">{{ $countryName }}</x-heading>
            <ul role="list">
                @foreach ($subdivisionGroups as $subdivisionName => $groupedLawPolicySources)
                    <li>
                        <x-heading :level="$level + 1">
                            {{ $subdivisionName ? $subdivisionName : __('Federal') }}
                        </x-heading>
                        @if (count($groupedLawPolicySources))
                            <ul role="list">
                                @foreach ($groupedLawPolicySources as $lawPolicySource)
                                    <li>
                                        <x-heading :level="$level + 2">
                                            {{ $lawPolicySource->name }}
                                        </x-heading>
                                        @php
                                            $lpName = 'lawPolicySources['.$lawPolicySource->id.']'
                                        @endphp
                                        <x-hearth-checkbox :name="$lpName" :checked="$checked->contains($lawPolicySource)" />
                                        <x-hearth-label :for="$lpName" :value="__('Add to regime assessment')" />
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
                                            <dt>{{ __('Provisions') }}</dt>
                                            <dd>{{ count($lawPolicySource->provisions) }}</dd>
                                        </dl>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
