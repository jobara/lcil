<?php

namespace App\Http\Requests;

use App\Enums\EvaluationAssessments;
use Illuminate\Validation\Rules\Enum;

class StoreEvaluationRequest extends RedirectFormRequest
{
    /**
     * The anchor on the redirect URL that users should be sent to if validation fails.
     *
     * @var string
     */
    protected $redirectAnchor = '#error-summary__message';

    public function authorize()
    {
        return auth()->check();
    }

    public function attributes(): array
    {
        return [
            'evaluations.*.assessment' => __('Measure Evaluation'),
            'evaluations.*.comment' => __('Measure Evaluation Remarks'),
        ];
    }

    public function messages(): array
    {
        return [
            'evaluations.*.assessment.Illuminate\Validation\Rules\Enum' => 'The :attribute must only include the following: '.implode(', ', EvaluationAssessments::values()).'.',
            'evaluations.*.assessment.Illuminate\Validation\Rules\RequiredIf' => 'The :attribute cannot be empty if the :values is specified.',
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
            'evaluations.*.assessment' => ['required_with:evaluations.*.comment', 'nullable', new Enum(EvaluationAssessments::class)],
            'evaluations.*.comment' => ['nullable', 'string'],
            'evaluations.*.provision_id' => ['required', 'string'],
        ];
    }
}
