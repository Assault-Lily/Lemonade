<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HexColorCode implements Rule
{
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
        if(is_null($value)) return true;
        return (bool)(preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeがHEXカラーコードの要件を満たしていません';
    }
}
