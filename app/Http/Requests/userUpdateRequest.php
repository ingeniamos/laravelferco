<?php

namespace Cmisoft\Http\Requests;

use Cmisoft\Http\Requests\Request;

class userUpdateRequest extends Request
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
            'name' => 'required|min:7',
            'email' => 'required|email|unique:users',
        ];
    }
}
