<?php

namespace App\Http\Controllers;

use App\Models\LawPolicySource;

class LawPolicySourceController extends Controller
{
    public function create()
    {
        // placeholder for create controller
    }

    public function index()
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

    public function show(LawPolicySource $lawPolicySource)
    {
        return view('law-policy-sources.show', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }
}
