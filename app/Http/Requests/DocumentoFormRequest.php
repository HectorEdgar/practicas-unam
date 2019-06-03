<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoFormRequest extends FormRequest
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

        'titulo' => 'required|max:600',
        'lugar_public_pais' => 'nullable|max:50',
        'lugar_public_edo'=> 'nullable|max:50',
        'derecho_autor' => 'required|integer|between:0,1 ',
        'fecha_publi'  => 'nullable|max:4',
        'url'   => 'nullable|max:600',
        'fecha_consulta' => 'required|date',
        'poblacion'  => 'required|integer',
        'tipo' => 'nullable|max:5',
        'notas'  => 'nullable|max:700'
        ];
    }
}
