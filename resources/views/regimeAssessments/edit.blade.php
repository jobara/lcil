<x-app-layout>
    <x-slot name="title">{{ __('Edit Regime Assessment: :jurisdiction', ['jurisdiction' => get_jurisdiction_name($regimeAssessment->jurisdiction, $regimeAssessment->municipality)]) }}</x-slot>
    <x-slot name="header">
        {{ Breadcrumbs::render('regimeAssessments.edit', $regimeAssessment) }}
        <h1 itemprop="name">{{ __('Edit Regime Assessment') }}</h1>
    </x-slot>

    @auth
        <x-forms.error-summary />

        <x-forms.regime-assessment :regimeAssessment="$regimeAssessment" :lawPolicySources="$lawPolicySources" />
    @endauth

</x-app-layout>
