<?php

namespace Cmisoft\Http\Requests;

use Cmisoft\Http\Requests\Request;

class AgendaRequest extends Request
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
            'actividad'=>'required|min:20',
            'responsable'=>'required',
            'fecha_limite'=>'required',
        ];
    }
}
