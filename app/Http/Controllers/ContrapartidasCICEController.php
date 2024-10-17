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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $this->Transacciones = $Transacciones;

        $conteoTransac = $Transacciones->count();
        if ($conteoTransac < 0) {
            dispatch(new BusquedaConceptoCIJob($Transacciones))->delay(now()->addSeconds());
            $aproxSeconds = ceil($conteoTransac / 25);
            $aproxSeconds = $aproxSeconds > 60 ? ($aproxSeconds / 60) . ' mins' : ($aproxSeconds) . ' segs';
            return redirect()->route('transaccion.index')->with('success',
                $conteoTransac . ' transacciones ' . $codigo . ' de agosto serán revisadas. Este proceso tardará aprox: ' . $aproxSeconds
            );
        }

        $inicioicrotime = microtime(true);
        Log::info('El job BusquedaConceptoCI ha comenzado.');

        try {
            // Log después de la operación
            $codigo = "AJ";

            $CPController = new ContrapartidasCICEController();
            DB::beginTransaction();
            foreach ($this->Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', 'aj');

                if ($CPController->NoHayComprobantes($comprobantes, $transa)) {
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
                $principal = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->first();
                //todo: si hay mas, es error, del excel o que se permitio subir 2 veces

                $lasContrapartidas = $lasContrapartidas->WhereNot('id', $principal->id)
                    ->Where('documento_ref', $principal->documento_ref)
                    ->get();

                //AJUSTES
                if ($CPController->LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principal)) {
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida_CI' => 'No se encontró una suma coherente, no suman 0',
                        'concepto_flujo_homologación' => 'No se encontró una suma coherente, no suman 0',
                    ]);
                    continue;
                }

                //va y busca los demas
//                foreach ($Todas as $principal) {
                    //validacion adicional: el doc_ref debe ser igual para el original y la CP
                    $Col_ContrapartidaRef = $lasContrapartidasForeach->Where('documento_ref', $principal->documento_ref)
                        ->WhereNotIn('id', $principal->id)
                        ->Where('valor_credito', $transa->valor_debito)
                        ->get()
                        ->sortByDesc('valor_credito')
                        ->first();


                    if ($Col_ContrapartidaRef) {
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida_CI' => 'No se encontro CP para doc_ref',
                            'concepto_flujo_homologación' => 'No se encontro CP para doc_ref',
                        ]);
                    } else {
                        $int_ContrapartidaRef = intval($Col_ContrapartidaRef->codigo_cuenta);
                        $concepto = $CPController->hallarConcepto($int_ContrapartidaRef, $codigo);
                        $IntCp = count($lasContrapartidasForeach);
                        $IntCp = $IntCp == 0 ? $IntCp : $IntCp - 1;
                        $transa->update([
                            'n_contrapartidas' => $IntCp,
                            'contrapartida_CI' => $int_ContrapartidaRef,
                            'concepto_flujo_homologación' => $concepto,
                        ]);
                    }
//                }
            }
            DB::commit();
            Log::info('El job BusquedaConceptoCI ha terminado exitosamente.');
            $finicrotime = microtime(true);
            Parametro::create([
                'Fecha_creacion_parametro' => date('Y-m-d H:i:s'),
                'nombre' => 'Cruzar Ajustes CI',
                'valor' => number_format($finicrotime - $inicioicrotime, 3),
                'categoria' => 'jobs_' . $inicioicrotime,
                'numero' => number_format($finicrotime, 3),
            ]);

            return redirect()->route('transaccion.index')->with('success',
                'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
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
            $Transacciones = transaccion::Where('codigo', $codigo)->WhereNull('concepto_flujo_homologación');
            $NtrasArchivo = transaccion::Where('codigo', $codigo)->count();
            $NtrasSinConcepto = clone $Transacciones;
            $NtrasSinConcepto = $NtrasSinConcepto->count();
            if ($NtrasSinConcepto === 0)
                return back()->with('warn', 'Las AN ya han sido cruzadas');
            if ($NtrasArchivo === 0)
                return back()->with('error', 'Sin anulaciones disponibles ');

            DB::beginTransaction();
            foreach ($Transacciones->get() as $index => $transa) {

                $laContra = transaccion::Where('documento', $transa->documento)->Where('id', '!=', $transa->id)->first();

                if ($laContra === null) {
                    $mensaje_t_Error = "No se encontro la contrapartida en el archivo auxiliar con el documento " . $transa->documento;
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida_CI' => $mensaje_t_Error,
                        'concepto_flujo_homologación' => $mensaje_t_Error,
                    ]);
                    continue;
                }

                //ANULACIONES
                if ($this->LaContraPartidaNoSumaCero($laContra, $transa, $transa)) continue;

                $concepto = $this->hallarConcepto($laContra->codigo_cuenta_contable, $codigo);
                $transa->update([
                    'n_contrapartidas' => 1,
                    'contrapartida_CI' => $laContra->codigo_cuenta_contable,
                    'concepto_flujo_homologación' => $concepto,
                ]);
            }
            DB::commit();

            return redirect()->route('transaccion.index')->with('success',
                'Operación exitosa. ' . $NtrasSinConcepto . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
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
        return "Sin Concepto encontrado en $codigo";
    }

    public function LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales): bool
    {
        $sumprincipales = $principales->sum('valor_debito');
        $sumlasContrapartidas = $lasContrapartidas->sum('valor_credito');
        $elCero = abs($sumprincipales) !== abs($sumlasContrapartidas);
        if ($elCero) {
            $transa->update([
                'contrapartida_CI' => "No suman cero. PRINCIPAL DEBITO $sumprincipales",
                'concepto_flujo_homologación' => "CONTRAPARTIDA CREDITO $sumlasContrapartidas",
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
                'contrapartida_CI' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                'concepto_flujo_homologación' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
            ]);
        }
        return $HayComprobantes === 0;
    }

    public function BorrarConceptos(): void
    {
        $laFecha = new \DateTime();
        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $mes = 8; // agosto
//      $anio = $laFecha->format('Y'); // Obtiene el año

        $conteo = transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->count();
        $tranListas = transaccion::WhereNotNull('concepto_flujo_homologación')->whereMonth('fecha_elaboracion', $mes)->update([
            'n_contrapartidas' => null,
            'contrapartida_CI' => null,
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
