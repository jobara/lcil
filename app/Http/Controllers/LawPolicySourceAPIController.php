<?php

namespace App\Http\Controllers;

use App\Http\Resources\LawPolicySourceResource;
use App\Models\LawPolicySource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LawPolicySourceAPIController extends Controller
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

        $lawPolicySources = LawPolicySource::filter($filters)
            ->orderBy('jurisdiction')
            ->orderBy('municipality')
            ->orderBy('name')
            ->withCount('provisions');

        return LawPolicySourceResource::collection($lawPolicySources->paginate()->withQueryString());
    }

    public function show(LawPolicySource $lawPolicySource): JsonResource
    {
        return new LawPolicySourceResource($lawPolicySource->loadCount('provisions'));
    }
}
