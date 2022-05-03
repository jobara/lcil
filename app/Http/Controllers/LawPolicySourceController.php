<?php

namespace App\Http\Controllers;

use App\Models\LawPolicySource;
use Illuminate\Http\Request;

class LawPolicySourceController extends Controller
{
    public function show(LawPolicySource $lawPolicySource)
    {
        return view('law-policy-sources.show', [
            'lawPolicySource' => $lawPolicySource,
        ]);
    }
}
