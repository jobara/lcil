<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationRequest;
use App\Models\Evaluation;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EvaluationController extends Controller
{
    public function show(RegimeAssessment $regimeAssessment, Measure $measure): View
    {
        $evaluations = Evaluation::where('regime_assessment_id', $regimeAssessment->id)
            ->where('measure_id', $measure->id)
            ->get();

        return view('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
            'evaluations' => $evaluations,
        ]);
    }

    public function update(RegimeAssessment $regimeAssessment, Measure $measure, StoreEvaluationRequest $request): RedirectResponse
    {
        $evaluations = Evaluation::where('regime_assessment_id', $regimeAssessment->id)
            ->where('measure_id', $measure->id)
            ->get();

        $toUpdate = [];
        $toDelete = [];

        $validated = $request->validated()['evaluations'] ?? [];

        foreach ($validated as $evaluation) {
            if (isset($evaluation['assessment'])) {
                $toUpdate[] = array_merge($evaluation, [
                    'regime_assessment_id' => $regimeAssessment->id,
                    'measure_id' => $measure->id,
                ]);
            }
            Provision::findOrFail($evaluation['provision_id']);
            if (! isset($evaluation['assessment']) && $evaluations->contains(fn ($model) => $evaluation['provision_id'] == $model->provision_id)) {
                $toDelete[] = $evaluations->where('provision_id', $evaluation['provision_id'])->first()->id;
            }
        }

        Evaluation::upsert($toUpdate, ['regime_assessment_id', 'measure_id', 'provision_id'], ['assessment', 'comment']);
        Evaluation::destroy($toDelete);

        return redirect(\localized_route('evaluations.show', [
            'regimeAssessment' => $regimeAssessment,
            'measure' => $measure,
        ]).'#save__message')->with('status', 'saved');
    }
}
