<?php

namespace App\Http\Requests;

use App\Enums\RegimeAssessmentStatuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRegimeAssessmentStatusRequest extends FormRequest
{
    /**
     * The anchor on the redirect URL that users should be sent to if validation fails.
     *
     * @var string
     */
    protected $redirectAnchor = '#error-summary__message';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    public function attributes(): array
    {
        return [
            'status' => __('Regime Assessment Status (status)'),
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The :attribute must be one of the following: '.implode(', ', RegimeAssessmentStatuses::values()).'.',
            'status.Illuminate\Validation\Rules\Enum' => 'The :attribute must be one of the following: '.implode(', ', RegimeAssessmentStatuses::values()).'.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => ['required', new Enum(RegimeAssessmentStatuses::class)],
        ];
    }
}
