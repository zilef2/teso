<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\BusquedaConceptoCI_AJJob;
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

    public function Buscar_AJ_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        $codigo = "AJ";
        $bsuqeudaind = new BusquedaIndependienteController();

        if (Comprobante::Where('codigo', $codigo)->count() === 0)
            return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

        $Transacciones = Myhelp::TransaccionesCI_AJ_AN($codigo);
        $conteoTransac = $Transacciones->count();

        if ($conteoTransac > 3) {
            $user = Myhelp::AuthU();
            Log::info('El job BusquedaConceptoCI_AJJob ha comenzado.');
            dispatch(new BusquedaConceptoCI_AJJob($Transacciones, "Cruce de AJ finalizado", $user))->delay(now()->addSeconds());

            $aproxSeconds = ceil($conteoTransac / 25); //25 son las que se analizan por segundo
            $aproxSeconds = $aproxSeconds > 60 ? ($aproxSeconds / 60) . ' mins' : ($aproxSeconds) . ' segs';
            return redirect()->route('transaccion.index')->with('warning',
                $conteoTransac . ' transacciones ' . $codigo . ' de agosto serán revisadas. Este proceso tardará aprox: ' . $aproxSeconds
            );
        }

        try {
            $mensaje = $bsuqeudaind->Encontrar_AJ_CI($Transacciones);
            return redirect()->route('transaccion.index')->with('success', $mensaje);

        } catch (\Throwable $th) {
//            DB::rollback();
            return back()->with('error', 'Ajustes con errores: ' . ZilefErrors::RastroError($th));
        }
    }


    public function Buscar_AN_CI(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $codigo = "AN";
            $busquedaind = new BusquedaIndependienteController();
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


            if ($NtrasSinConcepto > 3) {
                $user = Myhelp::AuthU();
                $usermail = $user->email;
                Log::info('El job Buscar_AN_CI ha comenzado.');
                $noSerializable = $Transacciones->get();
                dispatch(
                    new BC_AnulacionesJob($noSerializable, "Cruce de $codigo finalizado", $usermail)
                )->delay(now()->addSeconds());

                $aproxSeconds = ceil($NtrasSinConcepto / 25); //25 son las que se analizan por segundo
                $aproxSeconds = $aproxSeconds > 60 ? ($aproxSeconds / 60) . ' mins' : ($aproxSeconds) . ' segs';
                return redirect()->route('transaccion.index')->with('warning',
                    $NtrasSinConcepto . ' transacciones ' . $codigo . ' de agosto serán revisadas. Este proceso tardará aprox: ' . $aproxSeconds
                );
            }

            try {
                $busquedaind->Encontrar_AN_CI($Transacciones);
                return redirect()->route('transaccion.index')->with('success',
                    'Operación exitosa. ' . $NtrasSinConcepto . ' transacciones ' . $codigo . ' de agosto fueron revisadas | solo se busca 1 CP'
                );

            } catch (\Throwable $th) {
//                DB::rollback();
                return back()->with('error', 'Ajustes con errores: ' . ZilefErrors::RastroError($th));
            }
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
