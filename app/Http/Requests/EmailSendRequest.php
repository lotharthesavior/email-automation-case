<?php

namespace App\Http\Requests;

use App\Rules\Base64;
use Illuminate\Foundation\Http\FormRequest;

class EmailSendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '*.email'              => ['email', 'required'],
            '*.subject'            => ['string', 'required'],
            '*.body'               => ['string', 'required'],
            '*.attachment.*.name'  => ['string', 'required', 'sometimes'],
            '*.attachment.*.value' => ['string', 'required', 'sometimes', new Base64],
        ];
    }
}
