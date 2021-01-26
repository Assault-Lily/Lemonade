<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use PhpParser\Node\Scalar\String_;

class Hiragana implements Rule
{
    /**
     * @var String
     */
    private $approve;

    /**
     * Create a new rule instance.
     *
     * @param String $approve_chars
     */
    public function __construct(String $approve_chars = '')
    {
        $this->approve = $approve_chars;
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
        return (bool)(preg_match("/^[ぁ-ゞァ-ヾー{$this->approve}]+$/u", $value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeは全角のひらがなとカタカナ(と一部許容される文字)である必要があります';
    }
}
