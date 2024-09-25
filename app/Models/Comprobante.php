<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo',
        'descripcion',
        'comprobante',
        'descripcion2',
        'notas',
        'numero_documento',
        'numero_cheque',
        'fecha_elaboracion',
        'consecutivo',
        'codigo_cuenta',
        'nombre_cuenta',
        'ccostos',
        'nit',
        'nombre',
        'valor_debito',
        'valor_credito',
        'codigo_asiento',
        'documento_ref',
        'plan_cuentas',
    ];

}
