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
     * @param ?string $code An ISO 3166-1 alpha-2 code.
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

if (! function_exists('group_by_jurisdiction')) {
    /**
     * Splits a list of items containing jurisdiction information to be grouped by their jurisdiction. It will
     * create an array with nested arrays for country and then subdivision.
     *
     * @param array<mixed> $items the set of items to split by jurisdiction
     *
     * @return array<string, array> The array of the items split into nested arrays based on their jurisdiction.
     */
    function group_by_jurisdiction(array $items = [], string $locale = 'en'): array
    {
        $grouped = [];

        foreach ($items as $item) {
            if (isset($item->jurisdiction)) {
                $codes = explode('-', $item->jurisdiction);

                $countryName = get_jurisdiction_name($codes[0], locale: $locale);

                if ($countryName) {
                    $subdivision = isset($codes[1]) ? get_region_name($codes[1], [$codes[0]], $locale) ?? '' : '';
                    $grouped[$countryName][$subdivision][] = $item;
                }
            }
        }

        return $grouped;
    }
}

if (! function_exists('parse_country_code')) {
    /**
     * @param string $code An ISO 3166-1 alpha-2 or ISO-3166-2 code.
     *
     * @return string The country portion of the ISO 3166-1 alpha-2 or ISO-3166-2 code.
     */
    function parse_country_code(string $code): string
    {
        $codes = explode('-', $code);

        return $codes[0];
    }
}

if (! function_exists('parse_subdivision_code')) {
    /**
     * @param string $code An ISO-3166-2 code.
     *
     * @return ?string The subdivision portion of the ISO-3166-2 code.
     */
    function parse_subdivision_code(string $code): ?string
    {
        $codes = explode('-', $code);

        if (count($codes) <= 1) {
            return null;
        }

        return $codes[1];
    }
}

if (! function_exists('clamp')) {
    /**
     * Restricts a number to be within a given range.
     * API based off of https://wiki.php.net/rfc/clamp
     *
     * @param int|float $num The number to restrict to the range
     * @param int|float $min The lower bound
     * @param int|float $max The upper bound
     *
     * @return int|float the restricted value
     */
    function clamp(int|float $num, int|float $min, int|float $max): int|float
    {
        return max($min, min($num, $max));
    }
}

if (! function_exists('to_associative_array')) {
    /**
     * Expand an array into an associative and optionally run mb_convert_case on the values. The array must only contain
     * strings or int values.
     *
     * @param array<string|int> $array The array to make associative
     * @param ?int $mode An optional mb_convert_case conversion mode
     * @param ?string $encoding An optional string encoding
     *
     * @return array<string|int, string|int> the restricted value
     */
    function to_associative_array(array $array, ?int $mode = null, ?string $encoding = null): array
    {
        $values = isset($mode) ? array_map(fn ($value) => mb_convert_case((string) $value, $mode, $encoding), $array) : $array;

        return array_combine($array, $values);
    }
}
