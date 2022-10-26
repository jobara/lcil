<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTokenRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->user()->tokens()->where('id', $id)->delete();

        return redirect(\localized_route('tokens.show'));
    }

    public function show(): View
    {
        return view('tokens', [
            'tokens' => Auth::user()->tokens,
        ]);
    }

    public function showDocs(): View
    {
        $routeNames = [
            'api.evaluations.index',
            'api.evaluations.show',
            'api.lawPolicySources.index',
            'api.lawPolicySources.show',
            'api.regimeAssessments.index',
            'api.regimeAssessments.show',
            'api.regimeAssessments.evaluations',
        ];

        $routeURIs = array_map(fn ($routeName) => request()->getSchemeAndHttpHost().'/'.app('router')->getRoutes()->getByName($routeName)->uri(), $routeNames);

        return view('api-docs', [
            'endPoints' => array_combine($routeNames, $routeURIs),
        ]);
    }

    public function store(StoreTokenRequest $request): RedirectResponse
    {
        $token = $request->user()->createToken($request->safe()->all()['token']);

        return redirect(\localized_route('tokens.show').'#token-saved')
            ->with('token', $token->plainTextToken);
    }
}
