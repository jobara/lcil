<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\RegimeAssessment */
class RegimeAssessmentResource extends JsonResource
{
    /**
     * Indicates if the resource's collection keys should be preserved.
     *
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'jurisdiction' => $this->jurisdiction,
            'jurisdiction_name' => get_jurisdiction_name($this->jurisdiction, $this->municipality),
            'municipality' => $this->municipality,
            'description' => $this->description,
            'year_in_effect' => $this->year_in_effect,
            'status' => $this->status,
            'lawPolicySources' => LawPolicySourceResource::collection($this->whenLoaded('lawPolicySources')),
            'lawPolicySources_count' => $this->whenCounted('lawPolicySources'),
            'evaluations' => EvaluationResource::collection($this->whenLoaded('evaluations')),
            'evaluations_count' => $this->whenCounted('evaluations'),
            'ra_id' => $this->ra_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
