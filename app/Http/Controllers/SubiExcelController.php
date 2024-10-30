<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\helpers\ZilefLogs;
use App\Imports\AsientoImport;
use App\Imports\ComprobanteImport;
use App\Imports\CuentaImport;
use App\Imports\TransaccionesImport;
use App\Jobs\BC_AnulacionesJob;
use App\Jobs\testingAndDoubs;
use App\Jobs\UpAsientosJob;
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
        ZilefLogs::EscribirEnLog($this, 'subirexceles' ,' ingreso a la vista subir excel');

        return Inertia::render('Excel/subirExceles', [
            'title' => __('app.label.user'),
            'nComprobante' => (Comprobante::count()),
            'ntransaccion' => (transaccion::count()),
            'ncuenta' => (cuenta::Where('id','>',0)->count()),
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
            $$value = $personalImp->{$value};
            $bandera = $bandera || $$value > 0;
        }

        $mensaje = '';
        if ($bandera) {
            foreach ($mensajesWarnings as $key => $value) {
                if (${$contares[$key]} > 0) {
                    $NombreVariable = $contares[$key] . 'string';
                    $mensaje .= $value . '<b>'. ${$contares[$key]} . '</b>.<br><br> '. $personalImp->{$NombreVariable}.'<br> ';
                }
            }
        }

        return $mensaje;
    }

    public function upExCuentas(Request $request){
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando cuentas', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            if ($request->archivo[$request->Contador]) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request,'archivo1');
                if ($mensageWarning != ''){
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
                if ($countfilas == 0){
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                } else{
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
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . $lasession . ' error en la iteracion ' . $countfilas . ' ' .$mensajeError);
        }
    }


    public function upExTransacciones(Request $request){
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando transacciones', false);
        $countfilas = 0;
        $entidad = 'Transaccion';
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != ''){
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

                ZilefLogs::EscribirEnLog($this, 'IMPORT:'.$entidad, '. operacion con exito', false);
                DB::commit();
                if ($countfilas == 0){
                    return back()->with('warning', __('app.label.op_successfully') . ' No hubo cambios');
                } else{
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
            if(str_starts_with($th->getMessage(),'|')){
                return back()->with('warning',$mensajeError);
            }else{
                return back()->with(
                    'error', __('app.label.op_not_successfully')
                    . ' Comprobante del error: ' . $lasession
                    . ' error en la iteracion ' . $countfilas . ' ' .$mensajeError
                );
            }
        }
    }


    public function uploadFileComprobantes(Request $request){
        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando comprobantes ', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != ''){
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }

                $personalImp = new ComprobanteImport();
                Excel::import($personalImp, $thefile);

                $countfilas = $personalImp->ContarFilasAbsolutas;

                $MensajeWarning = HelpExcel::MensajeWarComprobante($personalImp);
                if ($MensajeWarning !== '') { //exito

                    return back()->with('success', 'Formularios nuevos: ' . $countfilas)
                        ->with('warning2', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                DB::commit();
                if ($countfilas == 0){
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                } else{
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

            if(str_starts_with($th->getMessage(),'|')){
                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                    . $th->getMessage(), false);
                return back()->with('warning',$th->getMessage());

            }else{

                $mensajeError = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                    . $mensajeError, false);
                return back()->with(
                    'error', __('app.label.op_not_successfully')
                    . ' Comprobante del error: ' . $lasession
                    . ' error en la iteracion ' . $countfilas . ' ' .$mensajeError
                );
            }
        }
    }
    public function uploadFileAsientos(Request $request){

        ZilefLogs::EscribirEnLog($this, get_called_class(), 'importando asientos ', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            $thefile = $request->archivo[$request->Contador];
            if ($thefile) {
                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->NewValidarArchivoExcel($request);
                if ($mensageWarning != ''){
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                }
//                ini_set('memory_limit', '2048M');

                $pesoMegabyte = ((int)($thefile->getSize())) / (1024 *1024);

//                if ($pesoMegabyte > 4) {
//                    $user = Myhelp::AuthU();
//                    $usermail = $user->email;
//                    $path = $thefile->store('AsientosJob');
//                    Log::info('UpAsientosJob ha comenzado.');
//                    Log::info('testingAndDoubs deveras ha comenzado.');
//                    dispatch(job: new UpAsientosJob(
//                        $usermail,
//                        "El archivo de asientos ha sido cargado al sistema",
//                        $path
//                    ))->delay(now()->addSeconds());
//                    dispatch(new testingAndDoubs());
//                    $aproxMinutos = ceil($pesoMegabyte / 3);
//                    ini_set('memory_limit', '256M');
//                    return back()->with('warning',
//                        'Se avisará por correo cuando la carga finalize. Este proceso tardará aprox: ' . $aproxMinutos . ' minutos'
//                    );
//                }

                //si es liviano, continue
                $elImport = new AsientoImport();
                Excel::import($elImport, $thefile);

                ini_set('memory_limit', '256M');
                $countfilas = $elImport->ContarFilas;
                $MensajeWarning = HelpExcel::MensajeWarSoloVacios($elImport);
                if ($MensajeWarning !== '') { //exito
                    return back()->with('success', 'asientos nuevos: ' . $countfilas)
                        ->with('warning', $MensajeWarning);
                }

                ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                DB::commit();
                if ($countfilas == 0){
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
dd(
    $mensajeError
);
            $tipoError = 'error';
            if(str_starts_with($th->getMessage(),'|')){
                $tipoError = 'warning';
            }
            ZilefLogs::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                . $mensajeError, false);
            return back()->with(
                $tipoError, __('app.label.op_not_successfully')
                . ' Asiento del error: ' . $lasession
                . ' error en la iteracion ' . $countfilas . ' ' .$mensajeError
            );
        }
    }

}
