<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormularioStoreRequest extends FormRequest
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
        return [
    //        'name' => 'required|string|max:255|unique:'.Permission::class,
            'numero_necesidad' => 'nullable',
            'identificacion_user' => 'required',
            'proceso_que_solicita_presupuesto' => 'required',
            'valor_total_de_la_solicitud_actual' => 'required',
            'valor_total_asignado_en_vigencia_anterior' => 'nullable',

            'necesidad.*' => 'required',
            'justificacion.*' => 'required',
            'actividad.*' => 'required',
            'categoria.*' => 'required',
            'unidad_de_medida.*' => 'required',
            'cantidad.*' => 'required',
            'valor_unitario.*' => 'required',
            'valor_total_solicitatdo_por_necesidad.*' => 'required',
            'periodo_de_inicio_de_ejecucion.*' => 'required',
            'vigencias_anteriores.*' => 'required',
            'valor_asignado_en_la_vigencia_anterior.*' => 'required',

            'procesos_involucrados.*' => 'nullable',
            'plan_de_mejoramiento_al_que_apunta_la_necesidad.*' => 'required',
            'linea_del_plan_desarrollo_al_que_apunta_la_necesidad.*' => 'required',

            'frecuencia_de_uso.*' => 'required',
            'mantenimientos_requeridos.*' => 'required',
            'capacidad_instalada.*' => 'required',
            'riesgo_de_la_inversion.*' => 'required',
            'anexos.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx'
        ];
    }

    protected function prepareForValidation()
    {
//        $justificaciones = $this->input('justificacion', []);
        $justificaciones = $this->justificacion;
        foreach ($justificaciones as $index => $justificacion) {
            if ($justificacion === 'Elemento_Borrado') {
                // Si es "Elemento_Borrado", eliminamos las reglas de validación para ese índice
                $this->merge([
//                    'justificacion' => [],
                    'justificacion.' . $index => 'nullable',
                    'actividad.' . $index => 'nullable',
                    'categoria.' . $index => 'nullable',
                    'unidad.' . $index => 'nullable',
                    'cantidad.' . $index => 'nullable',
                    'valor_unitario.' . $index => 'nullable',
                    'periodo.' . $index => 'nullable',
                    'vigencias.' . $index => 'nullable',
                    'valor_asignado.' . $index => 'nullable',
                    'frecuencia.' . $index => 'nullable',
                    'mantenimientos.' . $index => 'nullable',
                    'capacidad.' . $index => 'nullable',
                    'riesgo.' . $index => 'nullable',
                ]);
            }
        }
    }
}
