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
        if (! request('country')) {
            return view('law-policy-sources.index');
        } else {
            $filters = request('keywords') ? request(['keywords']) : [];
            if (request('country') !== 'all') {
                $filters['jurisdiction'] = request('subdivision') ? request('country') . '-' . request('subdivision') : request('country');
            }

            return view('law-policy-sources.index', [
                'lawPolicySources' => LawPolicySource::filter($filters)
                                        ->orderBy('jurisdiction')
                                        ->orderBy('municipality')
                                        ->orderBy('name')
                                        ->paginate(10)
                                        ->withQueryString(),
            ]);
        }
    }

    /**
     * Display the request Law or Policy source
     *
     * @param LawPolicySource $lawPolicySource the Law or Policy source to display
     *
     * @return View
     */
    public function show(LawPolicySource $lawPolicySource): View
    {
        return view('law-policy-sources.show', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }
}
