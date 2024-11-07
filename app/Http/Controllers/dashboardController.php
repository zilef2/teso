<?php

namespace App\Http\Controllers;

use App\helpers\MyGlobalHelp;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\afectacion;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Inspeccion;
use App\Models\Role;
use App\Models\transaccion;
use App\Models\User;
use Database\Seeders\ConceptoFlujoSeeder;
use Inertia\Inertia;

class dashboardController extends Controller
{
    public function Dashboard()
    {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' | inspeccions create | '));

        $yearnow = date('Y');
        $yearPast = $yearnow - 1;

        for ($i = $yearnow; $i >= $yearPast; $i--) {

            $ConteoEntidades[0]['transaccion' . $i] = transaccion::WhereYear('fecha_elaboracion', $i)->count();
            $ConteoEntidades[0]['Comprobanteci' . $i] = Comprobante::Where('codigo', 'ci')->WhereYear('fecha_elaboracion', $i)->count();
            $ConteoEntidades[0]['Comprobantece' . $i] = Comprobante::Where('codigo', 'ce')->WhereYear('fecha_elaboracion', $i)->count();
            $ConteoEntidades[0]['afectacion' . $i] = afectacion::WhereYear('fecha_elaboracion', $i)->count();
            $ConteoEntidades[0]['asientos' . $i] = asiento::WhereYear('fecha_elaboracion', $i)->count();

            $ConteoEntidades[1]['Comprobanteaj' . $i] = Comprobante::Where('codigo', 'aj')->WhereYear('fecha_elaboracion', $i)->count();
            $ConteoEntidades[1]['Comprobantean' . $i] = Comprobante::Where('codigo', 'an')->WhereYear('fecha_elaboracion', $i)->count();
            $ConteoEntidades[1]['Comprobanteca' . $i] = Comprobante::Where('codigo', 'ca')->WhereYear('fecha_elaboracion', $i)->count();

            //file2
            $ComparacionCP['Comprobanteci' . $i . 'sincp'] = transaccion::Where('codigo', 'ci')
//                ->WhereYear('fecha_elaboracion', $i)
                ->Where('contrapartida', 'LIKE', "%No se encontro%")->count();
            $comprobanteResta = transaccion::Where('codigo', 'ci')
//                ->WhereYear('fecha_elaboracion', $i)
                ->WhereNot('contrapartida', 'LIKE', "%No se encontro%")
                ->count();
            $comprabanteResta = $comprobanteResta - $ComparacionCP['Comprobanteci' . $i . 'sincp'];
            $ComparacionCP['Comprobanteci' . $i . 'concp'] = max($comprabanteResta, 0);
//            dd($ComparacionCP            );
        }

        //ResumenCI.js
        [$conceptos, $ResumenCI] = $this->ResumenCI($yearnow, $yearPast);
        [$conceptos2, $ResumenCI2] = $this->ResumenCI2($yearnow, $yearPast);


        return Inertia::render('Dashboard', [
            'users' => (int)User::count(),
            'roles' => (int)Role::count(),

            'rolesNameds' => Role::where('name', '<>', 'superadmin')->pluck('name'),
            'numberPermissions' => $numberPermissions,
            'ConteoEntidades' => $ConteoEntidades,
            'ComparacionCP' => $ComparacionCP,
            'ResumenCI' => $ResumenCI,
            'conceptos' => $conceptos,
            'ResumenCI2' => $ResumenCI2,
            'conceptos2' => $conceptos2,
        ]);
    }

    private function ResumenCI($yearnow, $yearPast): array
    {
//        $conceptos = concepto_flujo::Where('ingresos_o_egresos', 'ingresos')->pluck('concepto_flujo');
        $ResumenCI = [];
        $losconceptos=[];
        for ($i = $yearnow; $i >= $yearPast; $i--) {
            $transacciones = transaccion:: select('concepto_flujo_homologación', 'valor_debito')
                ->whereYear('fecha_elaboracion', $i)
                ->orderby('valor_debito', 'DESC')
                ->get()
            ;

            foreach ($transacciones as $transaccion) {
                $concepto = $transaccion->concepto_flujo_homologación;

                if ($concepto != '' && $concepto != null
                    && !str_starts_with($concepto, "No se encontro")
                    && !str_starts_with($concepto, "Buscar")
                    && !str_starts_with($concepto, "Ingreso para")
                    && !str_starts_with($concepto, "contrapartidas")
                ) {
                    // si el concepto ya existe en el arreglo, suma el valor; si no se crea
                    if (isset($ResumenCI[$i][$concepto])) {
                        $ResumenCI[$i][$concepto] += $transaccion->valor_debito;
                    } else {
                        $losconceptos[$concepto] = $concepto;
                        $ResumenCI[$i][$concepto] = intval($transaccion->valor_debito);
                    }
                }
            }
            ksort($ResumenCI[$i], SORT_NUMERIC);
        }
        return [$losconceptos, $ResumenCI];
    }
    private function ResumenCI2($yearnow, $yearPast)
    {
        $losconceptos=[];
        $ResumenCI = [];
        for ($i = $yearnow; $i >= $yearPast; $i--) {
            $transacciones = transaccion::select('concepto_flujo_homologación', 'valor_debito')
                ->whereYear('fecha_elaboracion', $i)
                ->orderby('valor_debito', 'DESC')
                ->get();

            foreach ($transacciones as $transaccion) {
                $concepto = $transaccion->concepto_flujo_homologación;

                if ($concepto != '' && $concepto != null
                    && !str_starts_with($concepto, "No se encontro")
                    && !str_starts_with($concepto, "Buscar")
                    && !str_starts_with($concepto, "contrapartidas")
                ) {
                    // si el concepto ya existe en el arreglo, suma el valor; si no se crea
                    if (isset($ResumenCI[$i][$concepto])) {
                        $ResumenCI[$i][$concepto] += $transaccion->valor_debito;
                    } else {
                        $losconceptos[$concepto] = $concepto;
                        $ResumenCI[$i][$concepto] = intval($transaccion->valor_debito);
                    }
                }
            }
        }
//        dd(
//            $ResumenCI, $losconceptos
//        );
        return [$losconceptos, $ResumenCI];
    }
}
