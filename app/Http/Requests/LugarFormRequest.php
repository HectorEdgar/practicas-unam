<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LugarFormRequest extends FormRequest
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
            'ubicacion' => 'max:200',
            'pais' => 'required|max:200',
            'region_geografica' => 'required|max:200'
        ];
    }
}
