<?php

namespace App\Http\Controllers;

use App\helpers\CPhelp;
use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\BusquedaConceptoCI_AJJob;
use App\Models\afectacion;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use function Psy\debug;

class ContrapartidasCEController extends Controller
{

    public function Buscar_CP_CE(Request $request)
    {
        try {
            $codigo = "CE";
            $frase_reservada = "No se encontro";
            if(!CPhelp::Val_Exista_CE_auxiliar($codigo)){
                return back()->with('error', 'Faltan archivos. Auxiliar, CE,AS,AF');//comprobantes de egreso, asientos, sin afectacion
            }
            DB::beginTransaction();

            $MesTransaccional = Parametro::Where("nombre" ,"Mes transaccional")->first();
            $asientos = asiento::WhereMonth('fecha_elaboracion',$MesTransaccional->valor)->get();
            foreach ($asientos as $index => $asiento) {
                $numero_unico1 = intval($asiento->nit);
                $numero_unico2 = intval($asiento->documento_ref);
                $numerounico = $numero_unico1 * $numero_unico2; //nit documento_ref
                $asiento->update([
                    'numerounico' => $numerounico,
                ]);
            }
            //todo:toask: debito para CI? 30oct2024
            [$comprobantes, $valor_debito_credito, $opuesto_debito_credito] = $this->ComprobantesCE($codigo);

            foreach ($comprobantes as $index => $compro) {
                //BUSCAR LA AFECTACION
                $afectas = $this->AfectacionesCE($codigo, $compro->numero_documento);
                //CE

                if ($afectas->count() === 0) {
                    $compro->update([
                        'sin_afectacion' => 1,
                        //aquiii con el codigo_cuenta del CE
                    ]);
                    continue;
                }
                //CALCULAR NU
                $numero_unico1 = intval($compro->nit);
                $numero_unico2 = intval($compro->documento_ref);
                $numerounico = $numero_unico1 * $numero_unico2; //nit documento_ref

                //BUSCAR EL ASIENTO CON NU
                $asientoNU = asiento::Where('numerounico',$numerounico)->first();

                //! early return
                if ($asientoNU === null) {
                    $compro->update([
                        'sin_afectacion' => 0,
                        'numerounico' => $numerounico,
                        'resultado_asientos' => $frase_reservada.' un asiento con el NU: '.$numerounico,
                    ]);
                    continue;
                }

                $compro->update([
                    'sin_afectacion' => 0,
                    'numerounico' => $numerounico,
                    'cuenta_contrapartida' => $asientoNU->codigo_cuenta,
                    'resultado_asientos' => 'OK'
                ]);
            }

            $INT_TransaccionesOperadas = CPhelp::BuscarContrapartidaGeneral($codigo,$frase_reservada);
            DB::commit();
            return back()->with('success',
                'Éxito. CE: ' . $comprobantes->count() . ' Transacciones: ' . $INT_TransaccionesOperadas . ' revisadas'
            );
        } catch (\Throwable $th) {
            DB::rollback();
            $mensaj = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', 'Operacion CE con errores ' . $mensaj);
        }
    }

    public static function ComprobantesCE($codigo): array
    {
        $valor_debito_credito = (strcmp($codigo, "CI") === 0) ? "valor_debito" : "valor_credito";
        $opuesto_debito_credito = (strcmp($valor_debito_credito, "valor_credito") === 0) ? "valor_debito" : "valor_credito";
        $paraMes = Parametro::Where("nombre" ,"Mes transaccional")->first();
        if($paraMes){//TODO: doubt
            $mes = intval($paraMes->valor);
        }

        $comprocciones = Comprobante::Where('codigo', $codigo)
//            ->WhereYear('fecha_elaboracion', $anio)
            ->whereMonth('fecha_elaboracion', $mes)
            ->get();
        return [$comprocciones, $valor_debito_credito, $opuesto_debito_credito];
    }
    public static function AfectacionesCE($codigo,$documentoBuscado): Collection
    {
        $valor_debito_credito = (strcmp($codigo, "CI") === 0) ? "valor_debito" : "valor_credito";
        $opuesto_debito_credito = (strcmp($valor_debito_credito, "valor_credito") === 0) ? "valor_debito" : "valor_credito";
        $paraMes = Parametro::Where("nombre" ,"Mes transaccional")->first();
        $mes = 8;
        if($paraMes){//TODO: doubt
            $mes = intval($paraMes->valor);
        }

        return afectacion::Where('codigo_cuenta', $documentoBuscado)
            ->whereMonth('fecha_elaboracion', $mes)
            ->get();
    }
    public function hallarConcepto($cuentaCP, $codigo)
    {
        $cf = concepto_flujo::Where('cuenta_contable', $cuentaCP)->first();
        if ($cf) {
            return $cf->concepto_flujo;
        }
        return "No se encontro concepto en $codigo";
    }

    public function LaContraPartidaNoSumaCeroGet($lasContrapartidas, $compro, $principal): bool
    {
        $crediODebi = intval($compro->valor_debito) == 0 ? 'valor_credito' : 'valor_debito';
        $debiOCredi = intval($compro->valor_debito) == 0 ? 'valor_debito' : 'valor_credito';
        $principalValor = $compro->{$crediODebi};
        $sumlasContrapartidas = $lasContrapartidas->sum($debiOCredi);
        $elCero = abs(intval($principalValor)) !== abs(intval($sumlasContrapartidas));
        if ($elCero) {
            $compro->update([
                'contrapartida' => "No se encontro un Debito y credito iguales.DEBITO: $principalValor",
                'concepto_flujo_homologación' => "CONTRAPARTIDA CREDITO: $sumlasContrapartidas",
            ]);
        }
        return $elCero;
    }

    public function NoHayComprobantes($comprobantes, $compro): bool
    {
        $HayComprobantes = $comprobantes->count();
        if ($HayComprobantes === 0) {
            $compro->update([
                'contrapartida' => 'No se encontro ningun comprobante para el documento ' . $compro->documento,
                'concepto_flujo_homologación' => 'No se encontro ningun comprobante para el documento ' . $compro->documento,
            ]);
        }
        return $HayComprobantes === 0;
    }

}
