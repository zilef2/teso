<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\helpers\ZilefLogs;
use App\Imports\afectacionImport;
use App\Imports\AsientoImport;
use App\Imports\ComprobanteImport;
use App\Imports\CuentaImport;
use App\Imports\PreAfectacionImport;
use App\Imports\TransaccionesImport;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\testingAndDoubs;
use App\Jobs\UpAsientosJob;
use App\Jobs\UpComprobantesJob;
use App\Models\afectacion;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\cuenta;
use App\Models\transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class SubiExcelController extends Controller
{

    public function subirexceles()
    { //just  a view
        ZilefLogs::EscribirEnLog($this, 'subirexceles', ' ingreso a la vista subir excel');
        $ntransaccion = [
            0,
            transaccion::count(),
            Comprobante::count(),
            asiento::count(),
            cuenta::count(),
            afectacion::count(),
        ];
        return Inertia::render('Excel/subirExceles', [
            'title' => __('app.label.user'),
            'ntransaccion' => $ntransaccion,
        ]);
    }

    // Duplicate entry '1152194566' for key 'users_identificacion_unique'
    private function MensajeWar($personalImp)
    {
        $bandera = false;
        $contares = [
            'contarVacios',
            'contarTotalIncongruente',
            'contarIncongruencias',
            'contarUsuariosInexistentes',
        ];
        $mensajesWarnings = [
            '# Filas con celdas vacias: ',
            '# Hay Filas: total con incongruencias: ',
            '# Incongruencias: ',
            '# Usuarios Inexistentes: ',
        ];


        foreach ($contares as $key => $value) {
            if ($personalImp->{$value}) {
                $$value = $personalImp->{$value};
                $bandera = $bandera || $$value > 0;
            }
        }

        $mensaje = '';
        if ($bandera) {
            foreach ($mensajesWarnings as $key => $value) {
                if (${$contares[$key]} > 0) {
                    $NombreVariable = $contares[$key] . 'string';
                    $mensaje .= $value . '<b>' . ${$contares[$key]} . '</b>.<br><br> ' . $personalImp->{$NombreVariable} . '<br> ';
                }
            }
        }

        return $mensaje;
    }

    public function upExCuentas(Request $request)
    {
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando cuentas', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            if ($request->archivo[$request->Contador]) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request, 'archivo1');
                if ($mensageWarning != '') {
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }

                $personalImp = new CuentaImport();
                Excel::import($personalImp, $request->archivo1);

                $countfilas = $personalImp->ContarFilasAbsolutas;

                $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);

                if ($MensajeWarning !== '') { //exito
                    return back()->with('success', 'Formularios nuevos: ' . $countfilas)
                        ->with('warning2', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                DB::commit();
                if ($countfilas == 0) {
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                } else {
//                    cuenta::where('user_id', $personalImp->usuario->id)->update(['enviado' => 1]);
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
                }

            } else {
                DB::rollback();
                return back()->with('error', __('app.label.op_not_successfully') . ' Archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $lasession = session('larow') ?? 'error de session';
            $lasession = $lasession[0] ?? 'error de session';
            $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();

            ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $mensajeError, false);
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . $lasession . ' error en la iteracion ' . $countfilas . ' ' . $mensajeError);
        }
    }


    public function upExTransacciones(Request $request)
    {
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando transacciones', false);
        $countfilas = 0;
        $entidad = 'Transaccion';
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != '') {
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }

                $personalImp = new TransaccionesImport();
                Excel::import($personalImp, $thefile);

                $countfilas = $personalImp->ContarFilasAbsolutas;
                $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);

                if ($MensajeWarning !== '') { //exito
                    return back()->with('success', 'Registros nuevos: ' . $countfilas)
                        ->with('warning2', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $entidad, '. operacion con exito', false);
                DB::commit();
                if ($countfilas == 0) {
                    return back()->with('warning', __('app.label.op_successfully') . ' No hubo cambios');
                } else {
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
                }
            } else {
                DB::rollback();
                return back()->with('error', __('app.label.op_not_successfully') . ' Archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $lasession = session('larow') ?? 'error de session';
            $lasession = $lasession[0] ?? 'error de session';
            $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();

            ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $mensajeError, false);
            if (str_starts_with($th->getMessage(), '|')) {
                return back()->with('warning', $mensajeError);
            } else {
                return back()->with(
                    'error', __('app.label.op_not_successfully')
                    . ' Comprobante del error: ' . $lasession
                    . ' error en la iteracion ' . $countfilas . ' ' . $mensajeError
                );
            }
        }
    }


    public function uploadFileComprobantes(Request $request)
    {
        ini_set('memory_limit', '40000M');

        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando comprobantes ', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != '') {
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }

                $personalImp = new ComprobanteImport();
                Excel::import($personalImp, $thefile);
                $countfilas = $personalImp->ContarFilasAbsolutas;
                $user = Myhelp::AuthU();
                $path = $thefile->store('ComprobantesJob');
                dispatch(new UpComprobantesJob($user->email, "Carga de archivo de comprobantes, finalizada con exito", $path))->delay(now()->addSeconds());
                DB::commit();
                $pesoMegabyte = ((int)($thefile->getSize())) / (1024 * 1024);
                $aproxMinutos = ceil($pesoMegabyte / 2);
                return back()->with('warning',
                    'Se avisará por correo cuando la carga finalize. Este proceso tardará aprox: ' . $aproxMinutos . ' minutos'
                );

                //todo: verificar el manejo de excepciones para el job
