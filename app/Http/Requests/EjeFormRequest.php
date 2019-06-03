<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EjeFormRequest extends FormRequest
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
            'nombre'=>'required|max:500',
            'area'=>'required|max:50',
            'poblacion'=>'required|max:50',
            'descripcion'=>'required'
        ];
    }
}
