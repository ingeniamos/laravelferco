<?php

namespace Cmisoft\Http\Requests;

use Cmisoft\Http\Requests\Request;

class LiderRequest extends Request
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
            'apellido'=>'required|min:3',
            'cedula'=>'required|min:5|max:10', 
            'telefono'=>'required|min:10', 
            'puesto_id'=>'required', 
            'coordinador_id'=>'required', 
            'email'=>'required|email',
        ];
    }
}
