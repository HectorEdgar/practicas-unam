<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObraLugarFormRequest extends FormRequest
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
            'longitud' => 'required|max:200',
            'latitud' => 'required|max:200'
        ];
    }
}
