<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PonenciaFormRequest extends FormRequest
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
            'evento' => 'required|max:250',
            'lugar_presentacion' => 'required|max:100',
            'fecha_pesentacion' => 'required|max:20',
            'paginas' => 'required|max:50'

        ];
    }
}
