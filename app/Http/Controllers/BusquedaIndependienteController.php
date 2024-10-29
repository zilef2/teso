<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusquedaIndependienteController extends Controller
{
    public function Encontrar_AJ_CI($Transacciones): string
    {
        $inicioicrotime = microtime(true);
        $codigo = "AJ";

        $CPController = new ContrapartidasCICEController();
        DB::beginTransaction();
        foreach ($Transacciones as $index => $transa) {
            $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                ->Where('codigo', 'aj');

            if ($CPController->NoHayComprobantes($comprobantes, $transa)) {
                $transa->update([
                    'n_contrapartidas' => 0,
                    'contrapartida' => 'No se encontró comprobantes para el documento',
                    'concepto_flujo_homologación' => 'No se encontró comprobantes para el documento',
                ]);
                continue;
            }

            $lasContrapartidas = clone $comprobantes;
            $lasContrapartidasForeach = clone $comprobantes;

            //core
            $principal = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->first();
            //todo: si hay mas, es error, del excel o que se permitio subir 2 veces

            $lasContrapartidas = $lasContrapartidas->WhereNot('id', $principal->id)
                ->Where('documento_ref', $principal->documento_ref)
                ->get();
            //AJUSTES
            if ($CPController->LaContraPartidaNoSumaCeroGet($lasContrapartidas, $transa, $principal)) {
                continue;
            }
            //va y busca los demas
            //validacion adicional: el doc_ref debe ser igual para el original y la CP
            $Col_ContrapartidaRef = $lasContrapartidasForeach
                ->Where('documento_ref', $principal->documento_ref)
                ->WhereNot('id', $principal->id)
                ->Where('valor_credito', $transa->valor_debito)
                ->get()
                ->sortByDesc('valor_credito')
                ->first();

            if ($Col_ContrapartidaRef) {
                $int_ContrapartidaRef = intval($Col_ContrapartidaRef->codigo_cuenta);
                $concepto = $CPController->hallarConcepto($int_ContrapartidaRef, $codigo);
                $transa->update([
                    'n_contrapartidas' => 1,
                    'contrapartida' => $int_ContrapartidaRef,
                    'concepto_flujo_homologación' => $concepto,
                ]);
            } else {
                $transa->update([
                    'n_contrapartidas' => 0,
                    'contrapartida' => 'No se encontro CP para doc_ref',
                    'concepto_flujo_homologación' => 'No se encontro CP para doc_ref',
                ]);
            }
        }
        DB::commit();
        Log::info('El job AJ ha terminado exitosamente.');
        $finicrotime = microtime(true);

        $tiempoTransacurrido = number_format($finicrotime - $inicioicrotime, 2);
        Parametro::create([
            'Fecha_creacion_parametro' => date('Y-m-d H:i:s'),
            'nombre' => 'Cruzar Ajustes CI',
            'valor' => $tiempoTransacurrido,
            'categoria' => 'Crusando Ajustes'
        ]);

        return 'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas';
    }

    //aquiii Encontrar_AN_CI

}
