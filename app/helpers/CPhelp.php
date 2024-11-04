<?php
namespace App\helpers;

use App\Models\afectacion;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use App\Models\transaccion;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CPhelp {


    public static function BuscarContrapartidaGeneral($codigo,$frase_reservada){
        [$Transacciones, $valor_debito_credito, $opuesto_debito_credito] = self::TransaccionesCICE($codigo);

        $returnV = 0;
        foreach ($Transacciones as $index => $transa) {
            $returnV++;
            $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                ->Where('codigo', $codigo);

            if(CPhelp::ValidacionNumComprobantes($comprobantes,$transa,$frase_reservada)) continue;

            $lasContrapartidas = clone $comprobantes;
            $principales = $comprobantes->where($valor_debito_credito, '>', 0)->get();
            $lasContrapartidas = $lasContrapartidas->where($opuesto_debito_credito, '>', 0)->get();

            //validacion de credito - debito
            $sumprincipales = $principales->sum($valor_debito_credito);
            $sumlasContrapartidas = $lasContrapartidas->sum($opuesto_debito_credito);

            if ($sumprincipales !== $sumlasContrapartidas) {
                $transa->update([
                    'contrapartida' => "$frase_reservada. Los creditos no concuerdan, principales suman: $sumprincipales",
                    'concepto_flujo_homologación' => "contrapartidas suman: $sumlasContrapartidas",
                ]);
                continue;
            }

            //va y busca los demas
            foreach ($principales as $principal) {
                $FirstMaxContraPartida = $lasContrapartidas->sortByDesc($valor_debito_credito)->first();
                $cuentaCP = $FirstMaxContraPartida->codigo_cuenta;

                //buscamos el concepto
                $concepto = self::hallarConcepto($cuentaCP);
                $transa->update([
                    'n_contrapartidas' => count($lasContrapartidas),
                    'contrapartida' => $cuentaCP,
                    'concepto_flujo_homologación' => $concepto,
                ]);
            }
        }
        return $returnV;
    }
    private static function hallarConcepto($cuentaCP)
    {
        $cf = concepto_flujo::Where('cuenta_contable', $cuentaCP)->first();
        if ($cf) {
            return $cf->concepto_flujo;
        }
        return 'Buscar en AJ o AN';
    }

    public static function TransaccionesCICE($codigo): array
    {
        $valor_debito_credito = (strcmp($codigo, "CI") === 0) ? "valor_debito" : "valor_credito";
        $opuesto_debito_credito = (strcmp($valor_debito_credito, "valor_credito") === 0) ? "valor_debito" : "valor_credito";
        $paraMes = Parametro::Where("nombre" ,"Mes transaccional")->first();
        if($paraMes){
            $mes = intval($paraMes->valor);
        }

        $Transacciones = transaccion::Where('codigo', $codigo)
//            ->WhereNull('concepto_flujo_homologación')
//            ->WhereYear('fecha_elaboracion', $anio)
            ->whereMonth('fecha_elaboracion', $mes)
            ->get();
        return [$Transacciones, $valor_debito_credito, $opuesto_debito_credito];
    }
    public static function ValidacionNumComprobantes($comprobantes,&$transa,$frase_reservada)
    {
        $HayComprobantes = $comprobantes->count();
        $returnValue = $HayComprobantes === 0;
        if ($returnValue) {
            $transa->update([
                'contrapartida' => $frase_reservada.' ningun comprobante para el documento ' . $transa->documento,
            ]);
        }
        return $returnValue;
    }
    // end JUST THIS PROJECT
    public static function Val_Exista_CI_auxiliar($codigo): bool
    {
        $Auxiliar = transaccion::count();
        $CI = Comprobante::Where('codigo',$codigo)->count();
        return $Auxiliar > 0 && $CI > 0;
    }
    public static function Val_Exista_CE_auxiliar($codigo): bool
    {
        $Auxiliar = transaccion::count();
        $CE = Comprobante::Where('codigo',$codigo)->count();
        $asiento = asiento::count();
        $afectacion = afectacion::count();
        return $Auxiliar > 0 && $CE > 0 && $asiento > 0 && $afectacion > 0;
    }



}
?>
