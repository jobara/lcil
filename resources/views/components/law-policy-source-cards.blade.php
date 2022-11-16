@props(['lawPolicySources', 'level' => 4])
@if (count($lawPolicySources))
    <ul role="list">
        @foreach ($lawPolicySources as $lawPolicySource)
            <li class="card">
                <x-law-policy-source-card :lawPolicySource="$lawPolicySource" :level="$level" />
            </li>
        @endforeach
    </ul>
@endif
