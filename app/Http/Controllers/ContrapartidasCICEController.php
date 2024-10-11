<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContrapartidasCICEController extends Controller
{
    //todo: falta traer y configurar "Buscar_CP_CI" que esta en transaccionController


    public function Buscar_AJ_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $codigo = "AJ";
            if (Comprobante::Where('codigo', $codigo)->count() === 0)
                return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

            DB::beginTransaction();
            $Transacciones = Myhelp::TransaccionesCI_AJ_AN($codigo);

            foreach ($Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', $codigo);

                if ($this->NoHayComprobantes($comprobantes, $transa)) continue;

                $lasContrapartidas = clone $comprobantes;

                $principales = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->get();
                $lasContrapartidas = $lasContrapartidas->WhereNotIn("id", $principales->id)
                    ->Where('documento_ref', $principales->documento_ref)->get();
                // $lasContrapartidas  =  should be one

                if ($this->LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales)) continue;

                //va y busca los demas
                foreach ($principales as $principal) {
                    //validacion adicional: el doc_ref debe ser igual para el original y la CP
                    $principalRef = $principal->documento_ref;
                    $Col_ContrapartidaRef = $lasContrapartidas->Where('documento_ref', $principalRef)
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
                'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' del mes y año actual fueron revisadas'
            );
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

                if ($this->NoHayComprobantes($comprobantes, $transa)) continue; // todo: actualizar la transaccion

                $lasContrapartidas = clone $comprobantes;

                $principales = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->get();

                if ($this->ComprobantesSinCodigoCuentaContable($principales)) continue; // todo: actualizar la transaccion

                $lasContrapartidas = $lasContrapartidas->WhereNotIn("id", $principales->pluck('id'))
                    ->Where('documento_ref', $principales[0]->documento_ref);
                // $lasContrapartidas  =  should be one

                if ($this->LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales)) continue;

                //va y busca los demas
                foreach ($principales as $principal) {
                    //validacion adicional: el doc_ref debe ser igual para el original y la CP
                    $principalRef = $principal->documento_ref;
                    $Col_ContrapartidaRef = $lasContrapartidas->Where('documento_ref', $principalRef)
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
                'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' del mes y año actual fueron revisadas'
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
        $lasContrapartidas = $lasContrapartidas->get();
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
}
