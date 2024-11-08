<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class concepto_flujo extends Model
{
    use HasFactory;
    protected $fillable = [
        'cuenta_contable',
        'concepto_flujo',
        'categoria',
        'ingresos_o_egresos'
    ];
}
