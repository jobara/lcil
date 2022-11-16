<x-app-layout>
    <x-slot name="title">{{ __('Create a Law or Policy Source') }}</x-slot>
    <x-slot name="header">
        {{ Breadcrumbs::render('lawPolicySources.create') }}
        <h1 itemprop="name">{{ __('Create a Law or Policy Source') }}</h1>
    </x-slot>

    @auth
        <x-forms.error-summary :anchors="['is_core' => 'is-core-1']" />

        <x-forms.law-policy-source />
    @endauth

</x-app-layout>
