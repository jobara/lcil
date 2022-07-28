<?php

namespace App\Http\Controllers;

use App\Enums\RegimeAssessmentStatuses;
use App\Models\MeasureDimension;
use App\Models\RegimeAssessment;
use Illuminate\Contracts\View\View;

class RegimeAssessmentController extends Controller
{
    public function index(): View
    {
        // The pagination strips empty/null queries, so assume that we're searching all countries if a page query is set
        if (! array_key_exists('country', request(['country'])) && empty(request(['page']))) {
            return view('regimeAssessments.index');
        }

        $filters = request('keywords') ?
            request(['keywords']) :
            [];

        if (request('country') !== null) {
            $filters['jurisdiction'] = request('subdivision') ?
            request('country').'-'.request('subdivision') :
            request('country');
        }

        if (auth()->check()) {
            if (request('status') !== null) {
                $filters['status'] = request('status');
            }
        } else {
            $filters['status'] = RegimeAssessmentStatuses::Published;
        }

        return view('regimeAssessments.index', [
            'regimeAssessments' => RegimeAssessment::filter($filters)
                ->orderBy('jurisdiction')
                ->orderBy('municipality')
                ->orderBy('ra_id', 'desc')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function show(RegimeAssessment $regimeAssessment): View
    {
        $regimeAssessment->load('lawPolicySources');

        return view('regimeAssessments.show', [
            'regimeAssessment' => $regimeAssessment,
            'measureDimensions' => MeasureDimension::get(),
        ]);
    }
}
