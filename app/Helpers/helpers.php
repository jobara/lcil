<?php

use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

if (! function_exists('get_jurisdiction_name')) {
    function get_jurisdiction_name($code, $municipality = null, $locale = 'en', $separator = ', ')
    {
        $countryRepository = new CountryRepository();

        $codes = explode('-', $code);
        $name = [];

        if (isset($codes[1])) {
            // get_region_name is from Hearth's helper functions
            $subdivision = get_region_name($codes[1], [$codes[0]], $locale);

            if (isset($subdivision)) {
                if (isset($municipality)) {
                    $name[] = ucfirst($municipality);
                }
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

if (! function_exists('get_countries')) {
    function get_countries($locale = 'en')
    {
        $countryRepository = new CountryRepository();

        return $countryRepository->getList($locale);
    }
}

if (! function_exists('get_subdivisions')) {
    function get_subdivisions($code, $locale = 'en')
    {
        $subdivisionRepository = new SubdivisionRepository();
        $subdivisions = [];

        if (isset($code)) {
            $subdivisions = $subdivisionRepository->getList([$code], $locale);
        }

        return $subdivisions;
    }
}
