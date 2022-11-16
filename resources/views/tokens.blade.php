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

    {!!
        Str::markdown(__('API tokens can be used to access to fetch data using the [API](:apiURL).
        The token should be included in the `Aunthentication` header as a `Bearer` token.', [
            'apiURL' => localized_route('api.show')
        ]))
    !!}

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
                            <td class="icon-table-cell">
                                <form method="POST" action="{{ route('tokens.destroy', $token->id) }}">
                                    @csrf
                                    <button type="submit" aria-label="{{ __('Revoke :tokenName token', ['tokenName' => $token->name]) }}">
                                        @svg('gmdi-delete-forever-o', ['aria-hidden' => 'true'])
                                    </button>
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
