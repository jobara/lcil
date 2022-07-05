<?php

namespace App\Http\Requests;

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProvisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function attributes(): array
    {
        return [
            'section' => __('Section or Subsection (section)'),
            'body' => __('Provision Text (body)'),
            'reference' => __('Reference / Link (reference)'),
            'legal_capacity_approach' => __('Approach to Legal Capacity (legal_capacity_approach)'),
            'decision_making_capability' => __('Decision Making Capability (decision_making_capability)'),
            'decision_making_capability.*' => __('Decision Making Capability (decision_making_capability)'),
            'decision_type' => __('Type of Decision (decision_type)'),
            'decision_type.*' => __('Type of Decision (decision_type)'),
            'court_challenge' => __('Court Challenge (court_challenge)'),
            'decision_citation' => __('Decision Citation (decision_citation)'),
        ];
    }

    public function messages(): array
    {
        return [
            'section.min' => 'The :attribute is required.',
            'section.required' => 'The :attribute is required.',
            'body.required' => 'The :attribute is required.',
            'reference.url' => 'The :attribute format must be in a form like https://example.com or http://example.com.',
            'decision_type.prohibited_if' => 'The :attribute requires the :other indicate that a challenge has been initiated; the current value is: :value',
            'decision_type.*.Illuminate\Validation\Rules\Enum' => 'The :attribute must only include the following: ' . implode(', ', ProvisionDecisionTypes::values()) . '.',
            'legal_capacity_approach.Illuminate\Validation\Rules\Enum' => 'The :attribute must be one of the following: ' . implode(', ', LegalCapacityApproaches::values()) . '.',
            'decision_making_capability.*.Illuminate\Validation\Rules\Enum' => 'The :attribute must only include the following: ' . implode(', ', DecisionMakingCapabilities::values()) . '.',
            'court_challenge.Illuminate\Validation\Rules\Enum' => 'The :attribute must be one of the following: ' . implode(', ', ProvisionCourtChallenges::values()) . '.',
            'decision_citation.prohibited_if' => 'The :attribute requires the :other indicate that a challenge has been initiated; the current value is: :value',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'section' => ['required', 'min:1', 'string'],
            'body' => ['required', 'min:1', 'string'],
            'reference' => ['nullable', 'url'],
            'decision_type' => [
                'nullable',
                'prohibited_if:court_challenge,null,' . ProvisionCourtChallenges::NotRelated->value
            ],
            'decision_type.*' => [new Enum(ProvisionDecisionTypes::class)],
            'legal_capacity_approach' => ['nullable', new Enum(LegalCapacityApproaches::class)],
            'decision_making_capability' => ['nullable'], // need to validate array
            'decision_making_capability.*' => [new Enum(DecisionMakingCapabilities::class)],
            'court_challenge' => ['nullable', new Enum(ProvisionCourtChallenges::class)],
            'decision_citation' => [
                'nullable',
                'string',
                'prohibited_if:court_challenge,null,' . ProvisionCourtChallenges::NotRelated->value
            ],
        ];
    }
}
