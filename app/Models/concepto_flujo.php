<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class concepto_flujo extends Model
{
    use HasFactory;
    protected $fillable = [
        'cuenta',
        'valor',
        // todo: estaria mejor a un id?
    ];

}
