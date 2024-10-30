<?php

namespace App\helpers;

use App\Models\transaccion;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class ZilefErrors
{


    public static function RastroError($th): string
    {
        // Obtener el archivo y la línea donde ocurrió la excepción
        $filePath = str_replace(base_path(), '', $th->getFile());
        $lineError = $th->getLine();

        // Obtener el rastro del error desde la excepción, no desde el backtrace
        $backtrace = $th->getTrace();

        // Iterar sobre el backtrace para encontrar cada llamada relevante
        $traceDetails = [];
        foreach ($backtrace as $trace) {
            if (
                isset($trace['file'])
                && strpos($trace['file'], base_path('app')) !== false  // Debe estar dentro de 'app'
                && strpos($trace['file'], 'vendor') === false  // Ignorar las funciones de vendor
                && strpos($trace['file'], 'ZilefErrors') === false  // Ignorar la función de manejo de errores
            ) {
                $callerFile = basename($trace['file']);  // Usamos basename aquí para solo el nombre del archivo
                $callerLine = $trace['line'];
                $traceDetails[] = "Archivo: $callerFile en la línea $callerLine";
            }
        }

        // Mostrar todas las líneas donde ocurrió el error
        $traceOutput = implode(' | ', $traceDetails);

        // Construir el mensaje detallado
        $files = (env('APP_ENV') == 'local')
            ? 'Rastreo: ' . $traceOutput
            : 'Última llamada relevante: ' . ($traceDetails[0] ?? 'Desconocida');

        return $th->getMessage()
            . ' en la línea: ' . $lineError
            . ' en el archivo: ' . $filePath
            . ' | ' . $files;
    }

}

?>

