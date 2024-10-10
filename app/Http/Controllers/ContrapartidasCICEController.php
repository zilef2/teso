<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContrapartidasCICEController extends Controller
{
    //todo: falta traer y configurar "Buscar_CP_CI" que esta en transaccionController


    public function Buscar_AJ_AN_CI(Request $request)
    {
        try {
            $codigo = "AJ";
            if (Comprobante::Where('codigo', $codigo)->count() === 0)
                return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

            DB::beginTransaction();

            //buscamos las transacciones de AJ de este mes este año
            [$Transacciones, $valor_debito_credito] = Myhelp::TransaccionesCI_AJ_AN($codigo, 'AJ');//debito para AJ

            foreach ($Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', $codigo);

                $HayComprobantes = clone $comprobantes;
                $HayComprobantes = $HayComprobantes->count();
                if ($HayComprobantes === 0) {
                    $transa->update([
                        'contrapartida_CI' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                        'concepto_flujo_homologación' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                    ]);
                    continue;
                }

                $lasContrapartidas = clone $comprobantes;

                $principales = $comprobantes->where($valor_debito_credito, '>', 0)->get();
                $lasContrapartidas = $lasContrapartidas->where("valor_credito", '>', 0);
                $lasContrapartidas2 = clone $lasContrapartidas;
                $lasContrapartidas = $lasContrapartidas->get();
                //validacion de credito - debito
                $sumprincipales = $principales->sum('valor_debito'); //todo: hacer mas dinamica
                $sumlasContrapartidas = $lasContrapartidas->sum('valor_credito'); //todo: hacer mas dinamica

                if ($sumprincipales !== $sumlasContrapartidas) {
                    dd('Error fatal, La suma de la contrapartida no concuerda',
                        $principales,
                        $lasContrapartidas,
                        $sumprincipales, $sumlasContrapartidas);
                    $transa->update([
                        'contrapartida_CI' => "debitos y creditos distintos ($sumprincipales | $sumlasContrapartidas)",
                        'concepto_flujo_homologación' => "debitos y creditos distintos ($sumprincipales | $sumlasContrapartidas)",
                    ]);
                    continue;
                }

                //va y busca los demas
                foreach ($principales as $principal) {
                    //validacion adicional: el doc_ref debe ser igual para el original y la CP
                    $principalRef = $principal->documento_ref;
                    $Col_ContrapartidaRef = $lasContrapartidas2->Where('documento_ref', $principalRef)
                        ->Where('id', '!=', $principal->id)
                        ->get();

                    if ($Col_ContrapartidaRef->count() <= 0 || !isset($ContrapartidaRef[0])) {
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida_CI' => 'No se encontro CP en AJ',
                            'concepto_flujo_homologación' => 'No se encontro CP en AJ',
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
            $mensaj = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', 'Operacion con errores ' . $mensaj);
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
}
