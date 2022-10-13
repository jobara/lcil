<x-app-layout>
    <x-slot name="title">{{ __('API Tokens') }}</x-slot>
    <x-slot name="header">
        <h1 itemprop="name">{{ __('API Tokens') }}</h1>
    </x-slot>

    @auth
        <x-forms.error-summary />

        @if (session('token'))
            <div id="token-saved" class="notice" role="alert">
                <p>
                    @svg('gmdi-info-o', 'icon-inline', ['aria-hidden' => 'true'])
                    {{ __('Token generated') }}
                </p>
                <p>
                    <strong>{{ session('token') }}</strong>
                </p>
                <p>
                    {{ __('The token has been generated. Securely store the token and use it for making API requests.
                    The token value will not be displayed after you navigate away from this page.') }}
                </p>
            </div>
        @endif
    @endauth

    <p>
        {!! Str::inlineMarkdown(__('API tokens can be used to access to fetch data and for use in other applications.
        The token should be included in the `Aunthentication` header as a `Bearer` token.')) !!}
    </p>

    @auth
        <h2>{{ __('Generate Token') }}</h2>

        <form method="POST" action="{{ route('tokens.store') }}">
            @csrf
            <ul role="list">
                <li>
                    <x-forms.label for="token" :value="__('Token Name')" />
                    <x-hearth-input type="text" name="token" required :value="old('token')" />
                    <x-hearth-error for="token" />
                </li>
                <li>
                    <button type="submit">{{ __('Generate Token') }}</button>
                </li>
            </ul>
        </form>

        <h2>{{ __('Manage Tokens') }}</h2>

        @if(count($tokens))
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Token') }}</th>
                        <th>{{ __('Revoke') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tokens as $token)
                        <tr>
                            <td>{{ $token->name }}</td>
                            <td x-data="{}" class="icon-table-cell">
                                <a href="{{ route('tokens.destroy', $token->id) }}" aria-label="{{ __('Revoke :tokenName', ['tokenName' => $token->name]) }}" x-on:click.prevent="$refs.form.submit()">
                                    @svg('gmdi-delete-forever-o', ['aria-hidden' => 'true'])
                                </a>
                                <form method="POST" action="{{ route('tokens.destroy', $token->id) }}" x-ref="form">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>{{ __('No tokens available.') }}</p>
        @endif
    @endauth
</x-app-layout>
