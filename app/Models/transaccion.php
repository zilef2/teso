<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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
        'n_contrapartidas',
        'contrapartida_CI',
        'concepto_flujo_homologación',
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

    ];

//    public function user(){return $this->belongsTo(User::class);}
//     public function formularios(){return $this->hasMany(Formulario::class);}
     public function cuenta(){return $this->hasOne(cuenta::class);}

//     public function contraparte(){return $this->hasMany(contraparte::class);}

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('transaccions_search_*');
        });

        static::deleted(function () {
            Cache::forget('transaccions_search_*');
        });
    }
}
