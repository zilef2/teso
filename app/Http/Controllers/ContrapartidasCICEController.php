<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Jobs\BusquedaConceptoCI;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContrapartidasCICEController extends Controller
{
    //todo: falta traer y configurar "Buscar_CP_CI" que esta en transaccionController

    private function BusquedaPocasTransacciones($Transacciones){

    }

    public function Buscar_AJ_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $codigo = "AJ";

            if (Comprobante::Where('codigo', $codigo)->count() === 0)
                return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

            $Transacciones = Myhelp::TransaccionesCI_AJ_AN($codigo);
            $conteoTransac = $Transacciones->count();
            if($conteoTransac > 100){
//                dispatch(new BusquedaConceptoCI($Transacciones));
                dispatch(new BusquedaConceptoCI($Transacciones))->delay(now()->addSeconds(4));
                return redirect()->route('transaccion.index')->with('success',
                    $conteoTransac . ' transacciones ' . $codigo . ' de agosto serán revisadas'
                );
            }else{

                DB::beginTransaction();
                foreach ($Transacciones as $index => $transa) {
                    $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                        ->Where('codigo', $codigo);

                    if ($this->NoHayComprobantes($comprobantes, $transa)){
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida_CI' => 'No se encontró comprobantes para el documento',
                            'concepto_flujo_homologación' => 'No se encontró comprobantes para el documento',
                        ]);
                        continue;
                    }

                    $lasContrapartidas = clone $comprobantes;
                    $lasContrapartidasForeach = clone $comprobantes;

                    //core
                    $principales = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->get();

                    $lasContrapartidas = $lasContrapartidas->WhereNotIn('id', $principales->pluck('id'))
                        ->WhereIn('documento_ref', $principales->pluck('documento_ref'))
                        ->get();
                    // $lasContrapartidas  =  should be one

                    //AJUSTES
                    if ($this->LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales)){
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida_CI' => 'No se encontró una suma coherente, no suman 0',
                            'concepto_flujo_homologación' => 'No se encontró una suma coherente, no suman 0',
                        ]);
                        continue;
                    }

                    //va y busca los demas
                    foreach ($principales as $principal) {
                        //validacion adicional: el doc_ref debe ser igual para el original y la CP
                        $principalRef = $principal->documento_ref;
                        $Col_ContrapartidaRef = $lasContrapartidasForeach->Where('documento_ref', $principalRef)
                            ->Where('id', '!=', $principal->id)
                            ->get();

                        if ($Col_ContrapartidaRef->count() <= 0 || !isset($ContrapartidaRef[0])) {
                            $transa->update([
                                'n_contrapartidas' => 0,
                                'contrapartida_CI' => 'No se encontro CP en ' . $codigo,
                                'concepto_flujo_homologación' => 'No se encontro CP en ' . $codigo,
                            ]);
                        } else {
                            $int_ContrapartidaRef = $ContrapartidaRef[0]->codigo_cuenta;
                            $concepto = $this->hallarConcepto($int_ContrapartidaRef, $codigo);
                            $transa->update([
                                'n_contrapartidas' => count($lasContrapartidas),
                                'contrapartida_CI' => $int_ContrapartidaRef,
                                'concepto_flujo_homologación' => $concepto,
                            ]);
                        }
                    }
                }
                DB::commit();

                return redirect()->route('transaccion.index')->with('success',
                    'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
                );
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Ajustes con errores: ' . ZilefErrors::RastroError($th));
        }
    }

    public function Buscar_AN_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $codigo = "AN";
            if (Comprobante::Where('codigo', $codigo)->count() === 0)
                return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

            DB::beginTransaction();
            //buscamos las transacciones de $codigo de este mes este año
            $valor_debito_credito = "valor_debito";
            $Transacciones = Myhelp::TransaccionesCI_AJ_AN($codigo);
            foreach ($Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', $codigo);


                if ($this->NoHayComprobantes($comprobantes, $transa)){
                     $mensaje_t_Error = "No se encontro ningun comprobante para el documento " . $transa->documento_ref;
                $transa->update([
                    'n_contrapartidas' => 0,
                    'contrapartida_CI' => $mensaje_t_Error,
                    'concepto_flujo_homologación' => $mensaje_t_Error,
                ]);
                    continue;
                }

                $lasContrapartidas = clone $comprobantes;
                $lasContrapartidasForeach = clone $comprobantes;

                $principales = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->get();

                if ($this->ComprobantesSinCodigoCuentaContable($principales)){
                    $mensaje_t_Error = "No se encontro ningun comprobante para el codigo_cuenta " . $transa->codigo_cuenta_contable;
                $transa->update([
                    'n_contrapartidas' => 0,
                    'contrapartida_CI' => $mensaje_t_Error,
                    'concepto_flujo_homologación' => $mensaje_t_Error,
                ]);
                    continue;
                }

                $lasContrapartidas = $lasContrapartidas->WhereNotIn("id", $principales->pluck('id'))
                    ->WhereIn('documento_ref', $principales->pluck('documento_ref'))
                    ->get();
                // $lasContrapartidas  =  should be one

                //ANULACIONES
                if ($this->LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales)){
                     $mensaje_t_Error = "La contrapartida y la transaccion no suman cero.";
                $transa->update([
                    'n_contrapartidas' => 0,
                    'contrapartida_CI' => $mensaje_t_Error,
                    'concepto_flujo_homologación' => $mensaje_t_Error,
                ]);
                    continue;
                }

                //va y busca los demas
                foreach ($principales as $principal) {
                    //validacion adicional: el doc_ref debe ser igual para el original y la CP
                    $principalRef = $principal->documento_ref;
                    $Col_ContrapartidaRef = $lasContrapartidasForeach->Where('documento_ref', $principalRef)
                        ->Where('id', '!=', $principal->id)
                        ->get();

                    if ($Col_ContrapartidaRef->count() <= 0 || !isset($ContrapartidaRef[0])) {
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida_CI' => 'No se encontro CP en ' . $codigo,
                            'concepto_flujo_homologación' => 'No se encontro CP en ' . $codigo,
                        ]);
                    } else {
                        $int_ContrapartidaRef = $ContrapartidaRef[0]->codigo_cuenta;
                        $concepto = $this->hallarConcepto($int_ContrapartidaRef, $codigo);
                        $transa->update([
                            'n_contrapartidas' => count($lasContrapartidas),
                            'contrapartida_CI' => $int_ContrapartidaRef,
                            'concepto_flujo_homologación' => $concepto,
                        ]);
                    }
                }
            }
            DB::commit();

            return redirect()->route('transaccion.index')->with('success',
                'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
            );
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error ' . ZilefErrors::RastroError($th));
        }
    }

    private function hallarConcepto($cuentaCP, $codigo)
    {
        $cf = concepto_flujo::Where('cuenta_contable', $cuentaCP)->first();
        if ($cf) {
            return $cf->concepto_flujo;
        }
        return "Sin Concepto encontrado en $codigo";
    }

    private function LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales): bool
    {
        $sumprincipales = $principales->sum('valor_debito');
        $sumlasContrapartidas = $lasContrapartidas->sum('valor_credito');
        $elCero = abs($sumprincipales) !== abs($sumlasContrapartidas);
        if ($elCero) {
            $transa->update([
                'contrapartida_CI' => "debitos y creditos distintos ($sumprincipales | $sumlasContrapartidas)",
                'concepto_flujo_homologación' => "debitos y creditos distintos ($sumprincipales | $sumlasContrapartidas)",
            ]);
        }
        return $elCero;
    }

    private function ComprobantesSinCodigoCuentaContable($principales): bool
    {
        //todo: verificar que solo exista uno, pueden haber mas comprobantes con ese codigo_cuenta
        return (!isset($principales[0]));
    }

    private function NoHayComprobantes($comprobantes, $transa): bool
    {
        $HayComprobantes = $comprobantes->count();
        if ($HayComprobantes === 0) {
            $transa->update([
                'contrapartida_CI' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                'concepto_flujo_homologación' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
            ]);
        }
        return $HayComprobantes === 0;
    }

    public function BorrarConceptos()
    {
        $laFecha = new \DateTime();
        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $mes = 8; // agosto
//      $anio = $laFecha->format('Y'); // Obtiene el año

        dd(

        transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->get()
        );
        $tranListas = transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->update([
            'n_contrapartidas' => null,
            'contrapartida_CI' => null,
            'concepto_flujo_homologación' => null,
        ]);
        $conteo = transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->count();
        echo "Operacion: $tranListas. $conteo limpiadas";
    }
}
