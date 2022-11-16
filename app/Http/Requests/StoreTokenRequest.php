<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTokenRequest extends FormRequest
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
            'token' => __('Token Name (token)'),
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'A :attribute is required.',
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
            'token' => ['required', 'string'],
        ];
    }
}
