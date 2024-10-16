<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class ZipController extends Controller
{

    protected $signature = 'send:zip';

    public function DescompresionDespliegue($esAmbientePruebas = 1): string
    {
        try{
            $nombreApp = 'teso';
            if (extension_loaded('zip')) {
                $extensionActiva = 'Zip extension is loaded. Version: ' . phpversion('zip');

                if($esAmbientePruebas){
                    $extractTo = '/home/wwecno/pruebas2';
                    $mensajeAmbiente = ' <br><b>pruebas2 Ambiente de pruebas</b>';
                }else{
                    $extractTo = '/home/wwecno/pruebas2';
                    $mensajeAmbiente = ' <br><b>pruebas2 Ambiente de produccion</b>';
                }
                $zipFile = $extractTo.'/'.$nombreApp.'.zip';

                $zip = new ZipArchive;
                if ($zip->open($zipFile) === TRUE) {
                    $zip->extractTo($extractTo);
                    $zip->close();
                    return $extensionActiva. ' <br>Descompresión super exitosa<br>'.$mensajeAmbiente;
                } else {
                    return $extensionActiva. ' Falló apertura del archivo zip';
                }
            } else {
                return '<h1>Zip extension no esta cargada.</h1>';
            }
        } catch (\Throwable $th) {
            $mensan = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            dd($mensan);
        }
    }
}
