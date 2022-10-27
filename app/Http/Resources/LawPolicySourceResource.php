<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\LawPolicySource */
class LawPolicySourceResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'is_core' => $this->is_core,
            'reference' => $this->reference,
            'jurisdiction' => $this->jurisdiction,
            'jurisdiction_name' => get_jurisdiction_name($this->jurisdiction, $this->municipality),
            'municipality' => $this->municipality,
            'year_in_effect' => $this->year_in_effect,
            'provisions' => ProvisionResource::collection($this->whenLoaded('provisions')),
            'provisions_count' => $this->whenCounted('provisions'),
            'regimeAssessments' => RegimeAssessmentResource::collection($this->whenLoaded('regimeAssessments')),
            'regimeAssessments_count' => $this->whenCounted('regimeAssessments'),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
