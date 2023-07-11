<?php

namespace Valinteca\Msegat\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSaudiNumber implements Rule
{
    private $attribute;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        if (is_string($value)) {
            $value = [$value];
        }

        foreach ($value as $number) {
            if (! preg_match('/05[0-9]{8}/', $number)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "$this->attribute must have format: 05xxxxxxxx";
    }
}
