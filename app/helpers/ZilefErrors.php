<?php

namespace App\helpers;

use App\Models\transaccion;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class ZilefErrors {
    
    
    public static function RastroError($th){
        // Obtener rastro de error
    $backtrace = debug_backtrace();
    
    // Verificar si hay un archivo "padre" (el archivo que llamó)
    $callerFile = $backtrace[1]['file'] ?? '1nd Desconocido';
    $callerLine = $backtrace[1]['line'] ?? '1nd Desconocida';
    $callerFile2 = $backtrace[2]['file'] ?? '2nd Desconocido';
    $callerLine2 = $backtrace[2]['line'] ?? '2nd Desconocida';
    
     // Reducir la ruta del archivo
    $filePath = str_replace(base_path(), '', $th->getFile());
    $callerFilePath = str_replace(base_path(), '', $callerFile);
    
    $filePath .= str_replace(base_path(), '', $th->getFile());
    $callerFilePath2 = str_replace(base_path(), '', $callerFile2);
    
      foreach ($backtrace as $trace) {
        if (
            isset($trace['file']) 
            && strpos($trace['file'], base_path('app')) !== false  // Debe estar en 'app'
            && strpos($trace['file'], 'ZilefErrors') === false  // Ignorar la función de manejo de errores
        ) {
            $callerFileR = $trace['file'];
            $callerLineR = $trace['line'];
            break;  // Detenerse en el primer archivo dentro de 'app'
        }
    }
    if(env('APP_ENV') == 'local'){
        $files = ' Padre: ' . $callerFilePath . " abuelo $callerFilePath2";
    }else{
        $files = ' Padre: ' . $callerFilePath ;
    }
    
    // Construir mensaje detallado
    return $th->getMessage() 
                . ' L:' . $th->getLine() 
                . ' Ubi: ' . $filePath
                . $files
                . ' L ' . $callerLine . ' L2: '.$callerLine2
                . " ubis: $callerFileR | Ls: $callerLineR";
    }
}
?>

