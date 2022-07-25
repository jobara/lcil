<?php

namespace App\Http\Controllers;

use App\Models\MeasureDimension;
use App\Models\RegimeAssessment;
use Illuminate\Contracts\View\View;

class RegimeAssessmentController extends Controller
{
    public function show(RegimeAssessment $regimeAssessment): View
    {
        $regimeAssessment->load('lawPolicySources');

        return view('regimeAssessments.show', [
            'regimeAssessment' => $regimeAssessment,
            'measureDimensions' => MeasureDimension::get(),
        ]);
    }
}
