<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * Ensure a Country name can be retrieved
     *
     * @return void
     */
    public function test_get_country_name()
    {
        $jurisdiction = get_jurisdiction_name('CA');

        $this->assertEquals('Canada', $jurisdiction);
    }

    /**
     * Ensure null is returned if the country code doesn't exist
     *
     * @return void
     */
    public function test_get_country_name_does_not_exist()
    {
        $jurisdiction = get_jurisdiction_name('invalid');

        $this->assertNull($jurisdiction);
    }

    /**
     * Ensure the country name is returned in the requested locale
     *
     * @return void
     */
    public function test_get_country_with_locale()
    {
        $jurisdiction = get_jurisdiction_name('US', locale: 'fr-CA');

        $this->assertEquals('États-Unis', $jurisdiction);
    }

    /**
     * Ignore municipality if no subdivision
     *
     * @return void
     */
    public function test_get_country_ignore_municipality()
    {
        $jurisdiction = get_jurisdiction_name('CA', 'Toronto');

        $this->assertEquals('Canada', $jurisdiction);
    }

    /**
     * Ensure subdivision can be retrieved
     *
     * @return void
     */
    public function test_get_subdivision_name()
    {
        $jurisdiction = get_jurisdiction_name('CA-ON');

        $this->assertEquals('Ontario, Canada', $jurisdiction);
    }

    /**
     * Ensure subdivision can be retrieved in the requested locale
     *
     * @return void
     */
    public function test_get_subdivision_name_with_locale()
    {
        $jurisdiction = get_jurisdiction_name('BR-AP', locale: 'pt-BR');

        $this->assertEquals('Amapá, Brasil', $jurisdiction);
    }

    /**
     * Ignore subdivision if it isn't located
     *
     * @return void
     */
    public function test_get_subdivision_missing()
    {
        $jurisdiction = get_jurisdiction_name('CA-XX');

        $this->assertEquals('Canada', $jurisdiction);
    }

    /**
     * Ensure subdivision can be retrieved with municipality
     *
     * @return void
     */
    public function test_get_subdivision_with_municipality()
    {
        $jurisdiction = get_jurisdiction_name('CA-ON', 'Toronto');

        $this->assertEquals('Toronto, Ontario, Canada', $jurisdiction);
    }

    /**
     * Ensure subdivision can be retrieved with municipality
     *
     * @return void
     */
    public function test_uppercase_first_char_of_municipality()
    {
        $jurisdiction = get_jurisdiction_name('CA-ON', 'toronto');

        $this->assertEquals('Toronto, Ontario, Canada', $jurisdiction);
    }

    /**
     * Ignore municipality of subdivision missing
     *
     * @return void
     */
    public function test_get_subdivision_missing_with_municipality()
    {
        $jurisdiction = get_jurisdiction_name('CA-XX', 'Toronto');

        $this->assertEquals('Canada', $jurisdiction);
    }

    /**
     * Ensure null is returned if the country code doesn't exist
     *
     * @return void
     */
    public function test_get_subdivision_country_name_does_not_exist()
    {
        $jurisdiction = get_jurisdiction_name('XX-ON');

        $this->assertNull($jurisdiction);
    }

    /**
     * Ensure subdivision can be retrieved with municipality
     *
     * @return void
     */
    public function test_jurisdiction_with_custom_separator()
    {
        $jurisdiction = get_jurisdiction_name('CA-ON', 'toronto', separator: '_');

        $this->assertEquals('Toronto_Ontario_Canada', $jurisdiction);
    }

    /**
     * Ignore additional codes
     *
     * @return void
     */
    public function test_jurisdiction_with_extra_code_values()
    {
        $jurisdiction = get_jurisdiction_name('CA-ON-BC');

        $this->assertEquals('Ontario, Canada', $jurisdiction);
    }
}


// get_jurisdiction_name($code, $municipality, $locale = 'en', $separator = ', ')
