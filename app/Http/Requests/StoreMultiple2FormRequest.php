<?php

namespace sistema\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMultiple2FormRequest extends FormRequest
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
    public function rules()
    {
         $formRequests = [
         EtniaFormRequest::class,
         LugarEtniaFormRequest::class,
        ];

    $rules = [];

    foreach ($formRequests as $source) {
      $rules = array_merge(
        $rules,
        (new $source)->rules()
      );
    }

    return $rules;
    }
}
