<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ceconafectacion extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'nombre2',
        'saldo_rubro',
        'rubro',
        'nombre_empresa',
        'nombre_dependencia',
        'fecha_elaboracion',
        'estado',
        'descripcion',
    ];

}
