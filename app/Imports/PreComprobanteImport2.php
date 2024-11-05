<?php

namespace App\Imports;

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

class PreComprobanteImport2 implements ToCollection, WithStartRow
//    , WithMapping
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

    //<editor-fold desc="Hasta model function">

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
        $this->contarVaciosstring = "";
        $this->MensajeMortal = '';
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int{
        return 10000;
    }

//    public function map($row): array
//    {
//        Log::info('esto es una ' . $this->ContarFilasAbsolutas);
//        return array_slice($row, 0, 19);
//    }

    public function limit(): int
    {
        return 1;
    }

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

    /**
     * @param array $row
     *
     * @throws Exception
     */
    public function collection(Collection $rows): void
    {
        try {
            Log::info('Inicio absoluto. Comprobantes');
            $totalRows = count($rows);
            $this->ContarFilasAbsolutas = $totalRows;
            Log::info("Iniciando procesamiento de $totalRows filas");
            if ($this->SoloUnaVez === 0) {
                [$cuantaVeces, $mesYanio] = $this->HaSidoGuardadoAnteriormente($rows[0]);
                if ($cuantaVeces > 0) {
                    $mensajesito = '|Comprobantes ya cargados del mes: ' . $mesYanio;
                    $this->MensajeMortal = $mensajesito;
                    throw new \Exception($mensajesito);
                }
            }
            Log::info('Los comprobantes pasaron la 1 validacion');
            foreach ($rows as $row) {
                $this->ContarFilasAbsolutas++;
                $this->Requeridos($row); //this has dd function
                Log::info('validacion '.$this->ContarFilasAbsolutas);
                if($this->ContarFilasAbsolutas === 3) break;
//            if(strcmp($row[0],'AN') === 0){throw new \Exception('|Comprobantes de AN no son aceptados');}
            }
            Log::info('validacion de comprobantes finalizada');
        } catch (ValidationException $e) {
            Log::error('Error en la importación: ' . $e->getMessage() . ' En la linea: ' . $e->getLine());
            throw new \Exception($e->getMessage());
        } catch (\Throwable $th) {
            $mensajeError = '  ' . $th->getMessage() . '. Informar al desarrollador - L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            ZilefLogs::EscribirEnLog($this, 'IMPORT:comprobante ', $mensajeError, false);
            throw new \Exception($mensajeError);
        }
    }

    /**
     * @throws \Exception
     */
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


}

