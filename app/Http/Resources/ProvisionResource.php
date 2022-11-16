<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Provision */
class ProvisionResource extends JsonResource
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
            'section' => $this->section,
            'body' => $this->body,
            'reference' => $this->reference,
            'decision_type' => $this->decision_type,
            'legal_capacity_approach' => $this->legal_capacity_approach,
            'decision_making_capability' => $this->decision_making_capability,
            'court_challenge' => $this->court_challenge,
            'decision_citation' => $this->decision_citation,
            'lawPolicySource' => LawPolicySourceResource::make($this->whenLoaded('lawPolicySource')),
            'evaluations' => EvaluationResource::collection($this->whenLoaded('evaluations')),
            'evaluations_count' => $this->whenCounted('evaluations'),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
