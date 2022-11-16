<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedirectFormRequest extends FormRequest
{
    /**
     * The anchor on the redirect URL that users should be sent to if validation fails.
     *
     * @var ?string
     */
    protected $redirectAnchor;

    protected function getRedirectUrl(): string
    {
        $redirectUrl = parent::getRedirectUrl();
        $anchor = $this->redirectAnchor ?? '';

        return "{$redirectUrl}{$anchor}";
    }
}
