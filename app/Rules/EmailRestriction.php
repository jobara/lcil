<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class EmailRestriction implements InvokableRule
{
    /**
     * Verify that an e-mail is from an allowed domain if restrictions are enabled
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (! config('settings.registration.restricted')) {
            return;
        }

        $allowlist = config('settings.registration.allowlist');
        $blocklist = config('settings.registration.blocklist');
        $domain = substr($value, strpos($value, '@') + 1);
        $failMessage = __('Sorry, at the moment registration for :app is restricted.', ['app' => config('app.name')]);

        if (in_array($domain, $blocklist)) {
            $fail($failMessage);
        }

        if ($allowlist && count($allowlist) && ! in_array($domain, $allowlist)) {
            $fail($failMessage);
        }
    }
}
