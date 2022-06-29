<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLawPolicySourceRequest;
use App\Models\LawPolicySource;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LawPolicySourceController extends Controller
{
    public function create(): View
    {
        return view('lawPolicySources.create');
    }

    public function edit(LawPolicySource $lawPolicySource): View
    {
        return view('lawPolicySources.edit', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }

    /**
     * Display listing of Law and Policy sources, optionally filtered by jurisdiction and/or keywords.
     *
     * @return View
     */
    public function index(): View
    {
        // The pagination strips empty/null queries, so assume that we're searching all countries if a page query is set
        if (! array_key_exists('country', request(['country'])) && empty(request(['page']))) {
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
        $data = $this->assembleData($request);
        $lawPolicySource = LawPolicySource::create($data);

        return redirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    }

    public function update(StoreLawPolicySourceRequest $request, LawPolicySource $lawPolicySource): RedirectResponse
    {
        $data = $this->assembleData($request);
        $lawPolicySource->fill($data);

        if ($lawPolicySource->isDirty()) {
            $lawPolicySource->save();
        }

        return redirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    }

    protected function assembleData(StoreLawPolicySourceRequest $request): array
    {
        $validated = $request->validated();

        $jurisdiction = isset($validated['subdivision']) ?
        "{$validated['country']}-{$validated['subdivision']}" :
        "{$validated['country']}";

        return $request->safe()
            ->merge(['jurisdiction' => $jurisdiction])
            ->except(['country', 'subdivision']);
    }
}
