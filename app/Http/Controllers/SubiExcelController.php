<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\helpers\ZilefLogs;
use App\Imports\afectacionImport;
use App\Imports\AsientoImport;
use App\Imports\ComprobanteImport;
use App\Imports\ConAfectacionImport;
use App\Imports\CuentaImport;
use App\Imports\PreAfectacionImport;
use App\Imports\PreComprobanteImport;
use App\Imports\TransaccionesImport;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\UpAsientosJob;
use App\Jobs\UpComprobantesJob;
use App\Models\asiento;
use App\Models\Comprobante;
use App\Models\cuenta;
use App\Models\transaccion;
use Illuminate\Http\RedirectResponse;
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
            cuenta::count(),
        ];
        return Inertia::render('Excel/subirExceles', [
            'title' => 'Importar datos',
            'ntransaccion' => $ntransaccion,
        ]);
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
                $pesoKilobyte = ((int)($thefile->getSize())) / (1024);
//                if ($pesoKilobyte > 500) {
//                    Excel::queueImport($personalImp, $thefile);
//                    $mensajesito = "La operacion no debera tardar mucho";
//                    return back()->with('warning', $mensajesito);
//                } else {
                Excel::import($personalImp, $thefile);

                $countfilas = $personalImp->ContarFilasAbsolutas;
                $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);
                if ($MensajeWarning !== '') { //exito
                    return back()->with('success', 'Registros nuevos: ' . $countfilas)
                        ->with('warning', $MensajeWarning);
//                            ->with('warning2', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $entidad, '. operacion con exito', false);
                DB::commit();
                return back()->with('success', "El archivo se procesará en segundo plano");
//                    if ($countfilas == 0) {
//                        return back()->with('warning', __('app.label.op_successfully') . ' No hubo cambios');
//                    } else {
//                        return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
//                    }
//                }
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


    public function uploadFileComprobantes(Request $request): RedirectResponse
    {
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando comprobantes ', false);
        $countfilas = 0;

        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {

                $user = Myhelp::AuthU();
                $path = $thefile->store('ComprobantesJob');
                $pesoMegabyte = ((int)($thefile->getSize())) / (1024 * 1024);
                $aproxMinutos = ceil($pesoMegabyte * 2);
//                $limitMemory = intval($pesoMegabyte < 1 ? 1 : $pesoMegabyte) *4;
//                ini_set('memory_limit', $limitMemory.'G');
                ini_set('memory_limit', '3G');

                $helpExcel = new HelpExcel();
                Log::info('Validacion de archivo uploadFileComprobantes');
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != '') {
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }

                if ($pesoMegabyte > 1) {
                    [$tipoReturn, $mensajesin] = $this->encolarCruce($user, $path, $aproxMinutos);
                } else {
                    [$tipoReturn, $mensajesin] = $this->realizarCruce($thefile);
                }
                DB::commit();
                return back()->with($tipoReturn, $mensajesin);
            } else {
                DB::rollback();
                return back()->with('error', __('app.label.op_not_successfully') . ' Archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $lasession = session('larow') ?? 'error de session';
            $lasession = $lasession[0] ?? 'error de session';
            $messagetype = 'error';
            if (str_starts_with($th->getMessage(), '|')) {
                $messagetype = 'warning';
            }
            $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                . $mensajeError, false);

            return back()->with($messagetype, __('app.label.op_not_successfully')
                . ' Comprobante del error: ' . $lasession
                . ' error en la iteracion ' . $countfilas . ' ' . $mensajeError
            );
        }
    }

    private function encolarCruce($user, $path, $aproxMinutos): array
    {
        dispatch(new UpComprobantesJob($user->email, "Carga de archivo de comprobantes, finalizada con exito", $path))->delay(now()->addSeconds());
        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'IMPORT:Comprobantes', ' finalizo con exito', false);
//        return back()->with('warning',
//            'Se avisará por correo cuando la carga finalize. Este proceso tardará aprox: ' . $aproxMinutos . ' minutos'
//        );
        return ['warning', 'Este proceso tardará aprox: ' . $aproxMinutos . ' minutos'];
    }

    private function realizarCruce($thefile): array
    {
        try {
            $PrepersonalImp = new PreComprobanteImport();
            Excel::import($PrepersonalImp, $thefile);
            if ($PrepersonalImp->ConProblemas)
                return ['Error', 'Error'];

            $personalImp = new ComprobanteImport();
            Excel::import($personalImp, $thefile);
            $countfilas = $personalImp->ContarFilasAbsolutas;

            $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);
            if ($MensajeWarning !== '') {
                return ['warning', $MensajeWarning];
            }
            $mensajetype = 'success';
            if ($countfilas == 0) {
                $mensajetype = 'warning';
                return [$mensajetype, __('app.label.op_successfully') . ' No hubo cambios'];
            } else {
                return [$mensajetype, __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito'];
            }
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    public function uploadFileAfe(Request $request)//todo: esto no se ha verificado desde el cambio del 8nov2024
    {
        $ente = "Afectacion";
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando sin afe' . $ente, false);
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

    public function uploadFileConAfe(Request $request)//todo: esto no se ha verificado desde el cambio del 8nov2024
    {
        $ente = "ceconafectacion";
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando con afe ' . $ente, false);
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
                $personalImp = new ConAfectacionImport();
                Excel::import($personalImp, $thefile);
                $countfilas = $personalImp->ContarFilas;
                $ContarFilasAbsolutas = $personalImp->ContarFilasAbsolutas;

                $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);
                if ($MensajeWarning !== '') {
                    ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $ente, ' finalizo con exito, se leyeron ' . $ContarFilasAbsolutas . ' filas', false);
                    return back()->with('success', "Archivo de: $ente. Se han leido $countfilas filas")
                        ->with('warning2', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:' . $ente, ' finalizo con exito', false);

                DB::commit();
                return back()->with('success', "El archivo se procesará en segundo plano");
//                if ($countfilas == 0) {
//                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
//                } else {
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

    public function uploadFileAsientos(Request $request): RedirectResponse
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
