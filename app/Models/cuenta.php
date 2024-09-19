<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class cuenta extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'codigo_cuenta_contable',
        'numero_cuenta_bancaria',
        'banco',
        'tipo_de_recurso',
    ];
}
