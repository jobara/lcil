<?php

namespace App\Http\Controllers;

use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EvaluationAPIController extends Controller
{
    public function index(): ResourceCollection
    {
        $filters = [];

        if (request('ra_id') !== null) {
            $filters['ra_id'] = request('ra_id');
        }

        if (request('measureCode') !== null) {
            $filters['measureCode'] = request('measureCode');
        }

        if (request('provisionID') !== null) {
            $filters['provisionID'] = request('provisionID');
        }

        if (request('assessment') !== null) {
            $filters['assessment'] = request('assessment');
        }

        $evaluations = Evaluation::filter($filters)
            ->with(['regimeAssessment', 'measure', 'provision'])
            // @phpstan-ignore-next-line
            ->orderBy(RegimeAssessment::select('jurisdiction')
                ->whereColumn('id', 'evaluations.regime_assessment_id')
            )
            // @phpstan-ignore-next-line
            ->orderBy(RegimeAssessment::select('municipality')
                ->whereColumn('id', 'evaluations.regime_assessment_id')
            )
            // @phpstan-ignore-next-line
            ->orderBy(RegimeAssessment::select('ra_id')
                ->whereColumn('id', 'evaluations.regime_assessment_id')
            )
            // @phpstan-ignore-next-line
            ->orderBy(Measure::select('code')
                ->whereColumn('id', 'evaluations.measure_id')
            )
            // @phpstan-ignore-next-line
            ->orderBy(Provision::select('section')
                ->whereColumn('id', 'evaluations.provision_id')
            );

        return EvaluationResource::collection($evaluations->paginate()->withQueryString());
    }

    public function show(Evaluation $evaluation): JsonResource
    {
        $evaluation->loadMissing(['measure', 'provision', 'regimeAssessment']);

        return new EvaluationResource($evaluation);
    }
}
