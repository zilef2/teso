<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\ZilefLogs;
use App\Models\cesinafectacion;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PreAfectacionImport implements ToCollection, WithStartRow
//, WithHeadingRow
{
    public int $ContarFilasAbsolutas;
    public string $nombrePropio;
    public int $SoloUnaVez;
    public int $contarVacios;

    function __construct()
    {
        $this->nombrePropio = 'Comprobantes sin afectacion';
        $this->ContarFilasAbsolutas = 1; // filas del excel
        $this->SoloUnaVez = 0;
        $this->contarVacios = 0;
    }

    public function startRow(): int{return 2;}
    /**
     * @throws \Exception
     */
//    public function model(array $row): Model|afectacion|null
    public function collection(Collection $rows)
    {
        if ($this->SoloUnaVez === 0) {
            [$cuantaVeces, $mesYanio] = $this->HaSidoGuardadoAnteriormente($rows[0]);
            if ($cuantaVeces > 0) {
                throw new \Exception('|' . $this->nombrePropio . ' ya han sido cargados del mes: ' . $mesYanio);
            }
            //solo se valida el 1 y el ultimo
            [$cuantaVeces, $mesYanio] = $this->HaSidoGuardadoAnteriormente($rows->last());
            if ($cuantaVeces > 0) {
                throw new \Exception('|' . $this->nombrePropio . ' ya han sido cargados del mes: ' . $mesYanio);
            }
        }
        foreach ($rows as $row)
        {
        try {
            $this->ContarFilasAbsolutas++;
            if ($this->validarNull($row)) return null;
            $this->Requeridos($row); //this has a return only for happy path, cause  dd function


            return null;
        } catch (\Throwable $th) {
            $mensajeError = (new \App\helpers\Myhelp)->mensajesErrorBD($th, 'AsientoImport', 0, '_');
            ZilefLogs::EscribirEnLog($this, 'IMPORT:comprobante', $mensajeError, false);
            throw new \Exception($mensajeError);
        }
        }
    }

    public function Requeridos($theRow): bool
    {
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
        16 q */

        $columnasPermitidasVacias = [
            8,10,12
        ];
        foreach ($theRow as $key => $value) {
            if (in_array($key, $columnasPermitidasVacias)) {
                continue;
            }
            if (is_null($value) || $value === '') {
                throw new \Exception('VALOR VACIO EN LA FILA: ' . $this->ContarFilasAbsolutas. ' en la columna: ' . $key);
            }
        }
        if (!is_numeric(($theRow[0]))) { //intval
            $mensajesito = 'TIPO DE VALOR INCORRECTO (deberia ser un numerico) EN LA FILA ' . $this->ContarFilasAbsolutas. ' columna A';
            dd($theRow, $theRow[0], $mensajesito);
        }

        if (!is_numeric($theRow[1])) {
            dd($theRow, $theRow[1], 'TIPO DE VALOR INCORRECTO (deberia ser un numerico) EN LA FILA ' . $this->ContarFilasAbsolutas . ' columna B');
        }
        return true;
    }

    private function validarNull($row): bool
    {
        session(['larow' => $row]);
        return (
            !isset($row[0])
            || mb_strtolower($row[0]) == 'codigo_cuenta'
        );
    }

    private function HaSidoGuardadoAnteriormente($therow): array
    {
        $laFecha = HelpExcel::getFechaExcel($therow[6]); //la fecha
        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $anio = $laFecha->format('Y'); // Obtiene el año

        $ExisteUnComprobante = cesinafectacion::Where('codigo', $therow[0])
            ->WhereYear('fecha_elaboracion', $anio)
            ->whereMonth('fecha_elaboracion', $mes)->count();

        $mesYanio = $mes . '-' . $anio;
        $this->SoloUnaVez++;
        return [$ExisteUnComprobante, $mesYanio];
    }
}
