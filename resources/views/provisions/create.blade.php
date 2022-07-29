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
                'court_challenge' => 'court-challenge-not-related',
                'decision_making_capability' => 'decision-making-capability-independent',
                'decision_making_capability.0' => 'decision-making-capability-independent',
                'decision_type' => 'decision-type-financial-property',
                'decision_type.0' => 'decision-type-financial-property',
            ]"
        />

    <x-forms.provision :lawPolicySource="$lawPolicySource" />
    @endauth
    <aside>
        <x-law-policy-source-card :lawPolicySource="$lawPolicySource" level="2" expanded />

    </aside>

</x-app-layout>
