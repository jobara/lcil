<x-app-layout>
    <x-slot name="header">
        {{ Breadcrumbs::render('provisions.edit', $lawPolicySource) }}
        <h1 itemprop="name">{{ __('Edit Provision') }}</h1>
    </x-slot>

    @auth
        <x-forms.error-summary
            :anchors="[
                'body' => 'body-editable',
                'court_challenge' => 'court_challenge-not-related',
                'decision_making_capability' => 'decision_making_capability-independent',
                'decision_making_capability.0' => 'decision_making_capability-independent',
                'decision_type' => 'decision_type-financial-property',
                'decision_type.0' => 'decision_type-financial-property',
            ]"
        />

        <x-forms.provision :lawPolicySource="$lawPolicySource" :provision="$provision" />
    @endauth
    <aside>
        <x-law-policy-source-card :lawPolicySource="$lawPolicySource" level="2" expanded />
    </aside>

</x-app-layout>
