<x-app-layout>
    <x-slot name="title">{{ __('Create Regime Assessment') }}</x-slot>
    <x-slot name="header">
        {{ Breadcrumbs::render('regimeAssessments.create') }}
        <h1 itemprop="name">{{ __('Create Regime Assessment') }}</h1>
    </x-slot>

    @auth
        <x-forms.error-summary />

        <x-forms.regime-assessment :lawPolicySources="$lawPolicySources" />
    @endauth

</x-app-layout>
