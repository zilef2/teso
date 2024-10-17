<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Comprobante;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ComprobanteImport implements ToModel, WithStartRow
{

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $SoloUnaVez = 0;

    protected array $DebenSerNulos;

    //manejo de errores
    public int $contarVacios;
    public string $contarVaciosstring;
    public string $nombrePropio;


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
        $this->ContarFilasAbsolutas = 0;
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0;
        $this->contarVaciosstring = "";
        $this->DebenSerNulos = [
            'codigo',
            'descripcion',
            'comprobante',
            'notas',
            'numero_documento',
            'numero_cheque',
            'fecha_elaboracion',
            'consecutivo',
            'codigo_cuenta',
            'nombre_cuenta',
            'ccostos',
            'nit',
            'nombre',
            'valor_debito',
            'valor_credito',
            'codigo_asiento',
            'documento_ref',
            'plan_cuentas',
        ];
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
            || strcmp(mb_strtolower($row[0]), 'codigo') === 0
            || strcmp(mb_strtolower($row[0]), 'descripcion') === 0
        );
    }//esta mal

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
            if(in_array($key,$columnasPermitidasVacias)){
                continue;
            }
            if (is_null($value) || $value === ''){
                dd($theRow,$value,'VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
                throw new \Exception('VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
            }
        }
        if (!is_string(($theRow[0]))){ //intval
            $mensajesito = 'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA ';
            dd($theRow,$theRow[0],$mensajesito.$this->ContarFilasAbsolutas);
//                throw new Exception($mensajesito.$this->ContarFilasAbsolutas);
        }

        if (!is_string($theRow[1])){
            dd($theRow,$theRow[1],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
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
     */
    public function model(array $row)
    {
        try {
            $this->ContarFilasAbsolutas++;
//            if ($this->validarNull($row)) return null;
            $this->Requeridos($row); //this has dd function

            if($this->SoloUnaVez === 0){
                [$cuantaVeces,$mesYanio] = $this->HaSidoGuardadoAnteriormente($row);
                if($cuantaVeces > 0){
                    throw new \Exception('|Comprobantes ya cargados del mes: '.$mesYanio);
                }
            }
            if(strcmp($row[0],'AN') === 0){
                throw new \Exception('|Comprobantes de AN no son aceptados');
            }

            return $this->TheNewObject($row);
        }catch (\Throwable $th) {
            if(str_starts_with($th->getMessage(),'|')) {
                throw new \Exception($th->getMessage());
            }else{
                $mensajeError = '  ' . $th->getMessage() . '. Informar al desarrollador - L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
                Myhelp::EscribirEnLog($this, 'IMPORT:comprobante', $mensajeError, false);
                throw new \Exception($mensajeError);
            }
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

        $ExisteUnComprobante = Comprobante::Where('codigo',$therow[0])
            ->WhereYear('fecha_elaboracion',$anio)
            ->whereMonth('fecha_elaboracion',$mes)->count();

        $mesYanio = $mes . '-'. $anio;
        $this->SoloUnaVez++;
        return [$ExisteUnComprobante,$mesYanio];
    }

    private function TheNewObject($therow)
    {
        return new Comprobante([
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

    }


}

