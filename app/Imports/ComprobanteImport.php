<?php

namespace App\Imports;

use App\helpers\BytesHelp;
use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\helpers\ZilefLogs;
use App\Models\Comprobante;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class ComprobanteImport implements ToModel, WithStartRow, WithMapping, WithChunkReading
//    , SkipsOnFailure
//    , ShouldQueue
//    , WithLimit
{

    use Importable, SkipsFailures;

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $SoloUnaVez = 0;

    protected array $DebenSerNulos;

    //manejo de errores
    public int $contarVacios;
    public string $contarVaciosstring;
    public string $nombrePropio;
    public string $MensajeMortal;
    private $pruebaMap = 0;

    /**
     * @throws \Exception
     */
    function __construct()
    {
//        if ($valor < 0) {
//            throw new \Exception("El valor no puede ser negativo.");
//        }

        $this->nombrePropio = 'comprobante';
        //contares
        $this->ContarFilasAbsolutas = 1;
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0;
        $this->contarVaciosstring = "";
        $this->MensajeMortal = '';
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        if (App::environment('local')) {
            return 1000;
        } else {
            return 2500;
        }
    }

    public function map($row): array
    {
        $this->pruebaMap++;
        if ($this->pruebaMap % 100 === 0) {
            Log::info('Prueba Real mapeo = ' . $this->pruebaMap);
//            gc_collect_cycles();
        }
        return array_slice($row, 0, 19);
    }
//    public function limit(): int
//    {
//        return 1;
//    }

    public function Requeridos($theRow)
    {
//        $this->ContarFilasAbsolutas++;
        $columnasPermitidasVacias = [
            3, //descripcion
            11, //ccostos
            12, //nit
            13 //nombre
        ];
        foreach ($theRow as $key => $value) {
            if (in_array($key, $columnasPermitidasVacias)) {
                continue;
            }
            if (is_null($value) || $value === '') {
                $mensajesito = implode($theRow) . ' ||\n ' . $value . ' || VALOR VACIO EN LA FILA ' . $this->ContarFilasAbsolutas . ' columna ' . $key;
                $this->MensajeMortal = $mensajesito;
                Log::info($mensajesito);
                throw new Exception('VALOR VACIO EN LA FILA ' . $this->ContarFilasAbsolutas . ' columna ' . $key);
            }
        }


        if (!is_string(($theRow[0]))) { //intval
            $mensajesito = implode($theRow) . ' ||\n ' . $theRow[0] . ' || TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA ' . $this->ContarFilasAbsolutas . ' columna ' . $key;
            $this->MensajeMortal = $mensajesito;
            Log::info($mensajesito);
            throw new Exception($mensajesito . $this->ContarFilasAbsolutas);
        }

        if (!is_string($theRow[1])) {
            $mensajesito = implode($theRow) . ' ||\n ' . $theRow[1] . ' || TIPO DE VALOR INCORRECTO(deberia ser un texto) EN LA FILA ' . $this->ContarFilasAbsolutas;
            $this->MensajeMortal = $mensajesito;
            Log::info($mensajesito);
        }

        //validar valor_debito valor_credito
//        if (!is_string($theRow[2])){
//            dd($theRow,$theRow[2],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
//        }

        return true;
    }

    /**
     * @param array $row
     *
     * @throws Exception
     */
    public function model(array $row)
    {
        try {
            $this->ContarFilasAbsolutas++;

            $result = $this->TheNewObject($row);
            $this->ContarFilas++;
            return $result;
        } catch (\Throwable $th) {
            // Capturar información detallada del error
            $errorDetails = [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'memory_current' => BytesHelp::formatBytes(memory_get_usage(true)),
                'memory_peak' => BytesHelp::formatBytes(memory_get_peak_usage(true)),
                'available_memory' => BytesHelp::formatBytes(BytesHelp::getAvailableMemory())
            ];
            Log::error("Error en UpComprobantesJob: " . print_r($errorDetails, true));
            $mensajeError = '  ' . $th->getMessage() . '. Informar al desarrollador - L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            ZilefLogs::EscribirEnLogParaJobs($this, 'IMPORT:comprobante ', $mensajeError);
            throw new \Exception($mensajeError);
        }
    }

    public function onFailure(Failure ...$failures): void
    {
        Log::info('que mierda2 onfailure');
        $mensajeError = implode($failures);
        ZilefLogs::EscribirEnLogParaJobs($this, 'IMPORT:comprobante fallo en la fila ' . $this->ContarFilasAbsolutas . ' | ', $mensajeError);
    }

    /**
     * @throws \Exception
     */
    private function HaSidoGuardadoAnteriormente($therow): array
    {
        $laFecha = HelpExcel::getFechaExcel($therow[7]);
        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $anio = $laFecha->format('Y'); // Obtiene el año

        $ExisteUnComprobante = Comprobante::Where('codigo', $therow[0])
            ->WhereYear('fecha_elaboracion', $anio)
            ->whereMonth('fecha_elaboracion', $mes)->count();

        $mesYanio = $mes . '-' . $anio;
        $this->SoloUnaVez++;
        return [$ExisteUnComprobante, $mesYanio];
    }

    private function TheNewObject($therow)
    {
        try {
            $compro = new Comprobante([
                'codigo' => $therow[0],
                'descripcion' => $therow[1],
                'comprobante' => $therow[2],
                'descripcion2' => $therow[3],
                'notas' => $therow[4],
                'numero_documento' => $therow[5],
                'numero_cheque' => $therow[6],
                'fecha_elaboracion' => HelpExcel::getFechaExcel($therow[7]),
                'consecutivo' => $therow[8],
                'codigo_cuenta' => $therow[9],
                'nombre_cuenta' => $therow[10],
                'ccostos' => $therow[11],
                'nit' => $therow[12],
                'nombre' => $therow[13],
                'valor_debito' => doubleval($therow[14]),
                'valor_credito' => doubleval($therow[15]),
                'codigo_asiento' => $therow[16],
                'documento_ref' => $therow[17],
                'plan_cuentas' => $therow[18],
            ]);


            return $compro;
        } catch (\Throwable $th) {
            Log::error('Error al procesar una fila en ComprobanteImport', ['error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'row0' => $therow[0],
                'row1' => $therow[1],
            ]);
            // Aquí puedes decidir cómo manejar el error en la fila, como saltarla, enviar una notificación, etc.
            throw $th;
        }
    }

}

