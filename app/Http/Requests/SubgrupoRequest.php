<?php

namespace Cmisoft\Http\Requests;

use Cmisoft\Http\Requests\Request;

class SubgrupoRequest extends Request
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
            'nombre'=>'required|min:3|unique:subgrupos'
        ];
    }

    public function messages()
    {
        return [
            'nombre.unique' => 'El subgrupo ya existe',
        ];
    }
}