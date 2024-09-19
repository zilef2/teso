<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class formularioUpdateRequest extends FormRequest {
    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {
        // $formularioId = $this->route('formulario') ?? null;
        return [
			// 'nombre' => 'required',
            // 'codigo' => 'nullable|unique:formularios,codigo,'.$formularioId,
            'codigo' => 'nullable',
            'fecha' => 'nullable',
            'hora_inicial' => 'nullable',
            'hora_final' => 'nullable',

            'actividad_id' => 'nullable',
            'material_id' => 'nullable',
            'ordentrabajo_id' => 'nullable',

            'pieza_id' => 'nullable',
            'cantidad' => 'nullable',

            'disponibilidad_id' => 'nullable',
            'reproceso_id' => 'nullable',
            'tipoformulario' => 'nullable',

            'nombreTablero' => 'nullable',
            'OTItem' => 'nullable',
            'TiempoEstimado' => 'nullable',
        ];
    }
}
