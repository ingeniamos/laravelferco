<?php

namespace Cmisoft\Http\Requests;

use Cmisoft\Http\Requests\Request;

class IndicadorRequest extends Request
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
            'nombre'=>'required|min:3',
            'valor'=>'required|integer|min:0',
            'escala'=>'required|integer',
            'subgrupo_id'=>'required',
        ];
    }
}
