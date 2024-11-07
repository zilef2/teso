<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefErrors;
use App\helpers\ZilefLogs;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\BusquedaConceptoCI_AJJob;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use App\Models\transaccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\NoReturn;
use function Psy\debug;

#[AllowDynamicProperties] class ContrapartidasCIController extends Controller
{
    //todo: falta traer y configurar "Buscar_CP_CI" que esta en transaccionController
    public string $FromController = 'Comprobante';
    public function __construct()
    {
        $this->arrayFillableSearch = [
            'codigo',
            'numero_documento',
            'valor_debito',
            'valor_credito',
            'resultado_asientos',
        ];
    }

    public function Buscar_AJ_CI(Request $request): RedirectResponse
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


    public function Buscar_AN_CI(Request $request): RedirectResponse
    {
        try {
            $codigo = "AN";
            $busquedaind = new BusquedaIndependienteController();
            //<editor-fold desc="inicio">
            $Transacciones = transaccion::Where('codigo', $codigo)
                //                ->WhereNull('concepto_flujo_homologacion')
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
                    'Operacion exitosa. ' . $NtrasSinConcepto . ' transacciones ' . $codigo . ' de agosto fueron revisadas | solo se busca 1 CP'
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
                'concepto_flujo_homologacion' => "CONTRAPARTIDA CREDITO: $sumlasContrapartidas",
            ]);
        }
        return $elCero;
    }

    public function NoHayComprobantes($comprobantes, $transa): bool
    {
        $HayComprobantes = $comprobantes->count();
        if ($HayComprobantes === 0) {
            $transa->update([
                'contrapartida' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                'concepto_flujo_homologacion' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
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

        $conteo = transaccion::WhereNotNull('concepto_flujo_homologacion')->whereMonth('fecha_elaboracion', $mes)->count();
        $tranListas = transaccion::WhereNotNull('concepto_flujo_homologacion')->whereMonth('fecha_elaboracion', $mes)->update([
            'n_contrapartidas' => null,
            'contrapartida' => null,
            'concepto_flujo_homologacion' => null,
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
    public function BorrarAsientos(): void
    {
        $conteo = asiento::count();
        $asientos = asiento::where('id','>',0)->delete();
        echo "$conteo asientos eliminados";
    }
    public function Borrarcomprobantesce(): void
    {
        $conteo = Comprobante::Where('codigo', 'ce')->count();
        $Comprobantes = Comprobante::Where('codigo','ce')->delete();
        echo "$conteo comprobantes ce eliminados";
    }

    //</editor-fold>


    //<editor-fold desc="IndexCE">
    private function Mapear($clase)
    {
        $Result = $clase->map(function ($clas) {
            return $clas;
        });

        return $Result;
    }

    public function BusquedasText($laclase, $request)
    {
        foreach ($this->arrayFillableSearch as $index => $busqueda) {
            if ($request->has($busqueda)) {
                $laclase = $laclase->where(function ($query) use ($request, $busqueda) {
                    $query->where($busqueda, 'LIKE', "%" . $request->{$busqueda} . "%");
                });
            }
        }
        return $laclase->get();
    }

    public function Filtros($request)
    {
        $Comprobantes = Comprobante::Where('codigo','CE');

        if ($request->has(['field', 'order'])) {
            $Comprobantes = $Comprobantes->orderBy($request->field, $request->order);
        } else {
            $Comprobantes = $Comprobantes->orderBy('updated_at', 'DESC');
        }
        return $this->BusquedasText($Comprobantes, $request);
    }


    public function IndexCE(Request $request): Response
    {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' IndexCE '));
        $laclase = $this->Mapear($this->Filtros($request));


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $total = $laclase->count();
        $page = request('page', 1);
        $fromController = new LengthAwarePaginator(
            $laclase->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render($this->FromController . '/IndexCE', [
            'fromController' => $fromController,
            'total' => $total,

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['field', 'order',
                'codigo',
                'numero_documento',
                'valor_debito',
                'valor_credito',
            ]),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
        ]);
    }
    //</editor-fold>
}
