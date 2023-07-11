<?php

namespace Valinteca\Msegat\Rules;

use Illuminate\Contracts\Validation\Rule;

class VarsArrayIncludesAllMessageKeys implements Rule
{
    private $attribute;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private $keys)
    {
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

        foreach ($value as $val) {
            if (count(array_diff($this->keys, array_keys($val)))) {
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
        return "$this->attribute must contains all variables in the message";
    }
}
