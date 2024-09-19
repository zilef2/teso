<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Formulario;
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
    public $UltimoFormulario;
    public $Ecategoria;
    
    public $Uproceso_que_solicita_presupuesto;
    
    public $Mlista_pros_presupuestp;
    public $Mplanmejoramientonecesidad;
    public $Mlineadelplan;
    private $TodosFormDelUser;
    private array $vectorCategoriaInsensitive;
    private array $vectorUprocesoInsensitive;
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
        $this->Uproceso_que_solicita_presupuesto = HelpExcel::Uproceso_que_solicita_presupuesto();
        $this->Ecategoria = HelpExcel::Ecategoria();
        
        $this->Mlista_pros_presupuestp = HelpExcel::Mlista_pros_presupuestp();
        $this->Mplanmejoramientonecesidad = HelpExcel::Mplanmejoramientonecesidad();
        $this->Mlineadelplan = HelpExcel::Mlineadelplan();
        
        //array mb_strtolower
        $this->vectorCategoriaInsensitive = array_map('mb_strtolower', $this->Ecategoria);
        $this->vectorUprocesoInsensitive = array_map('mb_strtolower', $this->Uproceso_que_solicita_presupuesto);
        $this->vectorMlistaprosInsensitive = array_map('mb_strtolower', $this->Mlista_pros_presupuestp);
        $this->vectorMplanInsensitive = array_map('mb_strtolower', $this->Mplanmejoramientonecesidad);
        $this->vectorMlineaInsensitive = array_map('mb_strtolower', $this->Mlineadelplan);
        
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
            session(['larow' => $row]);
            $identi = $row[0];
            if (!isset($identi) || mb_strtolower($identi) == 'identificacion' || mb_strtolower($identi) == 'identificación' || mb_strtolower($identi) == 'cc') {
                return null;
            }
            
            //# validaciones
            $row[12] = $row[12] ?? 0;
            $vigAnterior = intval($row[12]);
            
            if (!$this->Requeridos($row)) {
                ++$this->contarVacios;
                $this->contarVaciosstring .= $row[0] . '__' . $row[1] . '___' . $row[2].':: ';
                return null;
            }

            //# fin validaciones
            
            /*
             * Identificacion -0 
             * Proceso que solicita presupuesto -1
             * Necesidad -2
             * Justificacion -3
             * Actividad -4
             * Categoria -5
             * Unidad de medida -6
             * Cantidad -7
             *  Valor unitario  -8
             *  Valor total solicitatdo por necesidad  -9
             * Periodo de inicio de ejecucion -10
             * Vigencias anteriores -11
             * Valor asignado en la vigencia anterior -12
             * Procesos involucrados -13
             * Plan de mejoramiento al que apunta la necesidad -14
             * Linea del plan desarrollo al que apunta la necesidad -15
             * Frecuencia de uso -16
             * Mantenimientos requeridos -17
             * 
             * Capacidad instalada -18
             * Riesgo de la inversion -19
             * Valor total de la solicitud actual -20
             */
            //valores numericos
            $cedula = intval($row[0]);
            if(!isset($this->usuario)){
                $this->usuario = User::where('identificacion', $cedula)->first();
                
                if(!$this->usuario){
                    $this->contarUsuariosInexistentes++;
                    dd($row, mb_strtolower($identi),'usuario inexistente. Fila ' . $this->ContarFilasAbsolutas . '<br><br> ');
                    return null;
                }
                $this->UltimoFormulario = Formulario::where('user_id', $this->usuario->id)->orderBy('numero_necesidad', 'desc')->first();
                $this->TodosFormDelUser = Formulario::where('user_id', $this->usuario->id)->get();

                if($this->UltimoFormulario)
                    $this->numero_necesidad = intval($this->UltimoFormulario->numero_necesidad);
            }
            //fin valores numericos

            //mas Validaciones
            $row2 = str_replace("\n", "", $row[2]);
            foreach ($this->TodosFormDelUser as $index => $item) {
                if (strcmp($item->necesidad, $row2) === 0) {
                    $this->contarIncongruencias++;
                    $this->contarIncongruenciasstring .= 'Necesidades repetidas: '. $row2.' Fila ' . $this->ContarFilasAbsolutas . '<br><br> ';
                    dd('Necesidades repetidas', $row2,'fila '.$this->ContarFilasAbsolutas);
                    return null;
                }
            }
            
            $intFactores = intval(intval($row[8] * $row[7])/10);
            $intTotal = intval(intval($row[9])/10);
            $esTotalValido = $intTotal == $intFactores;
            if(!$esTotalValido){
                $this->contarTotalIncongruente++;
                $this->contarTotalIncongruentestring = 'Total incorrecto. Fila ' . $this->ContarFilasAbsolutas.'<br><br> ';
                dd($intTotal*10 , $intFactores*10, $this->contarTotalIncongruentestring);
                return null;
            }
                
            //fin mas Validaciones
            //endregion
            
            //transformaciones
            //ucfirst |
            $procesoSolicita = (trim($row[1]));
            $ClaveProcesoSolicita = array_search(mb_strtolower($procesoSolicita), $this->vectorUprocesoInsensitive);
            if($this->EncontroFalso($ClaveProcesoSolicita,'contarTotalIncongruente')){
                $this->contarTotalIncongruentestring = 'Proceso no encontrado. Fila ' . $this->ContarFilasAbsolutas . '<br><br> ';
                dd($row, $this->contarTotalIncongruentestring, $ClaveProcesoSolicita, mb_strtolower($procesoSolicita), $this->vectorUprocesoInsensitive);
                return null;
            }
            

            
            $categoriaLista = ((trim($row[5])));
            $ClaveCategoria = array_search(mb_strtolower($categoriaLista), $this->vectorCategoriaInsensitive);
            if($ClaveCategoria === false) $categoria = $categoriaLista;
            else $categoria = $ClaveCategoria;

            $laListaproceso = $this->laLista1($row);
            if($laListaproceso != "0" && !$laListaproceso){
                dd("1a",$laListaproceso,'fila '.$this->ContarFilasAbsolutas);
                return null;
            }
            $laListaproceso2 = $this->laLista2($row);
            if($laListaproceso2 != "0" && !$laListaproceso2){
                dd("2a",$laListaproceso2, $laListaproceso != "0",'fila '.$this->ContarFilasAbsolutas);
                return null;
            }
            $laListaproceso3 = $this->laLista3($row);
            if($laListaproceso3 != "0" && !$laListaproceso3){
                dd("3a",$laListaproceso3,$row,'fila '.$this->ContarFilasAbsolutas);
                return null;
            }
            //fin transformaciones
            
            
