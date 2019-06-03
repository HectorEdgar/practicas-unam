<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoFormRequest extends FormRequest
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
            'fk_doc' => 'required|max:150',
            'secundario' => 'required|max:200',
            'director' => 'required|max:250',
            'productor'=> 'required|max:200',
            'realizador'=> 'required|max:200',
            'conductor'=> 'required|max:200',
            'reportero'=> 'required|max:200',
            'guionista'=> 'required|max:200',
            'fotografia'=> 'required|max:250',
            'musica'=> 'required|max:600',
            'actores'=> 'required|max:900',
            'narrador'=> 'required|max:200',
            'fecha_trans'=> 'required|max:200',
            'hora_trans'=> 'required|max:200',
            'idioma'=> 'required|max:200',
            'subtitulo'=> 'required|max:200',
            'formato'=> 'required|max:200',
            'duracion'=> 'required|max:200',
            'programa'=> 'required|max:200',
            'canal'=> 'required|max:200'
        ];
    }
}
