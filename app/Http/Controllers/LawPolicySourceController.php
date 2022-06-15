<?php

namespace App\Http\Controllers;

use App\Models\LawPolicySource;
use Illuminate\Contracts\View\View;

class LawPolicySourceController extends Controller
{
    public function create()
    {
        // placeholder for create controller
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
}
