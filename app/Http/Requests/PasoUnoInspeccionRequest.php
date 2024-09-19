<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasoUnoInspeccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){return true;}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return
        [
			'fecha_realizacion' => 'required|date',
			'areainspeccion' => 'required',
			'userRecibir' => 'required',
			'responsable_sst' => 'nullable',
			'responsable_a' => 'nullable',
        ];
    }
}
