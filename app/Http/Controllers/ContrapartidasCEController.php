<?php

namespace App\Http\Controllers;

use App\helpers\CPhelp;
use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\BusquedaConceptoCI_AJJob;
use App\Jobs\CruceCEJob;
use App\Models\afectacion;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use App\Models\transaccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use function Psy\debug;

class ContrapartidasCEController extends Controller
{

    public function Buscar_CP_CE(Request $request): RedirectResponse
    {
        try {
            $codigo = "CE";
            $frase_reservada = "No se encontro";
            $fraseExito = "Encontrado";
            if (!CPhelp::Val_Exista_CE_auxiliar($codigo)) {
                return back()->with('error', 'Faltan archivos. Auxiliar, CE,AS,AF');//comprobantes de egreso, asientos, sin afectacion
            }
//            if(Comprobante::WhereNull('nit')->count() === 0){
//                return back()->with('error', 'Existen comprobantes con NIT vacio');
//            }
            DB::beginTransaction();

            $MesTransaccional = Parametro::Where("nombre", "Mes transaccional")->first();
            $asientos = asiento::WhereMonth('fecha_elaboracion', $MesTransaccional->valor)->get();


            $this->CalcularNUAsientos($asientos, $fraseExito);
//            $this->validarCalculoNUasientos();

            $comprobantes = $this->Comprobantes_CE_del_mes($codigo);
            foreach ($comprobantes as $index => $compro) {
                //BUSCAR LA AFECTACION
                $afectas = $this->AfectacionesCE($codigo, $compro->codigo_cuenta);
                if ($afectas->count() === 0) {
                    $compro->update([
                        'resultado_asientos' => $fraseExito,
                        'sin_afectacion' => 1,
                        'cuenta_contrapartida' => $compro->cuenta_contrapartida,
                        'numerounico' => 0,
                    ]);
                    continue;
                }
                //CALCULAR NU
                $numero_unico1 = intval($compro->nit);
                $numero_unico2 = intval($compro->documento_ref);
                $numerounico = ($numero_unico1 * 11) + ($numero_unico2 * 13); //nit documento_ref

                //BUSCAR EL ASIENTO CON NU
                $asientoNU = asiento::Where('numerounico', $numerounico)->first();

                //! early return
                if ($asientoNU === null) {
                    $compro->update([
                        'resultado_asientos' => $frase_reservada . ' un asiento con el NU: ' . $numerounico,
                        'sin_afectacion' => -1,
                        'numerounico' => $numerounico,
                    ]);
                    continue;
                }

                $compro->update([
                    'resultado_asientos' => $frase_reservada . ' NU = nit ' . $numero_unico1 . ' * ref ' . $numero_unico2,
                    'sin_afectacion' => 0,
                    'numerounico' => $numerounico,
                    'cuenta_contrapartida' => $asientoNU->codigo_cuenta,
                ]);
            }

//            dispatch(new CruceCEJob($codigo,$frase_reservada))->delay(now());
//            $INT_TransaccionesOperadas = CPhelp::BuscarContrapartidaGeneral($codigo, $frase_reservada);
            DB::commit();
            return back()->with('success',
                'Éxito. Comprobantes cruzados: ' . $comprobantes->count() . '. Las Transacciones quedaron pendientes. Se enviará un correo avisando su finalización'
//                'Éxito. Comprobantes cruzados: ' . $comprobantes->count() . '. Transacciones: ' . $INT_TransaccionesOperadas
            );
        } catch (\Throwable $th) {
            DB::rollback();
            $mensaj = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', 'Operacion CE con errores ' . $mensaj);
        }
    }

    public static function Comprobantes_CE_del_mes($codigo): Collection
    {
        $valor_debito_credito = (strcmp($codigo, "CI") === 0) ? "valor_debito" : "valor_credito";
        $opuesto_debito_credito = (strcmp($valor_debito_credito, "valor_credito") === 0) ? "valor_debito" : "valor_credito";
        $paraMes = Parametro::Where("nombre", "Mes transaccional")->first();
        if ($paraMes) {//TODO: doubt
            $mes = intval($paraMes->valor);
        }

        $comprocciones = Comprobante::Where('codigo', $codigo)
//            ->WhereYear('fecha_elaboracion', $anio)
            ->whereMonth('fecha_elaboracion', $mes)
            ->get();
        return $comprocciones;
    }


    public static function AfectacionesCE($codigo, $codigoCuentaBuscada): Collection
    {
        $valor_debito_credito = (strcmp($codigo, "CI") === 0) ? "valor_debito" : "valor_credito";
        $opuesto_debito_credito = (strcmp($valor_debito_credito, "valor_credito") === 0) ? "valor_debito" : "valor_credito";
        $paraMes = Parametro::Where("nombre", "Mes transaccional")->first();
        $mes = 8;
        if ($paraMes) {//TODO: doubt
            $mes = intval($paraMes->valor);
        }

        return afectacion::Where('codigo_cuenta', $codigoCuentaBuscada)
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
                'concepto_flujo_homologacion' => "CONTRAPARTIDA CREDITO: $sumlasContrapartidas",
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
                'concepto_flujo_homologacion' => 'No se encontro ningun comprobante para el documento ' . $compro->documento,
            ]);
        }
        return $HayComprobantes === 0;
    }

    private function validarCalculoNUasientos($asientos, $fraseExito): void{

    }
    private function CalcularNUAsientos($asientos, $fraseExito): void
    {
        foreach ($asientos as $asiento) {
            $numero_unico1 = intval($asiento->nit);
            $numero_unico2 = intval($asiento->documento_ref);
            $numerounico = (($numero_unico1 * 11) + ($numero_unico2 * 13)); //nit documento_ref

            $asientoDuplicado = CPhelp::VerificarDuplicados($numerounico, $asiento); //throw exception
            if ($asientoDuplicado) {
                if ($asiento === null || $asientoDuplicado === null) dd(
                    $asiento,
                    $asientoDuplicado
                );
                $asiento->update([
                    'numerounico' => $numerounico,
                    'resultado_asiento' => 'codigo_cuenta. Asiento: ' .
                        $asiento->codigo_cuenta .
                        ' Duplicado: ' .
                        $asientoDuplicado->codigo_cuenta,
                ]);
            } else {
                $asiento->update([
                    'numerounico' => $numerounico,
                    'resultado_asiento' => $fraseExito,
                ]);
            }
        }
    }

}
