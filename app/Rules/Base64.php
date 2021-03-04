<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64 implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->isValidBase64($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a base64 data.';
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    private function isValidBase64(string $data): bool
    {
        if ( base64_encode(base64_decode($data, true)) === $data){
            return true;
        }

        return false;
    }
}
