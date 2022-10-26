<x-app-layout>
    <x-slot name="title">{{ __('hearth::dashboard.title') }}</x-slot>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('hearth::dashboard.title') }}</h1>
    </x-slot>

    <ul>
        <li><a href="{{ localized_route('api.show') }}">{{ __('API') }}</a></li>
        <li><a href="{{ localized_route('tokens.show') }}">{{ __('Manage API Tokens') }}</a></li>
    </ul>
</x-app-layout>
