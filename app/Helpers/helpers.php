<?php

use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

if (! function_exists('get_jurisdiction_name')) {
    /**
     * Get the human readable name of the jurisdiction.
     * The name is constructed in the order of Municipality, Subdivision, Country.
     * Note: The municipality is returned as is, and not localized.
     *
     * @param string $code An ISO 3166-1 alpha-2 or ISO-3166-2 code.
     * @param ?string $municipality (optional) The name of a municipality (city/local/etc.)
     * @param string $locale An ISO 639-1 language code.
     * @param string $separator A string which will be inserted between the parts of the jurisdiction
     *
     * @return ?string The jurisdiction name, or null if the provided country code is not found.
     */
    function get_jurisdiction_name(string $code, ?string $municipality = null, string $locale = 'en', string $separator = ', '): ?string
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
    /**
     * Returns the list of all available countries.
     *
     * @param string $locale An ISO 639-1 language code.
     *
     * @return array<string, string> The list of all available countries
     */
    function get_countries(string $locale = 'en'): array
    {
        $countryRepository = new CountryRepository();

        return $countryRepository->getList($locale);
    }
}

if (! function_exists('get_subdivisions')) {
    /**
     * Returns the list of all available subdivisions for the specified country .
     *
     * @param null|string $code An ISO 3166-1 alpha-2 code.
     * @param string $locale An ISO 639-1 language code.
     *
     * @return array<string, string> The list of all available subdivisions for the specified country.
     */
    function get_subdivisions(?string $code = null, string $locale = 'en'): array
    {
        $subdivisionRepository = new SubdivisionRepository();
        $subdivisions = [];

        if (isset($code)) {
            $subdivisions = $subdivisionRepository->getList([$code], $locale);
        }

        return $subdivisions;
    }
}
