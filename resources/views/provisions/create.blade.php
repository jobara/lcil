<x-app-layout>
    <x-slot name="title">{{ __('Add Provision: :lawPolicySourceName', ['lawPolicySourceName' => $lawPolicySource->name]) }}</x-slot>
    <x-slot name="header">
        {{ Breadcrumbs::render('provisions.create', $lawPolicySource) }}
        <h1 itemprop="name">{{ __('Add Provision') }}</h1>
        <p>{{ __('Use this form to add a provision to the law or policy source.') }}
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

    <x-forms.provision :lawPolicySource="$lawPolicySource" />
    @endauth
    <aside>
        <x-law-policy-source-card :lawPolicySource="$lawPolicySource" level="2" expanded />

    </aside>

</x-app-layout>
