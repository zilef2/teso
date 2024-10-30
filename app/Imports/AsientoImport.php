<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\ZilefLogs;
use App\Models\asiento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AsientoImport implements ToModel,WithStartRow, WithChunkReading
{

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $SoloUnaVez = 0;

    //manejo de errores
    public int $contarVacios;
    public string $contarVaciosstring;
    public string $nombrePropio;


    /**
     * @throws \Exception
     */
    function __construct()
    {
        $this->nombrePropio = 'asiento';
        //contares
        $this->ContarFilasAbsolutas = 1; // eso mismo
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0;
        $this->contarVaciosstring = "";
    }


    public function startRow(): int{return 2;}
    public function chunkSize(): int{
        return 1000;
    }

    public function model(array $row)
    {

        try {
            $this->ContarFilasAbsolutas++;

            if ($this->validarNull($row)) return null;
            $this->Requeridos($row); //this has dd function

            if($this->SoloUnaVez === 0){
                [$cuantaVeces,$mesYanio] = $this->HaSidoGuardadoAnteriormente($row);
                if($cuantaVeces > 0){
                    throw new \Exception('|'.$this->nombrePropio.'s ya cargados del mes: '.$mesYanio);
                }
            }

            $result = $this->TheNewObject($row);
            $this->ContarFilas++;
            return $result;
        } catch (\Throwable $th) {
            $mensajeError = (new \App\helpers\Myhelp)->mensajesErrorBD($th, 'AsientoImport', 0, '_');
            ZilefLogs::EscribirEnLog($this, 'IMPORT:comprobante', $mensajeError, false);

            if (str_starts_with($th->getMessage(), '|')) {
                throw new \Exception(
                    $th->getMessage()
                );
            } else {
                throw new \Exception(
                    $mensajeError
                );
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function Requeridos($theRow)
    {
        /*
        0 a codigo_cuenta
        1 b nombre_cuenta
        2 c codigo
        3 d documento
        4 e fecha_elaboracion
        5 f descripcion
        6 g comprobante
        7 h valor_debito
        8 i valor_credito
        9 j nit
        10 k nombre
        11 l cod_costos
        12 m desc_costos
        13 n codigo_interno_cuenta
        14 o codigo_tercero
        15 p ccostos
        16 q saldo_inicial
        17 r saldo_final
        18 s nombre_empresa
        19 t nit_empresa
        20 u documento_ref
        21 v consecutivo
        22 w periodo
        23 x plan_cuentas
        */

        $columnasPermitidasVacias = [
            6,7,
            10,11,12
        ];
        foreach ($theRow as $key => $value) {
            if (in_array($key, $columnasPermitidasVacias)) {
                continue;
            }
            if (is_null($value) || $value === '') {
//                dd($theRow,$value,'VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
                throw new \Exception('VALOR VACIO EN LA FILA: ' . $this->ContarFilasAbsolutas, ' en la columna: '.$key);

//                return false;
            }
        }
        if (!is_string(($theRow[0]))) { //intval
            $mensajesito = 'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA ';
            dd($theRow, $theRow[0], $mensajesito . $this->ContarFilasAbsolutas);
//                throw new Exception($mensajesito.$this->ContarFilasAbsolutas);
//            return false;
        }

        if (!is_string($theRow[1])) {
            dd($theRow, $theRow[1], 'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA ' . $this->ContarFilasAbsolutas);
//            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    private function TheNewObject($therow)
    {
        return new asiento([
            'codigo_cuenta'=> $therow[0],
            'nombre_cuenta'=> $therow[1],
            'codigo'=> $therow[2],
            'documento'=> $therow[3],
            'fecha_elaboracion'=> $therow[4],
            'descripcion'=> $therow[5],
            'comprobante'=> $therow[6],
            'valor_debito'=> $therow[7],
            'valor_credito'=> $therow[8],
            'nit'=> $therow[9],
            'nombre'=> $therow[10],
            'cod_costos'=> $therow[11],
            'desc_costos'=> $therow[12],
            'codigo_interno_cuenta'=> $therow[13],
            'codigo_tercero'=> $therow[14],
            'ccostos'=> $therow[15],
            'saldo_inicial'=> $therow[16],
            'saldo_final'=> $therow[17],
            'nombre_empresa'=> $therow[18],
            'nit_empresa'=> $therow[19],
            'documento_ref'=> $therow[20],
            'consecutivo'=> $therow[21],
            'periodo'=> $therow[22],
            'plan_cuentas'=> $therow[23],
        ]);
    }

    private function validarNull($row)
    {
        session(['larow' => $row]);
        return (
            !isset($row[0])
            || mb_strtolower($row[0]) == 'codigo_cuenta'
        );
    }

    private function HaSidoGuardadoAnteriormente($therow)
    {
        $laFecha = HelpExcel::getFechaExcel($therow[4]); //la fecha
        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $anio = $laFecha->format('Y'); // Obtiene el año

        $ExisteUnComprobante = asiento::Where('codigo', $therow[0])
            ->WhereYear('fecha_elaboracion', $anio)
            ->whereMonth('fecha_elaboracion', $mes)->count();

        $mesYanio = $mes . '-' . $anio;
        $this->SoloUnaVez++;
        return [$ExisteUnComprobante, $mesYanio];
    }
}

