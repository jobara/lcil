<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class JurisdictionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(get_countries());
    }

    /**
     * Display the set of available subdivisions for the specified country in JSON
     *
     * @param  string  $country An ISO 3166-1 alpha-2 code
     *
     * @return JsonResponse
     */
    public function show(string $country): JsonResponse
    {
        return response()->json(get_subdivisions($country));
    }
}
