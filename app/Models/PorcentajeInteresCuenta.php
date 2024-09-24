<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PorcentajeInteresCuenta extends Model
{
    use HasFactory;
    protected $fillable = [
        'Mes',
        'valor',
        'cuenta_id',
    ];
}
