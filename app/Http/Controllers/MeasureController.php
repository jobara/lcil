<?php

namespace App\Http\Controllers;

use App\Models\MeasureDimension;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    public function index()
    {
        return view('measures.index', [
            'lcilMeasures' => MeasureDimension::with('indicators.measures')->get()
        ]);
    }
}
