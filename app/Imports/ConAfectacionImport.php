<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\helpers\ZilefLogs;
use App\Models\ceconafectacion;
use App\Models\Comprobante;
use App\Models\transaccion;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\ImportFailed;
use Opcodes\LogViewer\Log;

class ConAfectacionImport implements ToModel, WithStartRow, ShouldQueue, WithChunkReading
{

    public int $ContarFilasAbsolutas;
    public int $ContarFilas;

    public int $contarVacios;
    public string $contarVaciosstring;

    private $SoloUnaVez = 0;



    //<editor-fold desc="contruc and privates">
    /**
     * @throws \Exception
     */
    function __construct()
    {
        //contares
        $this->ContarFilasAbsolutas = 1; //startRow = 2, por tanto se tiene que comenzar en 1
        $this->ContarFilas = 0;
        //errores
        $this->contarVacios = 0;
        $this->contarVaciosstring = "";
    }


    public function startRow(): int{
        return 2;
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    /**
     * @throws \Exception
     */
    public function Requeridos($theRow){
        /*
        'consecutivo',
        'no_op',
        'numero_cheque',
        'valor_egreso',
        'valor_total',
        'nombre',
        'numero_cuenta',
        'codigo_resumido',
        'nombre_proyecto',
        'nit',
        'nombre',
        'saldo_rubro',
        'rubro',
        'nombre_empresa',
        'nombre_dependencia',
        'fecha_elaboracion',
        'estado',
        'descripcion',
        */
        $columnasPermitidasVacias = [
//            10, //  nombre K
//            11, //  cod_costos L
//            12, //  desc_costos_codigo M
        ]; //max: 23 plan_cuentas

        foreach ($theRow as $key => $value) {
            if(in_array($key,$columnasPermitidasVacias)){
                continue;
            }
            if (is_null($value) || $value === ''){
                //todo: al final, avisar que filas tubieron vacios, pero no frenar el proceso por ello
                throw new Exception('VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
//                return -1;
//                dd($theRow,$value,'VALOR VACIO EN LA FILA '.$this->ContarFilasAbsolutas);
            }
        }

        if (!is_numeric(intval($theRow[0]))){ //intval
            $mensajesito = 'TIPO DE VALOR INCORRECTO (deberia ser un numbero) EN LA FILA ';
            dd('No es numero',
                $mensajesito.$this->ContarFilasAbsolutas,
                $theRow,$theRow[0]);
//                throw new Exception($mensajesito.$this->ContarFilasAbsolutas);
        }

        if (!is_numeric($theRow[1])){
            dd($theRow,$theRow[1],'TIPO DE VALOR INCORRECTO (deberia ser un numbero) EN LA FILA '.$this->ContarFilasAbsolutas);
        }
//        if (!is_string($theRow[2])){
//            dd($theRow,$theRow[2],'TIPO DE VALOR INCORRECTO (deberia ser un texto) EN LA FILA '.$this->ContarFilasAbsolutas);
//        }
        return true;
    }
    //</editor-fold>

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row){
        $this->ContarFilasAbsolutas++;
        try {
            if($this->Requeridos($row) === -1) {
                throw new \Exception('|Error en filas del excel');
            }

            if($this->SoloUnaVez === 0){
                [$cuantaVeces,$mesYanio] = $this->HaSidoGuardadoAnteriormente($row);
                if($cuantaVeces > 0){
                    throw new \Exception('|Comprobantes ya cargados del mes: '.$mesYanio);
                }
            }

            $result = $this->TheNewObject($row);
            dd(
                $result
            );
            $this->ContarFilas++;
            return $result;

        } catch (\Throwable $th) {
            $mensajeError = ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();


            ZilefLogs::EscribirEnLog($this, 'IMPORT:cuentas', $mensajeError, false);
            throw new \Exception('|Error en la fila '. $this->ContarFilasAbsolutas);
//            dd($mensajeError, 'fila ' . $this->ContarFilasAbsolutas);
        }
    }


    /**
     * @throws Exception
     */
    private function HaSidoGuardadoAnteriormente($therow)
    {
        $laFecha = HelpExcel::getFechaExcel($therow[15]); //fecha_elaboracion
        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $anio = $laFecha->format('Y'); // Obtiene el año

        $ExisteUnComprobante = ceconafectacion::
            Where('consecutivo',$therow[0])
            ->WhereYear('fecha_elaboracion',$anio)
            ->whereMonth('fecha_elaboracion',$mes)->count();

        $mesYanio = $mes . '-'. $anio;
        $this->SoloUnaVez++;
        return [$ExisteUnComprobante,$mesYanio];
    }

    private function TheNewObject($therow){

        return new ceconafectacion([
            'consecutivo' => $therow[0],
            'no_op' => $therow[1],
            'numero_cheque' => $therow[2],
            'valor_egreso' => $therow[3],
            'valor_total' => $therow[4],
            'nombre' => $therow[5],
            'numero_cuenta' => $therow[6],
            'codigo_resumido' => $therow[7],
            'nombre_proyecto' => $therow[8],
            'nit' => $therow[9],
            'nombre2' => $therow[10],
            'saldo_rubro' => $therow[11],
            'rubro' => $therow[12],
            'nombre_empresa' => $therow[13],
            'nombre_dependencia' => $therow[14],
            'fecha_elaboracion' => HelpExcel::getFechaExcel($therow[15]),
            'estado' => $therow[16],
            'descripcion' => $therow[17],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                Mail::raw('error', function ($message) {
                    $message->to('ajelof2+11@gmail.com')->subject('Fallo TransaccionesImport');
                });
                $exception = $event->getException();
                Log::error('Importación '. get_class($this).' fallida', [
                    'error' => $exception->getMessage(),
//                    'file' => $event->getFile(),
                    'stack' => $exception->getTraceAsString(),
                ]);
            },
        ];
    }

}
