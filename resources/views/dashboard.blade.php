<x-app-layout>
    <x-slot name="title">{{ __('hearth::dashboard.title') }}</x-slot>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('hearth::dashboard.title') }}</h1>
    </x-slot>

    <p>{{ __('hearth::dashboard.welcome', ['name' => Auth::user()->name]) }}</p>
</x-app-layout>
