<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\ZilefLogs;
use App\Models\afectacion;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class afectacionImport implements ToModel, WithStartRow
{
    public int $ContarFilas;

    function __construct()
    {
        $this->ContarFilas = 0;
    }

    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     *
     * @return Model|null
     * @throws Exception
     */
    public function model(array $row): Model|afectacion
    {
        try {
            $this->ContarFilas++; //filas que se registraron en el aplicativo

            return new afectacion([
                'valor_debito' => $row[0],
                'valor_credito' => $row[1],
                'codigo_cuenta' => $row[2],
                'codigo_asiento' => $row[3],
                'tipo' => $row[4],
                'codigo' => $row[5],
                'fecha_elaboracion' => HelpExcel::getFechaExcel($row[6]),
                'consecutivo' => $row[7],
                'descripcion' => $row[8],
                'descripcion_concepto' => $row[9],
                'codigo_banco' => $row[10],
                'otros' => $row[11],
                'taquilla' => $row[12],
                'consecutivo2' => $row[13],
                'nombre_empresa' => $row[14],
                'nombre_dependencia' => $row[15],
            ]);
        } catch (\Throwable $th) {
            $mensajeError = (new \App\helpers\Myhelp)->mensajesErrorBD($th, 'AsientoImport', 0, '_');
            ZilefLogs::EscribirEnLog($this, 'IMPORT:asiento ', $mensajeError, false);
            throw new Exception($mensajeError);
        }
    }
}
/*
0 a 'valor_debito',
1 b 'valor_credito',
2 c 'codigo_cuenta',
3 d 'codigo_asiento',
4 e 'tipo',
5 f 'codigo',
6 g 'fecha_elaboracion',
7 h 'consecutivo',
8 i 'descripcion',
9 j 'descripcion_concepto',
10 k 'codigo_banco',
11 l 'otros',
12 m 'taquilla',
13 n 'consecutivo2',
14 o 'nombre_empresa',
15 p 'nombre_dependencia',
16 q  nada
*/
