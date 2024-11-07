<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asiento extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo_cuenta',
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
        'numerounico',
        'resultado_asiento',
    ];

}