//                $fechaNacimiento = HelpExcel::getFechaExcel($row[4])->format('Y-m-d H:i');
            $this->numero_necesidad++;
            $Formulario = new Formulario([
                'identificacion_user'  => $cedula,
                'numero_necesidad' => $this->numero_necesidad,
                'valor_total_de_la_solicitud_actual' => $row[20],
                'valor_total_asignado_en_vigencia_anterior' => 0,

                'proceso_que_solicita_presupuesto' => $ClaveProcesoSolicita,//[1]
                
                'necesidad' => $row2,//2 Párrafo
                'justificacion' => str_replace("\n", ". ",$row[3]), //3 Párrafo
                'actividad' => (trim($row[4])), //parrafo tamb
                'categoria' => $categoria,// sel unica con opcion de otras
                'unidad_de_medida' => ucfirst(mb_strtolower(trim($row[6]))),// sel unica
                'cantidad' => $row[7],
                'valor_unitario' => $row[8],//8 pesos
                'valor_total_solicitatdo_por_necesidad' => $row[9],// 8.5 automatico
                'periodo_de_inicio_de_ejecucion' => ucfirst(mb_strtolower(trim($row[10]))),//sel unica
                'vigencias_anteriores' => ucfirst(mb_strtolower(trim($row[11]))),//10 //sel unica
                'valor_asignado_en_la_vigencia_anterior' => $vigAnterior, //11 pesos
                
                'procesos_involucrados' => $laListaproceso,
                'plan_de_mejoramiento_al_que_apunta_la_necesidad' => $laListaproceso2,
                'linea_del_plan_desarrollo_al_que_apunta_la_necesidad' => $laListaproceso3,
                
                'frecuencia_de_uso' => ucfirst(mb_strtolower(trim($row[16]))),//sel unica
                'mantenimientos_requeridos' => ucfirst(mb_strtolower(trim($row[17]))),//sel unica
                'capacidad_instalada' => ucfirst(mb_strtolower(trim($row[18]))),//sel unica
                'riesgo_de_la_inversion' => ucfirst(mb_strtolower(trim($row[19]))),//sel unica
                'anexos' => '',
                'enviado' => 1,
                'user_id' => $this->usuario->id
            ]);
            $this->ContarFilas++;

            return $Formulario;
        } catch (\Throwable $th) {
            $mensajeError = ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'IMPORT:Formularios', $mensajeError, false);
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
    
    private function laLista1($row){
        $vectorProcesos = explode(" --", $row[13]);
        $vectorProcesoInvol = [];
        foreach ($vectorProcesos as $vectorProceso) {
            $procesoInvol = trim($vectorProceso);
            $temporal = array_search(mb_strtolower($procesoInvol), $this->vectorMlistaprosInsensitive);
            if ($this->EncontroFalso($temporal, 'contarTotalIncongruente')) return null; //contarProcesosInvolucrados
            $vectorProcesoInvol[] = $temporal;
        }
        return implode(',', $vectorProcesoInvol);
    }
    private function laLista2($row){
        $vectorProcesos = explode(" --", $row[14]);

        $vectorProcesoInvol = [];
        foreach ($vectorProcesos as $vectorProceso) {
            $procesoInvol = trim($vectorProceso);
            $temporal = array_search(mb_strtolower($procesoInvol), $this->vectorMplanInsensitive);
            if ($this->EncontroFalso($temporal, 'contarTotalIncongruente')){
                return null; //contarProcesosInvolucrados
            }
            $vectorProcesoInvol[] = $temporal;
        }
        return implode(',', $vectorProcesoInvol);
    }
    private function laLista3($row){
        $vectorProcesos = explode(" --", $row[15]);

        $vectorProcesoInvol = [];
        foreach ($vectorProcesos as $vectorProceso) {
            $procesoInvol = trim($vectorProceso);
            $temporal = array_search(mb_strtolower($procesoInvol), $this->vectorMlineaInsensitive);
            if ($this->EncontroFalso($temporal, 'contarTotalIncongruente')) return null; //contarProcesosInvolucrados
            $vectorProcesoInvol[] = $temporal;
        }
        return implode(',', $vectorProcesoInvol);
    }
}