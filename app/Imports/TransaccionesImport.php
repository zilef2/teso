<?php

namespace App\Imports;

use App\helpers\Myhelp;
use App\Models\transaccion;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TransaccionesImport implements ToModel, WithStartRow
{

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $contarVacios;
    public string $contarVaciosstring;

    protected $DebenSerNulos;
    private array $vectorCategoriaInsensitive;
    private array $vectorMlistaprosInsensitive;
    private array $vectorMplanInsensitive;
    private array $vectorMlineaInsensitive;
    private string $contarTotalIncongruentestring;
    private int $contarTotalIncongruente;


    /**
     * @throws \Exception
     */
    function __construct()
    {
//        if ($valor < 0) {
//            throw new \Exception("El valor no puede ser negativo.");
//        }

        //contares
        $this->ContarFilasAbsolutas = 0;
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0;
        $this->contarVaciosstring = "";
        $this->contarTotalIncongruente = 0;
        $this->contarTotalIncongruentestring = "";
        $this->DebenSerNulos = [
            'codigo_cuenta_contable',
            'nombre_cuenta',
            'codigo',
            'documento',
            'fecha_elaboracion',
            'descripcion',
            'comprobante',
            'valor_debito',
            'valor_credito',
            'nit',
            'nombre',
            'cod_costos',
            'desc_costos',
            'codigo_interno_cuenta',
            'codigo_tercero',
            'ccostos',
            'saldo_inicial',
            'saldo_final',
            'nombre_empresa',
            'nit_empresa',
            'documento_ref',
            'consecutivo',
            'periodo',
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
            || mb_strtolower($row[0]) == 'codigo_cuenta_contable'
            || mb_strtolower($row[0]) == 'nombre_cuenta'
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
            
            return $this->TheNewObject($row);
        } catch (\Throwable $th) {
            $mensajeError = ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'IMPORT:cuentas', $mensajeError, false);
            dd($mensajeError, 'fila ' . $this->ContarFilasAbsolutas);
        }
    }

    /**
     * @throws \Exception
     */
    public function Requeridos($theRow)
    {
        $this->ContarFilasAbsolutas++;
        $columnasPermitidasVacias = [
          11,12,13  
        ];
        foreach ($theRow as $key => $value) {
            if(in_array($key,$columnasPermitidasVacias)){
                continue;
            }
            if (is_null($value) || $value === ''){
//                dd($theRow,$value,'VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
                throw new Exception('VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
                
//                return false;
            }
        }
        if (!is_string(($theRow[0]))){ //intval
            $mensajesito = 'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA ';
            dd($theRow,$theRow[0],$mensajesito.$this->ContarFilasAbsolutas);
//                throw new Exception($mensajesito.$this->ContarFilasAbsolutas);
//            return false;
        }

        if (!is_string($theRow[1])){
            dd($theRow,$theRow[1],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
//            return false;
        }
//        if (!is_string($theRow[2])){
//            dd($theRow,$theRow[2],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
//        }

        return true;
    }

    private function TheNewObject($therow)
    {
        return new transaccion([
        'codigo_cuenta_contable' => $therow[0], 
        'nombre_cuenta' => $therow[1], 
        'codigo' => $therow[2], 
        'documento' => $therow[3], 
        'fecha_elaboracion' => $therow[4], 
        'descripcion' => $therow[5], 
        'comprobante' => $therow[6], 
        'valor_debito' => $therow[7], 
        'valor_credito' => $therow[8], 
        'nit' => $therow[9], 
        'nombre' => $therow[10], 
        'cod_costos' => $therow[11], 
        'desc_costos' => $therow[12], 
        'codigo_interno_cuenta' => $therow[13], 
        'codigo_tercero' => $therow[14], 
        'ccostos' => $therow[15], 
        'saldo_inicial' => $therow[16], 
        'saldo_final' => $therow[17], 
        'nombre_empresa' => $therow[18], 
        'nit_empresa' => $therow[19], 
        'documento_ref' => $therow[20], 
        'consecutivo' => $therow[21], 
        'periodo' => $therow[22], 
        'plan_cuentas' => $therow[23], 
        ]);
    }
}
