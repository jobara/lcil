<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Evaluation */
class EvaluationResource extends JsonResource
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
            'assessment' => $this->assessment,
            'comment' => $this->comment,
            'regimeAssessment' => RegimeAssessmentResource::make($this->whenLoaded('regimeAssessment')),
            'measure' => MeasureResource::make($this->whenLoaded('measure')),
            'provision' => ProvisionResource::make($this->whenLoaded('provision')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
