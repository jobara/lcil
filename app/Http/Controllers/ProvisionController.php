<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProvisionRequest;
use App\Models\LawPolicySource;
use App\Models\Provision;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

class ProvisionController extends Controller
{
    public function create(LawPolicySource $lawPolicySource): View
    {
        return view('provisions.create', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }

    public function edit(LawPolicySource $lawPolicySource, string $slug): View
    {
        $provision = $lawPolicySource->provisions->firstWhere('slug', $slug);

        if (empty($provision)) {
            throw (new ModelNotFoundException)->setModel(Provision::class);
        }

        return view('provisions.edit', [
            'lawPolicySource' => $lawPolicySource,
            'provision' => $provision,
        ]);
    }

    public function store(LawPolicySource $lawPolicySource, StoreProvisionRequest $request): RedirectResponse
    {
        $lawPolicySource->provisions()->create($request->safe()->all());

        return redirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    }

    public function update(LawPolicySource $lawPolicySource, string $slug, StoreProvisionRequest $request): RedirectResponse
    {
        $provision = $lawPolicySource->provisions->firstWhere('slug', $slug);

        if (empty($provision)) {
            throw (new ModelNotFoundException)->setModel(Provision::class);
        }

        $data = $request->safe()->all();
        $provision->fill($data);

        if (isset($data['court_challenge']) && $data['court_challenge'] === 'not_related') {
            $provision->decision_type = null;
            $provision->decision_citation = null;
        }

        if ($provision->isDirty()) {
            $provision->save();
        }

        return redirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    }
}
