<?php

namespace App\helpers;

use App\Models\Parametro;
use App\Models\transaccion;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

//JUST THIS PROJECT
//STRING S
//LARAVELFunctions
//dateFunctions
//arrayFunctions

class ZilefLogs {

    public function erroresExcel($errorFeo) {
        // $fila = session('ultimaPalabra');
        $error1 = "PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect date";
        if ($errorFeo == $error1) {
            return 'Existe una fecha invalida';
        }
        return 'Error desconocido';
    }
    public static function EscribirEnLog($thiis, $clase = '', $mensaje = '', $returnPermission = true, $critico = false) {
        $permissions = $returnPermission ? auth()->user()->roles->pluck('name')[0] : null;
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
        $nombreC = end($ListaControladoresYnombreClase);
        if (!$critico) {
            $Elpapa = (explode('\\', get_parent_class($thiis)));
            $nombreP = end($Elpapa);

            if ($permissions == 'admin' || $permissions == 'superadmin') {
                $ElMensaje = $mensaje != '' ? ' Mensaje: ' . $mensaje : 'mensaje Null desde erroresExcel';

                Log::info('Vista:' . $nombreC . ' Padre: ' . $nombreP . '|  U:' . Auth::user()->name . $ElMensaje);
                //Log::channel('soloadmin')->info('Vista:' . $nombreC . ' Padre: ' . $nombreP . '|  U:' . Auth::user()->name . $ElMensaje);
            } else {
                Log::info('Vista: ' . $nombreC . ' Padre: ' . $nombreP . '||  U:' . Auth::user()->name ?? 'us desconocido'.' | '. $clase . '| ' . $mensaje);
            }
        } else {
            Log::critical('Vista: ' . $nombreC . ' ||| U:' . Auth::user()->name . ' ||' . $clase . '|| ' . $mensaje);
        }
        return $permissions;
    }

    public static function NoAuthLog($thiis, $clase = '', $mensaje = '', $returnPermission = true, $critico = false) {
        $permissions = $returnPermission ? auth()->user()->roles->pluck('name')[0] : null;
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
        $nombreC = end($ListaControladoresYnombreClase);
        if (!$critico) {

            $Elpapa = (explode('\\', get_parent_class($thiis)));
            $nombreP = end($Elpapa);

            if ($permissions == 'admin' || $permissions == 'superadmin') {
                $ElMensaje = $mensaje != '' ? ' Mensaje: ' . $mensaje : '';
                Log::channel('soloadmin')->info('Vista:' . $nombreC . ' Padre: ' . $nombreP . '|  U:' . Auth::user()->name . $ElMensaje);
            } else {
                Log::info('Vista: ' . $nombreC . ' Padre: ' . $nombreP .' | '. $clase . '| ' . ' Mensaje: ' . $mensaje);
            }
            return $permissions;
        } else {
//                Log::critical('Vista: ' . $nombreC . 'U:' . $clase . '|| ' . ' Mensaje: ' . $mensaje);
            Log::critical('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||' . $clase . '|| ' . ' Mensaje: ' . $mensaje);
        }
        return $permissions;
    }
}
?>

