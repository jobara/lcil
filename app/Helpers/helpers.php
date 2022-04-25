<?php

use CommerceGuys\Addressing\Country\CountryRepository;

if (!function_exists('get_jurisdiction_name')) {
    function get_jurisdiction_name($code, $municipality = null, $locale = 'en', $separator = ', ')
    {
        $countryRepository = new CountryRepository();

        $codes = explode('-', $code);
        $name = [];

        if (isset($codes[1])) {
            $subdivision = get_region_name($codes[1], [$codes[0]], $locale);

            if (isset($subdivision)) {
                if (isset($municipality)) {
                    $name[] = ucfirst($municipality);
                }

                // get_region_name is from Hearth's helper functions
                $name[] = $subdivision;
            }
        }

        try {
            $name[] = $countryRepository->get($codes[0], $locale)->getName();
        } catch (CommerceGuys\Addressing\Exception\UnknownCountryException $e) {
            return null;
        }

        return implode($separator, $name);
    }
}
