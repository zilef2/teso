<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class formularioRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $formularioId = $this->route('formulario') ?? null;
        return
        [
			// 'nombre' => 'required',
            // 'codigo' => 'required|unique:formularios,codigo,'.$formularioId,
            'fecha' => 'required',
            'hora_inicial' => 'required',
            'hora_final' => 'nullable',
            'ordentrabajo_id' => 'nullable',

            // 'material_id' => 'required',
            // 'pieza_id' => 'nullable',
            // 'cantidad' => 'nullable',

            'actividad_id' => 'nullable',
            'disponibilidad_id' => 'nullable',
            'reproceso_id' => 'nullable',
            'user_id' => 'nullable',
            'tipoformulario' => 'required',
        ];
    }
}
