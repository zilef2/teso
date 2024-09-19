<?php

namespace App\helpers;

class MyModels {
    /*
    area
    aqui falta papa
    */
    public static function CargosYModelos(){

        $nombresDeCargos = CargosModelos::CargosYModelos();
        $isSome = [];
        foreach ($nombresDeCargos as $key => $value) {
            $isSome[] = 'is' . $value;
        }

        return [
            'nombresDeCargos' => $nombresDeCargos,
            'isSome' => $isSome,
        ];
    }

    public static function getPermissionToNumber($permissions): int{
        $contador = 1;
        $nombresDeCargos = CargosModelos::CargosYModelos();
        $nombresDeCargos = $nombresDeCargos['nombresDeCargos'];
        foreach ($nombresDeCargos as $cargo) {
            if ($permissions === $cargo) return $contador;
            $contador++;
        }

        if ($permissions === 'admin') return 9000;
        if ($permissions === 'superadmin') return 10000;
        return 0;
    }
}