//                $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);
//                if ($MensajeWarning !== '') { //exito
//
//                    return back()->with('success', 'Formularios nuevos: ' . $countfilas)
//                        ->with('warning2', $MensajeWarning);
//                }
//
//                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
//                DB::commit();
//                if ($countfilas == 0) {
//                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
//                } else {
////                    cuenta::where('user_id', $personalImp->usuario->id)->update(['enviado' => 1]);
//
//                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
//                }

            } else {
                DB::rollback();
                return back()->with('error', __('app.label.op_not_successfully') . ' Archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $lasession = session('larow') ?? 'error de session';
            $lasession = $lasession[0] ?? 'error de session';

            //todo: cambiar warning y error y ya, no se necesita un ifelse
            if (str_starts_with($th->getMessage(), '|')) {
                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                    . $th->getMessage(), false);
                return back()->with('warning', $th->getMessage());

            } else {

                $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                    . $mensajeError, false);
                return back()->with(
                    'error', __('app.label.op_not_successfully')
                    . ' Comprobante del error: ' . $lasession
                    . ' error en la iteracion ' . $countfilas . ' ' . $mensajeError
                );
            }
        }
    }

    public function uploadFileAfe(Request $request)
    {
        $ente = "Afectacion";
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando ' . $ente, false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != '') {
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }
                $preImport = new PreAfectacionImport();
                Excel::import($preImport, $thefile);
                $countfilasAbsolutas = $preImport->ContarFilasAbsolutas;

                $personalImp = new afectacionImport();
                Excel::import($personalImp, $thefile);
                $countfilas = $personalImp->ContarFilas;

                $MensajeWarning = HelpExcel::MensajeWarComprobante($preImport);
                if ($MensajeWarning !== '') {
                    ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $ente, ' finalizo con exito, se leyeron ' . $countfilasAbsolutas . ' filas', false);
                    return back()->with('success', "Archivo de: $ente. Se han leido $countfilas filas")
                        ->with('warning2', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $ente, ' finalizo con exito', false);
                DB::commit();
                if ($countfilas == 0) {
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                } else {
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
                }
            } else {
                DB::rollback();
                return back()->with('error', __('app.label.op_not_successfully') . ' Archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $lasession = session('larow') ?? 'error de session';
            $lasession = $lasession[0] ?? 'error de session';

            if (str_starts_with($th->getMessage(), '|')) {
                ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $ente, ' Fallo importacion: '
                    . $th->getMessage(), false);
                return back()->with('warning', $th->getMessage());

            } else {

                $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
                ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $ente, ' Fallo importacion: '
                    . $mensajeError, false);
                return back()->with(
                    'error', __('app.label.op_not_successfully')
                    . ' Comprobante del error: ' . $lasession
                    . ' error en la iteracion ' . $countfilas . ' ' . $mensajeError
                );
            }
        }
    }

    public function uploadFileAsientos(Request $request): \Illuminate\Http\RedirectResponse
    {
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando asientos ', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {
                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != '') {
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }
            }
            $user = Myhelp::AuthU();
            $path = $thefile->store('AsientosJob');
            dispatch(new UpAsientosJob($user->email, "Carga de archivo de asientos, finalizada con exito", $path))->delay(now()->addSeconds());
//            (new \App\Jobs\UpAsientosJob('ajelof2@gmail.com','no sale error',$path))->handle();

            DB::commit();
            $pesoMegabyte = ((int)($thefile->getSize())) / (1024 * 1024);
            $aproxMinutos = ceil($pesoMegabyte / 2);
            return back()->with('warning',
                'Se avisará por correo cuando la carga finalize. Este proceso tardará aprox: ' . $aproxMinutos . ' minutos'
            );
        } catch (\Throwable $th) {
            DB::rollback();

            $lasession = session('larow') ?? 'error de session';
            $lasession = $lasession[0] ?? 'error de session';
            $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            $tipoError = 'error';
            if (str_starts_with($th->getMessage(), '|')) {
                $tipoError = 'warning';
            }
            ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                . $mensajeError, false);
            return back()->with(
                $tipoError, __('app.label.op_not_successfully')
                . ' Asiento del error: ' . $lasession
                . ' error en la iteracion ' . $countfilas . ' ' . $mensajeError
            );
        }
    }

}
