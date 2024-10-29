<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Jobs\BusquedaConceptoCIJob;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use function Psy\debug;

class ContrapartidasCICEController extends Controller
{
    //todo: falta traer y configurar "Buscar_CP_CI" que esta en transaccionController

    private $Transacciones;

    private function BusquedaPocasTransacciones($Transacciones)
    {

    }

    public function Buscar_AJ_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        $codigo = "AJ";
        if (Comprobante::Where('codigo', $codigo)->count() === 0)
            return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

        $Transacciones = Myhelp::TransaccionesCI_AJ_AN($codigo);
        $conteoTransac = $Transacciones->count();

        if ($conteoTransac > 2) {
            dispatch(new BusquedaConceptoCIJob($Transacciones, "Cruce de AJ finalizado"))->delay(now()->addSeconds());

            $aproxSeconds = ceil($conteoTransac / 25); //25 son las que se analizan por segundo
            $aproxSeconds = $aproxSeconds > 60 ? ($aproxSeconds / 60) . ' mins' : ($aproxSeconds) . ' segs';
            return redirect()->route('transaccion.index')->with('warning',
                $conteoTransac . ' transacciones ' . $codigo . ' de agosto serán revisadas. Este proceso tardará aprox: ' . $aproxSeconds
            );
        }

        Log::info('El job BusquedaConceptoCI ha comenzado.');
        try {
            $mensaje = $this->Uscar_AJ_CI($Transacciones);
            return redirect()->route('transaccion.index')->with('success', $mensaje);

        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Ajustes con errores: ' . ZilefErrors::RastroError($th));
        }
    }

    public function Uscar_AJ_CI($Transacciones): string
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
            'categoria' => 'jobs_Ajustes'
        ]);

        return 'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas';
    }

    public function Buscar_AN_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {

            $codigo = "AN";

            //<editor-fold desc="inicio">
            $Transacciones = transaccion::Where('codigo', $codigo)
                //                ->WhereNull('concepto_flujo_homologación')
            ;
            $NtrasArchivo = transaccion::Where('codigo', $codigo)->count();
            $NtrasSinConcepto = clone $Transacciones;
            $NtrasSinConcepto = $NtrasSinConcepto->count();
            if ($NtrasSinConcepto === 0)
                return back()->with('warning', 'Las AN ya han sido cruzadas');
            if ($NtrasArchivo === 0)
                return back()->with('error', 'Sin anulaciones disponibles en el auxiliar');

            DB::beginTransaction();
            //</editor-fold>

            foreach ($Transacciones->get() as $index => $transa) {
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
                            'concepto_flujo_homologación' => $mensaje_t_Error,
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
                        'concepto_flujo_homologación' => $laContra->concepto_flujo_homologación,
                    ]);
                    //explain: ANULACIONES
                } else { //se busca por comprobante y no en el aux
                    if ($this->LaContraPartidaNoSumaCeroFirst($laContra2, $transa)) continue;

                    $int_ContrapartidaRef = intval($laContra2->codigo_cuenta);
                    $contrapartida = $this->hallarConcepto($int_ContrapartidaRef, $codigo);
                    $transa->update([
                        'n_contrapartidas' => $NumContras,
                        'contrapartida' => $laContra2->codigo_cuenta,
                        'concepto_flujo_homologación' => $contrapartida,
                    ]);
                }

            }
            DB::commit();

            return redirect()->route('transaccion.index')->with('success',
                'Operación exitosa. ' . $NtrasSinConcepto . ' transacciones ' . $codigo . ' de agosto fueron revisadas | solo se busca 1 CP'
            );
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error ' . ZilefErrors::RastroError($th));
        }
    }

    public function hallarConcepto($cuentaCP, $codigo)
    {
        $cf = concepto_flujo::Where('cuenta_contable', $cuentaCP)->first();
        if ($cf) {
            return $cf->concepto_flujo;
        }
        return "No se encontro concepto en $codigo";
    }

    public function LaContraPartidaNoSumaCeroFirst($lasContrapartidas, $transa): bool
    {

        $transaValor = intval($transa->valor_debito) === 0 ? intval($transa->valor_credito) : intval($transa->valor_debito);
        $contraValor = intval($lasContrapartidas->valor_debito) === 0 ? intval($lasContrapartidas->valor_credito) : intval($lasContrapartidas->valor_debito);
        $elCero = abs($transaValor) !== abs($contraValor);
        if ($elCero) {
            $transa->update([
                'contrapartida' => "No se encontro un Debito y credito igual. Principal = $transaValor",
                'concepto_flujo_homologación' => "Contrapartida = $contraValor",
            ]);
        }
        return $elCero;
    }

    public function LaContraPartidaNoSumaCeroGet($lasContrapartidas, $transa, $principal): bool
    {
        $crediODebi = intval($transa->valor_debito) == 0 ? 'valor_credito' : 'valor_debito';
        $debiOCredi = intval($transa->valor_debito) == 0 ? 'valor_debito' : 'valor_credito';
        $principalValor = $transa->{$crediODebi};
        $sumlasContrapartidas = $lasContrapartidas->sum($debiOCredi);
        $elCero = abs(intval($principalValor)) !== abs(intval($sumlasContrapartidas));
        if ($elCero) {
            $transa->update([
                'contrapartida' => "No se encontro un Debito y credito iguales.DEBITO: $principalValor",
                'concepto_flujo_homologación' => "CONTRAPARTIDA CREDITO: $sumlasContrapartidas",
            ]);
        }
        return $elCero;
    }

    public function ComprobantesSinCodigoCuentaContable($principales): bool
    {
        //todo: verificar que solo exista uno, pueden haber mas comprobantes con ese codigo_cuenta
        return (!isset($principales[0]));
    }

    public function NoHayComprobantes($comprobantes, $transa): bool
    {
        $HayComprobantes = $comprobantes->count();
        if ($HayComprobantes === 0) {
            $transa->update([
                'contrapartida' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                'concepto_flujo_homologación' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
            ]);
        }
        return $HayComprobantes === 0;
    }

    public function BorrarConceptos(): void
    {
        $paraMes = Parametro::Where("nombre", "Mes transaccional")->first();
        if ($paraMes) {
            $mes = intval($paraMes->valor);
        }

        $conteo = transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->count();
        $tranListas = transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->update([
            'n_contrapartidas' => null,
            'contrapartida' => null,
            'concepto_flujo_homologación' => null,
        ]);
        echo "Operacion: $tranListas. $conteo limpiadas";
    }

    public function BorrarAjustes(): void
    {
        //$ajustes = transaccion::WhereNotNull('codigo')->delete();
        $ajustes = Comprobante::Where('codigo', 'AJ');
        $conteo = $ajustes->count();
        $tranListas = $ajustes->delete();
        echo "Result $tranListas. - $conteo Eliminadas";
    }
}
