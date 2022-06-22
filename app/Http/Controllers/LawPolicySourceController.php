<?php

namespace App\Http\Controllers;

use App\Enums\LawPolicyTypes;
use App\Http\Requests\StoreLawPolicySourceRequest;
use App\Models\LawPolicySource;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Enum;

class LawPolicySourceController extends Controller
{
    public function create()
    {
        return view('lawPolicySources.create');
    }

    /**
     * Display listing of Law and Policy sources, optionally filtered by jurisdiction and/or keywords.
     *
     * @return View
     */
    public function index(): View
    {
        if (! array_key_exists('country', request(['country']))) {
            return view('lawPolicySources.index');
        }

        $filters = request('keywords') ?
            request(['keywords']) :
            [];

        if (request('country') !== null) {
            $filters['jurisdiction'] = request('subdivision') ?
            request('country') . '-' . request('subdivision') :
            request('country');
        }

        return view('lawPolicySources.index', [
            'lawPolicySources' => LawPolicySource::filter($filters)
                                    ->orderBy('jurisdiction')
                                    ->orderBy('municipality')
                                    ->orderBy('name')
                                    ->paginate(10)
                                    ->withQueryString(),
        ]);
    }

    public function show(LawPolicySource $lawPolicySource): View
    {
        return view('lawPolicySources.show', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }

    public function store(StoreLawPolicySourceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $jurisdiction = isset($validated['subdivision']) ?
            "{$validated['country']}-{$validated['subdivision']}" :
            "{$validated['country']}";

        $data = $request->safe()
                        ->merge(['jurisdiction' => $jurisdiction])
                        ->except(['country', 'subdivision']);

        $lawPolicySource = LawPolicySource::create($data);

        return redirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    }
}
