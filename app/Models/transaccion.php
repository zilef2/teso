<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class transaccion extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
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
        'transaccions',
    ];
}
