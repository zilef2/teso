<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class afectacion extends Model
{
    use HasFactory;
    protected $fillable = [
        'valor_debito',
        'valor_credito',
        'codigo_cuenta',
        'codigo_asiento',
        'tipo',
        'codigo',
        'fecha_elaboracion',
        'consecutivo',
        'descripcion',
        'descripcion_concepto',
        'codigo_banco',
        'otros',
        'taquilla',
        'consecutivo',
        'nombre_empresa',
        'nombre_dependencia',
    ];
}
