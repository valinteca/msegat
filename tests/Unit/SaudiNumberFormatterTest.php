<?php

namespace Valinteca\Msegat\Tests\Unit;

use Orchestra\Testbench\TestCase;
use Valinteca\Msegat\Exceptions\InvalidNumberFormatException;
use Valinteca\Msegat\Services\SaudiNumberFormatter;

class SaudiNumberFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function country_code_05_is_valid()
    {
        $this->assertTrue((new SaudiNumberFormatter('0512345678'))->isValid());
    }
    
    /**
     * @test
     */
    public function country_code_5_is_valid()
    {
        $this->assertTrue((new SaudiNumberFormatter('512345678'))->isValid());
    }

    /**
     * @test
     */
    public function country_code_966_is_valid()
    {
        $this->assertTrue((new SaudiNumberFormatter('966512345678'))->isValid());
    }

    /**
     * @test
     */
    public function country_code_plus_966_is_valid()
    {
        $this->assertTrue((new SaudiNumberFormatter('+966512345678'))->isValid());
    }

    /**
     * @test
     */
    public function country_code_plus_00966_is_valid()
    {
        $this->assertTrue((new SaudiNumberFormatter('00966512345678'))->isValid());
    }

    /**
     * @test
     */
    public function get_without_country_code_removes_country_code_and_adds_0_prefix()
    {
        $this->assertEquals((new SaudiNumberFormatter('00966512345678'))->getWithoutCountryCode(), '0512345678');
    }

    /**
     * @test
     */
    public function get_with_country_code_removes_0_prefix_and_adds_country_code()
    {
        $this->assertEquals((new SaudiNumberFormatter('0512345678'))->getWithCountryCode(), '966512345678');
    }

    /**
     * @test
     */
    public function get_with_country_allows_overriding_default_country_code_with_plus_966()
    {
        $this->assertEquals((new SaudiNumberFormatter('0512345678'))->getWithCountryCode('+966'), '+966512345678');
    }

    /**
     * @test
     */
    public function get_with_country_allows_overriding_default_country_code_with_00966()
    {
        $this->assertEquals((new SaudiNumberFormatter('0512345678'))->getWithCountryCode('00966'), '00966512345678');
    }

    /**
     * @test
     */
    public function get_with_country_overriding_default_country_code_with_invalid_code_throws_exception()
    {
        $this->expectException(InvalidNumberFormatException::class);

        (new SaudiNumberFormatter('0512345678'))->getWithCountryCode('123');
    }
}