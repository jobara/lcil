<?php

namespace App\Http\Controllers;

use App\Enums\RegimeAssessmentStatuses;
use App\Http\Requests\StoreRegimeAssessmentRequest;
use App\Http\Requests\UpdateRegimeAssessmentStatusRequest;
use App\Models\LawPolicySource;
use App\Models\MeasureDimension;
use App\Models\RegimeAssessment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegimeAssessmentController extends Controller
{
    public function create(Request $request): View
    {
        return view('regimeAssessments.create', [
            'lawPolicySources' => LawPolicySource::all()->sortBy([
                ['jurisdiction', 'asc'],
                ['municipality', 'asc'],
                ['name', 'asc'],
            ])->all(),
        ]);
    }

    public function edit(RegimeAssessment $regimeAssessment): View
    {
        return view('regimeAssessments.edit', [
            'regimeAssessment' => $regimeAssessment->load('lawPolicySources'),
            'lawPolicySources' => LawPolicySource::all()->sortBy([
                ['jurisdiction', 'asc'],
                ['municipality', 'asc'],
                ['name', 'asc'],
            ])->all(),
        ]);
    }

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
        if (! Auth::check() && $regimeAssessment->status !== RegimeAssessmentStatuses::Published) {
            abort(404);
        }

        $regimeAssessment->load('lawPolicySources');

        return view('regimeAssessments.show', [
            'regimeAssessment' => $regimeAssessment,
            'measureDimensions' => MeasureDimension::get(),
        ]);
    }

    public function store(StoreRegimeAssessmentRequest $request): RedirectResponse
    {
        $data = $this->assembleData($request);
        $data['status'] ??= RegimeAssessmentStatuses::Draft->value;
        $regimeAssessment = RegimeAssessment::create($data);

        $regimeAssessment->lawPolicySources()->sync(array_keys($data['lawPolicySources'] ?? []));

        return redirect(\localized_route('regimeAssessments.show', $regimeAssessment));
    }

    public function update(StoreRegimeAssessmentRequest $request, RegimeAssessment $regimeAssessment): RedirectResponse
    {
        $data = $this->assembleData($request);
        $regimeAssessment->fill($data);

        $regimeAssessment->lawPolicySources()->sync(array_keys($data['lawPolicySources'] ?? []));

        if ($regimeAssessment->isDirty()) {
            $regimeAssessment->save();
        }

        return redirect(\localized_route('regimeAssessments.show', $regimeAssessment));
    }

    public function updateStatus(UpdateRegimeAssessmentStatusRequest $request, RegimeAssessment $regimeAssessment): RedirectResponse
    {
        $regimeAssessment->fill($request->validated());

        if ($regimeAssessment->isDirty()) {
            $regimeAssessment->save();
        }

        return redirect(\localized_route('regimeAssessments.show', $regimeAssessment));
    }

    protected function assembleData(StoreRegimeAssessmentRequest $request): array
    {
        $validated = $request->validated();

        $jurisdiction = isset($validated['subdivision']) ?
        "{$validated['country']}-{$validated['subdivision']}" :
        "{$validated['country']}";

        return $request->safe()
            ->merge(['jurisdiction' => $jurisdiction])
            ->except(['country', 'subdivision']);
    }
}
