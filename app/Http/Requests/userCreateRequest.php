<?php

namespace Cmisoft\Http\Requests;

use Cmisoft\Http\Requests\Request;

class userCreateRequest extends Request
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
            'nombre' => 'required|min:3',
            'cedula' => 'required|min:6|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5',
        ];
    }
}
