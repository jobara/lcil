<?php

namespace App\Http\Controllers;

use App\Models\MeasureDimension;
use Illuminate\Contracts\View\View;

class MeasureController extends Controller
{
    public function index(): View
    {
        return view('measures.index', [
            'lcilMeasures' => MeasureDimension::with('indicators.measures')->get(),
        ]);
    }
}
