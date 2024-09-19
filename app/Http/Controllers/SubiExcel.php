<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Imports\PersonalImport;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class SubiExcel extends Controller
{

    public function subirexceles()
    { //just  a view
        Myhelp::EscribirEnLog($this, ' se subio un excel');

        return Inertia::render('Excel/subirExceles', [
            'title' => __('app.label.user'),
            'numUsuarios' => (Formulario::Where('enviado',1)->count()),
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

    public function uploadExcel(Request $request){
        Myhelp::EscribirEnLog($this, get_called_class(), 'uploadtrabajadors importando', false);
        $countfilas = 0;
        try {
            DB::beginTransaction();
            if ($request->archivo1) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request);
                if ($mensageWarning != ''){
                    DB::rollback();
                    return back()->with('warning', $mensageWarning);
                } 

                $personalImp = new PersonalImport();
                Excel::import($personalImp, $request->archivo1);

                $countfilas = $personalImp->ContarFilas;

                $MensajeWarning = $this->MensajeWar($personalImp);
                if ($MensajeWarning !== '') {
                    Formulario::where('user_id', $personalImp->usuario->id)->update(['enviado' => 1]);
                    
                    return back()->with('success', 'Formularios nuevos: ' . $countfilas)
                        ->with('warning2', $MensajeWarning);
                }

                Myhelp::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                DB::commit();
                if ($countfilas == 0){
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                } else{
                    Formulario::where('user_id', $personalImp->usuario->id)->update(['enviado' => 1]);
                    
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
            
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $mensajeError, false);
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . $lasession . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
}
