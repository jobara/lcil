<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JurisdictionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return get_countries();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $country
     * @return \Illuminate\Http\Response
     */
    public function show($country)
    {
        return get_subdivisions($country);
    }
}
