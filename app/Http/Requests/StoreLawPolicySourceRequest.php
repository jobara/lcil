<?php

namespace App\Http\Requests;

use App\Enums\LawPolicyTypes;
use Illuminate\Validation\Rules\Enum;

class StoreLawPolicySourceRequest extends RedirectFormRequest
{
    /**
     * The anchor on the redirect URL that users should be sent to if validation fails.
     *
     * @var string
     */
    protected $redirectAnchor = '#error-summary__message';

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function attributes(): array
    {
        return [
            'country' => __('Country (country)'),
            'is_core' => __('Effect on Legal Capacity (is_core)'),
            'municipality' => __('Municipality (municipality)'),
            'name' => __('Law or Policy Name (name)'),
            'reference' => __('Reference / Link (reference)'),
            'subdivision' => __('Province / Territory (subdivision)'),
            'type' => __('Type (type)'),
            'year_in_effect' => __('Year in Effect (year_in_effect)'),
        ];
    }

    public function messages(): array
    {
        return [
            'country.min' => 'A :attribute, specified using an ISO 3166-1 alpha-2 country code, is required.',
            'country.required' => 'A :attribute, specified using an ISO 3166-1 alpha-2 country code, is required.',
            'is_core.boolean' => 'The :attribute must be true or false.',
            'name.required' => 'The :attribute is required.',
            'reference.url' => 'The :attribute format must be in a form like https://example.com or http://example.com.',
            'subdivision.min' => 'The :attribute must be specified using the subdivision portion of an ISO 3166-2 code.',
            'subdivision.required_with' => 'The :attribute cannot be empty if the :values is specified.',
            'type.Illuminate\Validation\Rules\Enum' => 'The :attribute must be one of the following: ' . implode(', ', LawPolicyTypes::values()) . '.',
            'year_in_effect.integer' => 'The :attribute must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.',
            'year_in_effect.max' => 'The :attribute must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.',
            'year_in_effect.min' => 'The :attribute must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.',
            'year_in_effect.numeric' => 'The :attribute must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.',
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
            'name' => ['required', 'min:1', 'string'],
            'type' => ['nullable', new Enum(LawPolicyTypes::class)],
            'is_core' => ['nullable', 'boolean'],
            'reference' => ['nullable', 'url'],
            'country' => ['required', 'min:2', 'string'],
            'subdivision' => ['required_with:municipality', 'nullable', 'min:2', 'string'],
            'municipality' => ['nullable', 'string'],
            'year_in_effect' => [
                'nullable',
                'numeric',
                'integer',
                'min:' . config('settings.year.min'),
                'max:' . config('settings.year.max'),
            ],
        ];
    }
}
