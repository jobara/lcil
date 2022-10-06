<?php

namespace App\Http\Controllers;

use App\Models\RegimeAssessment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;

class WelcomeController extends Controller
{
    public function show(): View
    {
        $data = [
            'latestActivity' => Audit::latest()->take(4)->get(),
            'regimeAssessments' => [],
        ];

        if (Auth::check()) {
            $latestRegimeAssessmentAudits = Audit::latest()
                ->where('auditable_type', RegimeAssessment::class)
                ->where('user_id', AUTH::id())
                ->get();

            $data['regimeAssessments'] = RegimeAssessment::find($latestRegimeAssessmentAudits->pluck('auditable_id')->unique()->take(2))->sortByDesc('updated_at');
        }

        return view('welcome', $data);
    }
}
