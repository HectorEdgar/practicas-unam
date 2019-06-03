<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogFormRequest extends FormRequest
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
            'descripcion' => 'required|max:2000',
            'sentenciaSql' => 'max:2000',
            'fechaCreacion' => 'required'

        ];
    }
}
