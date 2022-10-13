<!-- Site Navigation -->
<nav aria-label="{{ __('Site Navigation') }}" class="nav-site">

    <!-- Navigation Links -->
    <ul role="list">
        <x-nav-link :href="localized_route('lawPolicySources.index')" :active="request()->routeIs(locale() . '.lawPolicySources.index')">
            {{ __('Law and Policy Sources') }}
        </x-nav-link>
        <x-nav-link :href="localized_route('regimeAssessments.index')" :active="request()->routeIs(locale() . '.regimeAssessments.index')">
            {{ __('Regime Assessments') }}
        </x-nav-link>
        <x-nav-link :href="localized_route('about')" :active="request()->routeIs(locale() . '.about')">
            {{ __('About') }}
        </x-nav-link>
    </ul>
</nav>
