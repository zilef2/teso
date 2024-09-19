<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\cuenta;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class PersonalImport implements ToModel
{

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $contarVacios;
    public string $contarVaciosstring;
    public int $numero_necesidad;
    public int $contarIncongruencias;
    public int $contarUsuariosInexistentes;

    public $usuario;
    private array $vectorCategoriaInsensitive;
    private array $vectorMlistaprosInsensitive;
    private array $vectorMplanInsensitive;
    private array $vectorMlineaInsensitive;


    /**
     * @throws \Exception
     */
    function __construct(){
//        if ($valor < 0) {
//            throw new \Exception("El valor no puede ser negativo.");
//        }

        $this->numero_necesidad = 0;

        //contares
        $this->ContarFilasAbsolutas = 0;
        $this->ContarFilas = 0;

        //errores
        $this->contarVacios = 0; $this->contarVaciosstring = "";
        $this->contarIncongruencias = 0; $this->contarIncongruenciasstring = "";
        $this->contarUsuariosInexistentes = 0; $this->contarUsuariosInexistentesstring = "";
        $this->contarTotalIncongruente = 0; $this->contarTotalIncongruentestring = "";


        //selecs
//        $this->Uproceso_que_solicita_presupuesto = HelpExcel::Uproceso_que_solicita_presupuesto();
//        $this->Ecategoria = HelpExcel::Ecategoria();
//
//        $this->Mlista_pros_presupuestp = HelpExcel::Mlista_pros_presupuestp();
//        $this->Mplanmejoramientonecesidad = HelpExcel::Mplanmejoramientonecesidad();
//        $this->Mlineadelplan = HelpExcel::Mlineadelplan();

        //array mb_strtolower
//        $this->vectorCategoriaInsensitive = array_map('mb_strtolower', $this->Ecategoria);
//        $this->vectorUprocesoInsensitive = array_map('mb_strtolower', $this->Uproceso_que_solicita_presupuesto);
//        $this->vectorMlistaprosInsensitive = array_map('mb_strtolower', $this->Mlista_pros_presupuestp);
//        $this->vectorMplanInsensitive = array_map('mb_strtolower', $this->Mplanmejoramientonecesidad);
//        $this->vectorMlineaInsensitive = array_map('mb_strtolower', $this->Mlineadelplan);

    }

    private function validarNull($row){
        session(['larow' => $row]);
        return (
            !isset($row[0])
            || mb_strtolower($row[0]) == 'codigo cuenta contable'
            || mb_strtolower($row[1]) == 'numero cuenta bancaria'
            || mb_strtolower($row[2]) == 'banco'
            || mb_strtolower($row[3]) == 'tipo de recurso'
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
            //region Description
            if($this->validarNull($row)) return null;

            //#: post validaciones
            $row[12] = $row[12] ?? 0;
            $vigAnterior = intval($row[12]);

//            if (!$this->Requeridos($row)) {
//                ++$this->contarVacios;
//                $this->contarVaciosstring .= $row[0] . '__' . $row[1] . '___' . $row[2].':: ';
//                return null;
//            }

            //# fin validaciones




            /*
                'codigo_cuenta_contable', 1
                'numero_cuenta_bancaria', 2
                'banco', 3
                'tipo_de_recurso', 4
             */

            //#: validar que no este repetido
//            $cedula = intval($row[0]);
//            if(!isset($this->usuario)){
//                $this->usuario = User::where('identificacion', $cedula)->first();
//
//                if(!$this->usuario){
//                    $this->contarUsuariosInexistentes++;
//                    dd($row, mb_strtolower($identi),' registro inexistente. Fila ' . $this->ContarFilasAbsolutas . '<br><br> ');
//                    return null;
//                }
//                $this->Ultimocuenta = cuenta::where('user_id', $this->usuario->id)->orderBy('numero_necesidad', 'desc')->first();
//                $this->TodosFormDelUser = cuenta::where('user_id', $this->usuario->id)->get();
//
//                if($this->Ultimocuenta)
//                    $this->numero_necesidad = intval($this->Ultimocuenta->numero_necesidad);
//            }

//            $row2 = str_replace("\n", "", $row[2]);
//            foreach ($this->TodosFormDelUser as $index => $item) {
//                if (strcmp($item->necesidad, $row2) === 0) {
//                    $this->contarIncongruencias++;
//                    $this->contarIncongruenciasstring .= 'Necesidades repetidas: '. $row2.' Fila ' . $this->ContarFilasAbsolutas . '<br><br> ';
//                    dd('Necesidades repetidas', $row2,'fila '.$this->ContarFilasAbsolutas);
//                    return null;
//                }
//            }

            //fin mas Validaciones
            //endregion

            //transformaciones
            //ucfirst |
            $procesoSolicita = (trim($row[1]));

//            $laListaproceso3 = $this->laLista3($row);
//            if($laListaproceso3 != "0" && !$laListaproceso3){
//                dd("3a",$laListaproceso3,$row,'fila '.$this->ContarFilasAbsolutas);
//                return null;
//            }
            //fin transformaciones


            $this->numero_necesidad++;
            $cuenta = new cuenta([
                'codigo_cuenta_contable' => $row[0],
                'numero_cuenta_bancaria' => $row[1],
                'banco' => $row[2],
                'tipo_de_recurso' => $row[3],
            ]);
            $this->ContarFilas++;

            return $cuenta;

        } catch (\Throwable $th) {
            $mensajeError = ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'IMPORT:cuentas', $mensajeError, false);
            dd($mensajeError,'fila '.$this->ContarFilasAbsolutas);
        }
    }

    public function Requeridos($theRow)
    {
        foreach ($theRow as $value) {
            if (is_null($value) || $value === ''){
                dd($theRow,$value,'VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
                return false;
            }
        }
        if (!is_int(intval($theRow[0]))){
            dd($theRow[0],'TIPO DE VALOR INCORRECTO (deberia ser un entero) EN LA FILA '.$this->ContarFilasAbsolutas);
            return false;
        }

        if (!is_string($theRow[1])){
            dd($theRow[1],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
            return false;
        }
        if (!is_string($theRow[2])){
            dd($theRow[2],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
            return false;
        }

        return true;
    }

    public function EncontroFalso($ElArray, $variableContar): bool
    {
        if ($ElArray !== 0 && !$ElArray) {
            $this->{$variableContar}++;
            return true;
        }
        return false;
    }

}
