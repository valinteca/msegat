<?php

namespace Valinteca\Msegat\Services;

use Valinteca\Msegat\Exceptions\InvalidNumberFormatException;

class SaudiNumberFormatter
{
    private $country_codes = [
        '966',
        '+966',
        '00966',
    ];

    public function __construct(private string $number)
    {
    }

    public function isValid(): bool
    {
        return (bool) preg_match('/^(00966|\+966|966|0)?(5)[0-9]{8}$/', $this->number);
    }

    public function getWithoutCountryCode()
    {
        return preg_replace('/^(00966|\+966|966)/', '0', $this->number);  
    }

    public function getWithCountryCode($country_code = '966')
    {
        if (! in_array($country_code, $this->country_codes)) {
            throw new InvalidNumberFormatException("Country code $country_code is not valid");
        }

        return preg_replace('/^(0)/', $country_code, $this->number);  
    }
}
