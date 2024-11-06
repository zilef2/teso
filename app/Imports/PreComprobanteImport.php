<?php

namespace App\Imports;

use AllowDynamicProperties;
use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\helpers\ZilefLogs;
use App\Models\Comprobante;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Validators\ValidationException;

#[AllowDynamicProperties] class PreComprobanteImport implements ToCollection, WithStartRow
    , WithMapping
    , WithChunkReading
{

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $SoloUnaVez = 0;

    protected array $DebenSerNulos;

    //manejo de errores
    public int $contarVacios;
    public string $contarVaciosstring;
    public string $nombrePropio;
    public string $MensajeMortal;
    public mixed $currentRow;
    public mixed $chunkSize;


    //<editor-fold desc="Hasta model function">
    private int $pruebaMap;

    /**
     * @throws \Exception
     */
    function __construct()
    {
        $this->nombrePropio = 'comprobante';
        //contares
        $this->ContarFilasAbsolutas = 1;
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0;
        $this->currentRow = 0;
        $this->pruebaMap = 0;
        $this->chunkSize = 1500;
        $this->contarVaciosstring = "";
        $this->MensajeMortal = '';

    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int{
        return $this->chunkSize;
    }

    public function map($row): array
    {
        $this->pruebaMap++;
        if ($this->pruebaMap % 100 === 0) {
            Log::info('Prueba mapeo = ' . $this->pruebaMap);
        }
        return array_slice($row, 0, 19);
    }

    public function limit(): int{return 1;}

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
            if(is_null($value))continue;
            if ($value === '') {
                $mensajesito =  "\n $value || VALOR VACIO EN LA FILA  $this->ContarFilasAbsolutas  columna  $key";
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
    //</editor-fold>


    private function HaSidoGuardadoAnteriormente($therow)
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

    public function collection(Collection $rows)
    {
        try {
            $totalRows = count($rows);

            $this->ContarFilasAbsolutas = $totalRows;
            Log::info("Iniciando procesamiento de $totalRows filas");

            $this->HaSidoGuardadoAnteriormente($rows[0]);
            if($rows[1]) $this->HaSidoGuardadoAnteriormente($rows[1]);
            $this->ConProblemas = 1;
            foreach ($rows->chunk($this->chunkSize) as $index => $chunk) {

                $chunkStart = $index * $this->chunkSize;
                $memoryBefore = memory_get_usage(true);

                Log::info("Procesando chunk " . ($index + 1), [
                    'rows' => "($chunkStart - " . ($chunkStart + count($chunk)) . ") de $totalRows",
                    'memory' => $this->formatBytes($memoryBefore)
                ]);

                foreach ($chunk as $row) {
                    $this->currentRow++;

                    // Verificaciones de memoria cada 100 filas
                    if ($this->currentRow % 100 == 0) {
                        $this->checkMemory();
                    }

//                    $this->procesarFila($row);
                }

                $memoryAfter = memory_get_usage(true);
                $memoryDiff = $memoryAfter - $memoryBefore;

                Log::info("Chunk " . ($index + 1) . " completado", [
                    'memory_used' => $this->formatBytes($memoryDiff),
                    'memory_total' => $this->formatBytes($memoryAfter)
                ]);

                // Limpiar memoria después de cada chunk
                gc_collect_cycles();
            }
            $this->ConProblemas = 0;

        } catch (\Throwable $th) {
            $this->MensajeMortal = "Error en fila {$this->currentRow}: " . $th->getMessage();
            Log::error($this->MensajeMortal, [
                'current_row' => $this->currentRow,
                'memory' => $this->formatBytes(memory_get_usage(true)),
                'peak_memory' => $this->formatBytes(memory_get_peak_usage(true))
            ]);
            throw $th;
        }
    }

    private function checkMemory()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPercentage = ($memoryUsage / $memoryLimit) * 100;

        if ($memoryPercentage > 80) {
            Log::warning("Alto uso de memoria detectado", [
                'row' => $this->currentRow,
                'memory_usage' => $this->formatBytes($memoryUsage),
                'memory_percentage' => round($memoryPercentage, 2) . '%'
            ]);
        }

        // Si estamos cerca del límite, forzar recolección de basura
        if ($memoryPercentage > 90) {
            gc_collect_cycles();
        }
    }

    private function procesarFila($row)
    {
        try {
            // Tu lógica actual de procesamiento
            // ...

        } catch (\Throwable $th) {
            $this->MensajeMortal = "Error procesando fila {$this->currentRow}: " . $th->getMessage();
            Log::error($this->MensajeMortal, [
                'row_data' => $row->toArray(),
                'memory' => $this->formatBytes(memory_get_usage(true))
            ]);
            throw $th;
        }
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }

    private function parseMemoryLimit($memoryLimit)
    {
        if ($memoryLimit === '-1') {
            return PHP_INT_MAX;
        }

        preg_match('/^(\d+)(.)$/', $memoryLimit, $matches);
        if (!$matches) {
            return (int)$memoryLimit;
        }

        $value = (int)$matches[1];
        switch (strtoupper($matches[2])) {
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024;
            case 'K':
                $value *= 1024;
        }

        return $value;
    }

}

