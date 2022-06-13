@props(['lawPolicySources', 'level' => 4])
@if (count($lawPolicySources))
    <ul>
        @foreach ($lawPolicySources as $lawPolicySource)
            <li>
                <x-law-policy-source-card :lawPolicySource="$lawPolicySource" :level="$level" />
            </li>
        @endforeach
    </ul>
@endif
