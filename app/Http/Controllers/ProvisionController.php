<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProvisionRequest;
use App\Models\LawPolicySource;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProvisionController extends Controller
{
    public function create(LawPolicySource $lawPolicySource): View
    {
        return view('provisions.create', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }

    public function store(LawPolicySource $lawPolicySource, StoreProvisionRequest $request): RedirectResponse
    {
        $lawPolicySource->provisions()->create($request->safe()->all());
        return redirect(\localized_route('lawPolicySources.show', $lawPolicySource));
    }
}
