@props(['regimeAssessments', 'level' => 4])
@if (count($regimeAssessments))
    <ul role="list">
        @foreach ($regimeAssessments as $regimeAssessment)
            <li class="card">
                <x-regime-assessment-card :regimeAssessment="$regimeAssessment" :level="$level" />
            </li>
        @endforeach
    </ul>
@endif
