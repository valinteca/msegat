<?php

namespace Valinteca\Msegat\Rules;

use Illuminate\Contracts\Validation\Rule;

class FilledArrayOrString implements Rule
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
        return (is_string($value) || is_array($value)) && ! empty($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "$this->attribute must be a filled string or array";
    }
}
