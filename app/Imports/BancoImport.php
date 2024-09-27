<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Banco;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BancoImport implements ToModel,WithStartRow
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
        $this->nombrePropio = 'banco';
        //contares
        $this->ContarFilasAbsolutas = 1; // eso mismo
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0;
        $this->contarVaciosstring = "";
    }


    public function startRow(): int
    {
        return 2;
    }


    private function validarNull($row)
    {
        session(['larow' => $row]);
        return (
            !isset($row[0])
            || mb_strtolower($row[0]) == 'codigo'
        );
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $this->ContarFilasAbsolutas++;

            if ($this->validarNull($row)) return null;
            $this->Requeridos($row); //this has dd function

//            if($this->SoloUnaVez === 0){
//                [$cuantaVeces,$mesYanio] = $this->ValidarArchivoHaSidoGuardadoAnteriormente($row);
//                if($cuantaVeces > 0){
//                    throw new \Exception('|Comprobantes ya cargados del mes: '.$mesYanio);
//                }
//            }

            return $this->TheNewObject($row);
        } catch (\Throwable $th) {
            if (str_starts_with($th->getMessage(), '|')) {
                throw new \Exception(
                    $th->getMessage()
                );
            } else {
                $mensajeError = Myhelp::mensajesErrorBD($th, 'Bancoimport', 0, '_');
//                $mensajeError = '  ' . $th->getMessage() . '. Informar al desarrollador - L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
                Myhelp::EscribirEnLog($this, 'IMPORT:comprobante', $mensajeError, false);
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
        $columnasPermitidasVacias = [
            0
        ];
        foreach ($theRow as $key => $value) {
            if (in_array($key, $columnasPermitidasVacias)) {
                continue;
            }
            if (is_null($value) || $value === '') {
//                dd($theRow,$value,'VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
                throw new \Exception('VALOR VACIO EN LA FILA ' . $this->ContarFilasAbsolutas);

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
    private function TheNewObject($therow): banco
    {
        return new Banco([
            'codigo_cuenta_contable' => $therow[0],
            'numero_cuenta_bancaria' => $therow[1],
            'banco' => $therow[2],
            'tipo_de_cuenta' => $therow[3],
            'tipo_de_recursos' => $therow[4],
            'convenio' => $therow[5],
        ]);
    }

    
//    private function ValidarArchivoHaSidoGuardadoAnteriormente($therow)
//    {
//        $laFecha = HelpExcel::getFechaExcel($therow[7]);
//        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
//        $anio = $laFecha->format('Y'); // Obtiene el año
//
//        $ExisteUnComprobante = Banco::WhereYear('fecha_elaboracion',$anio)
//            ->whereMonth('fecha_elaboracion',$mes)->count();
//
//        $mesYanio = $mes . '-'. $anio;
//        $this->SoloUnaVez++;
//        return [$ExisteUnComprobante,$mesYanio];
//    }
}

