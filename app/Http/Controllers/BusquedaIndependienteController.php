<?php

namespace App\Http\Controllers;

use App\helpers\ZilefErrors;
use App\Models\Comprobante;
use App\Models\Parametro;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusquedaIndependienteController extends Controller
{
    public function Encontrar_AJ_CI($Transacciones): string
    {
        try {
            DB::beginTransaction();
            $inicioicrotime = microtime(true);
            $codigo = "AJ";

            $CPController = new ContrapartidasCIController();
            DB::beginTransaction();
            foreach ($Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', 'aj');

                if ($CPController->NoHayComprobantes($comprobantes, $transa)) {
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida' => 'No se encontró comprobantes para el documento',
                        'concepto_flujo_homologacion' => 'No se encontró comprobantes para el documento',
                    ]);
                    continue;
                }

                $lasContrapartidas = clone $comprobantes;
                $lasContrapartidasForeach = clone $comprobantes;

                //core AJ
                $principal = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->first();
                //todo: si hay mas, es error, del excel o que se permitio subir 2 veces

                if($principal == null){
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida' => 'No se encontró un comprobante con codigo_cuenta = '. $transa->codigo_cuenta_contable,
                        'concepto_flujo_homologacion' => 'No se encontró',
                    ]);
                    continue;
                }
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
                        'concepto_flujo_homologacion' => $concepto,
                    ]);
                } else {
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida' => 'No se encontro CP para doc_ref',
                        'concepto_flujo_homologacion' => 'No se encontro CP para doc_ref',
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

            return 'Operacion exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas';
        } catch (\Throwable $th) {
            $error = ZilefErrors::RastroError($th);
            Log::error($error);
            return 'Operacion fallida... ' . $error;
        }
    }


    public function Encontrar_AN_CI($Transacciones):string
    {

        $inicioicrotime = microtime(true);
        $codigo = "AN";
        try {
            DB::beginTransaction();
            //</editor-fold>
            foreach ($Transacciones as $index => $transa) {
                // explain: AN
                $laContra = transaccion::Where('documento', $transa->documento)
                    ->WhereNot('id', $transa->id);

                $NumContras = $laContra->count();
                $laContra = $laContra->first();

                if ($laContra === null) {
                    $laContra2 = comprobante::Where('numero_documento', $transa->documento)
                        ->Where('codigo', $codigo)
                        ->WhereNot('codigo_cuenta', $transa->codigo_cuenta_contable);
                    $NumContras = $laContra2->count();
                    $laContra2 = $laContra2->first();

                    if ($laContra2 === null) {
                        $mensaje_t_Error = "No se encontro cp (auxiliar y comprobantes). Documento " . $transa->documento;
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida' => $mensaje_t_Error,
                            'concepto_flujo_homologacion' => $mensaje_t_Error,
                        ]);
                        continue;
                    }
                }

                if ($laContra) {
                    //explain: ANULACIONES
                    if ($this->LaContraPartidaNoSumaCeroFirst($laContra, $transa)) continue;
                    $transa->update([
                        'n_contrapartidas' => $NumContras,
                        'contrapartida' => $laContra->codigo_cuenta_contable,
                        'concepto_flujo_homologacion' => $laContra->concepto_flujo_homologacion,
                    ]);
                    //explain: ANULACIONES
                } else { //se busca por comprobante y no en el aux
                    if ($this->LaContraPartidaNoSumaCeroFirst($laContra2, $transa)) continue;

                    $int_ContrapartidaRef = intval($laContra2->codigo_cuenta);
                    $contrapartida = $this->hallarConcepto($int_ContrapartidaRef, $codigo);
                    $transa->update([
                        'n_contrapartidas' => $NumContras,
                        'contrapartida' => $laContra2->codigo_cuenta,
                        'concepto_flujo_homologacion' => $contrapartida,
                    ]);
                }
            }

            $finicrotime = microtime(true);
            $tiempoTransacurrido = number_format($finicrotime - $inicioicrotime, 2);
            Parametro::create([
                'Fecha_creacion_parametro' => date('Y-m-d H:i:s'),
                'nombre' => "Cruzar $codigo CI",
                'valor' => $tiempoTransacurrido,
                'categoria' => "Crusando $codigo",
            ]);
            DB::commit();
            return 'Operacion exitosa.';

        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ZilefErrors::RastroError($th);
            Log::error($error);
            return 'Operacion fallida... ' . $error;
        }
    }


    /*helps*/
    public function LaContraPartidaNoSumaCeroFirst($lasContrapartidas, $transa): bool
    {

        $transaValor = intval($transa->valor_debito) === 0 ? intval($transa->valor_credito) : intval($transa->valor_debito);
        $contraValor = intval($lasContrapartidas->valor_debito) === 0 ? intval($lasContrapartidas->valor_credito) : intval($lasContrapartidas->valor_debito);
        $elCero = abs($transaValor) !== abs($contraValor);
        if ($elCero) {
            $transa->update([
                'contrapartida' => "No se encontro un Debito y credito igual. Principal = $transaValor",
                'concepto_flujo_homologacion' => "Contrapartida = $contraValor",
            ]);
        }
        return $elCero;
    }

}
