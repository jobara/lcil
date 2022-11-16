<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegimeAssessmentResource;
use App\Models\RegimeAssessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RegimeAssessmentAPIController extends Controller
{
    public function index(): ResourceCollection
    {
        $filters = request('keywords') ?
            request(['keywords']) :
            [];

        if (request('country') !== null) {
            $filters['jurisdiction'] = request('subdivision') ?
                request('country').'-'.request('subdivision') :
                request('country');
        }

        if (request('status') !== null) {
            $filters['status'] = request('status');
        }

        $regimeAssessments = RegimeAssessment::filter($filters)
            ->orderBy('jurisdiction')
            ->orderBy('municipality')
            ->orderBy('ra_id', 'desc')
            ->with(['evaluations.measure', 'evaluations.provision', 'lawPolicySources'])
            ->withCount(['evaluations', 'lawPolicySources']);

        return RegimeAssessmentResource::collection($regimeAssessments->paginate()->withQueryString());
    }

    public function show(RegimeAssessment $regimeAssessment): JsonResource
    {
        $regimeAssessment->loadMissing(['evaluations.measure', 'evaluations.provision', 'lawPolicySources'])
            ->loadCount(['evaluations', 'lawPolicySources']);

        return new RegimeAssessmentResource($regimeAssessment);
    }

    public function evaluations(RegimeAssessment $regimeAssessment): RedirectResponse
    {
        return redirect()->action(
            [
                EvaluationAPIController::class,
                'index',
            ],
            [
                'ra_id' => $regimeAssessment->ra_id,
                'measureCode' => request('measureCode'),
                'provisionID' => request('provisionID'),
                'assessment' => request('assessment'),
            ]
        );
    }
}
