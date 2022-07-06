<x-app-layout>
    <x-slot name="header">
        {{ Breadcrumbs::render('lawPolicySources.edit', $lawPolicySource) }}
        <h1 itemprop="name">{{ __('Edit Law or Policy Source') }}</h1>
    </x-slot>

    @auth
        <x-forms.error-summary :anchors="['is_core' => 'is_core-1']" />

        <x-forms.law-policy-source :lawPolicySource="$lawPolicySource" />
    @endauth
</x-app-layout>